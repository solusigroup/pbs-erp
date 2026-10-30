<?php

namespace App\Http\Controllers;

use App\Models\CugilBarang;
use App\Models\CugilCustomer;
use App\Models\CugilPurchaseOrder;
use App\Models\CugilPurchaseOrderItem;
use App\Models\CugilRawItem;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\CugilSaleItem;
use App\Models\CugilSupplier;
use App\Models\Perusahaan;
use App\Services\ImageCompressionService;
use App\Services\JurnalAutoService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CugilTransaksiController extends Controller
{
    // ─── PURCHASE ORDERS ─────────────────────────────────────────────────────────
    public function purchaseOrders(Request $request)
    {
        $query = CugilPurchaseOrder::with(['supplier', 'items.barang', 'rawMaterials']);

        if ($request->filled('supplier')) {
            $query->where('kode_supplier', $request->supplier);
        }
        if ($request->filled('status')) {
            $query->where('status_terima', $request->status);
        }
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $purchaseOrders = $query->latest('tanggal')->paginate(20)->withQueryString();
        $suppliers = CugilSupplier::orderBy('nama_supplier')->get();
        $barangs = CugilBarang::orderBy('nama_barang')->get();
        $totalPO = CugilPurchaseOrder::count();
        $totalQty = CugilPurchaseOrder::sum('total_qty');
        $totalNilai = CugilPurchaseOrder::sum('total_nilai');
        $nextNomorPO = CugilPurchaseOrder::generateNextNomorPO();

        return view('cugil.purchase-order', compact(
            'purchaseOrders', 'suppliers', 'barangs', 'totalPO', 'totalQty', 'totalNilai', 'nextNomorPO'
        ));
    }

    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_supplier' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $nomorPO = null;

        DB::transaction(function () use ($request, &$nomorPO) {
            // Generate nomor PO otomatis di dalam transaksi (thread-safe)
            $nomorPO = CugilPurchaseOrder::generateNextNomorPO();

            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)
                ->orWhere('nama_supplier', $request->kode_supplier)
                ->first();
            $kodeSupplier = $sup ? $sup->kode_supplier : substr($request->kode_supplier, 0, 20);
            $namaVendor = $sup ? $sup->nama_supplier : substr($request->kode_supplier, 0, 100);

            $totalQty = 0;
            $totalNilai = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $totalQty += $qty;
                $totalNilai += ($qty * $hrg);
            }

            $ongkosAngkut = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;

            $po = CugilPurchaseOrder::create([
                'nomor_po' => $nomorPO,
                'tanggal' => $request->tanggal,
                'kode_supplier' => $kodeSupplier,
                'nama_vendor' => $namaVendor,
                'total_qty' => $totalQty,
                'total_nilai' => $totalNilai,
                'termin_payment' => $request->termin_payment ?? 'BELI PUTUS',
                'ongkos_angkut' => $ongkosAngkut,
                'batas_tanggal' => $request->batas_tanggal,
                'petugas' => $request->petugas ?? auth()->user()->name ?? 'WINARDI',
                'keterangan' => $request->keterangan,
                'status_terima' => $request->status_terima ?? 'Belum',
            ]);

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                CugilPurchaseOrderItem::create([
                    'cugil_po_id' => $po->id,
                    'kode_barang' => !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty' => $qty,
                    'satuan' => substr($item['satuan'] ?? 'Kg', 0, 10),
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        });

        // Auto-inisialisasi workflow checklist untuk PO baru
        $newPo = CugilPurchaseOrder::where('nomor_po', $nomorPO)->first();
        if ($newPo) {
            rescue(function () use ($newPo) {
                WorkflowService::initializeChecklist(
                    'cugil_po', CugilPurchaseOrder::class, $newPo->id, $newPo->nomor_po,
                    'po_created', auth()->user()->name ?? 'System'
                );
            });
        }

        // Auto-Generate Jurnal untuk Ongkos Angkut PO jika ada
        if ($newPo) {
            rescue(function () use ($newPo) {
                $jurnalService = app(\App\Services\JurnalAutoService::class);
                $jurnalService->createJurnalOngkosAngkutPO($newPo);
            });
        }

        return redirect()->route('cugil.po.index')->with('success', 'Purchase Order ' . $nomorPO . ' dengan ' . count($request->items) . ' item barang berhasil dibuat.');
    }

    public function updatePurchaseOrder(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_supplier' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $po = CugilPurchaseOrder::where('id', $id)->lockForUpdate()->firstOrFail();

            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)
                ->orWhere('nama_supplier', $request->kode_supplier)
                ->first();
            $kodeSupplier = $sup ? $sup->kode_supplier : substr($request->kode_supplier, 0, 20);
            $namaVendor = $sup ? $sup->nama_supplier : substr($request->kode_supplier, 0, 100);

            $totalQty = 0;
            $totalNilai = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $totalQty += $qty;
                $totalNilai += ($qty * $hrg);
            }

            $ongkosAngkut = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;

            // Note: nomor_po is immutable / not updated
            $po->update([
                'tanggal' => $request->tanggal,
                'kode_supplier' => $kodeSupplier,
                'nama_vendor' => $namaVendor,
                'total_qty' => $totalQty,
                'total_nilai' => $totalNilai,
                'termin_payment' => $request->termin_payment ?? 'BELI PUTUS',
                'ongkos_angkut' => $ongkosAngkut,
                'batas_tanggal' => $request->batas_tanggal,
                'petugas' => $request->petugas ?? auth()->user()->name ?? 'WINARDI',
                'keterangan' => $request->keterangan,
                'status_terima' => $request->status_terima ?? $po->status_terima,
            ]);

            $po->items()->delete();

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                CugilPurchaseOrderItem::create([
                    'cugil_po_id' => $po->id,
                    'kode_barang' => !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty' => $qty,
                    'satuan' => substr($item['satuan'] ?? 'Kg', 0, 10),
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        });

        $po = CugilPurchaseOrder::findOrFail($id);
        
        // Auto-Generate Jurnal untuk Ongkos Angkut PO jika ada
        rescue(function () use ($po) {
            $jurnalService = app(\App\Services\JurnalAutoService::class);
            $jurnalService->createJurnalOngkosAngkutPO($po);
        });
        
        return redirect()->route('cugil.po.index')->with('success', 'Purchase Order ' . $po->nomor_po . ' berhasil diperbarui.');
    }

    // ─── RAW MATERIALS (TERIMA BAHAN BAKU) ──────────────────────────────────────
    public function rawMaterials(Request $request)
    {
        $query = CugilRawMaterial::with(['supplier', 'items.barang', 'purchaseOrder']);

        if ($request->filled('supplier')) {
            $query->where('kode_supplier', $request->supplier);
        }
        if ($request->filled('status')) {
            $query->where('status_lunas', $request->status);
        }
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $rawMaterials = $query->latest('tanggal')->paginate(20)->withQueryString();
        $suppliers = CugilSupplier::orderBy('nama_supplier')->get();
        $barangs = CugilBarang::orderBy('nama_barang')->get();
        
        // PO yang siap/menunggu diterima di CUGIL RAW
        $pendingPOs = CugilPurchaseOrder::with(['items', 'supplier'])
            ->where('status_terima', '!=', 'YA')
            ->latest('tanggal')
            ->get();

        $allPOs = CugilPurchaseOrder::with(['items', 'supplier'])
            ->latest('tanggal')
            ->take(50)
            ->get();

        $totalTagihan = CugilRawMaterial::sum('tagihan');
        $totalPayment = CugilRawMaterial::sum('payment');
        $totalSisa = CugilRawMaterial::sum('sisa_tagihan');
        $belumLunasCount = CugilRawMaterial::where('status_lunas', 'BELUM LUNAS')->count();

        return view('cugil.raw-material', compact(
            'rawMaterials', 'suppliers', 'barangs', 'pendingPOs', 'allPOs',
            'totalTagihan', 'totalPayment', 'totalSisa', 'belumLunasCount'
        ));
    }

    public function storeRawMaterial(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_supplier' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)
                ->orWhere('nama_supplier', $request->kode_supplier)
                ->lockForUpdate()->first();
            $kodeSupplier = $sup ? $sup->kode_supplier : substr($request->kode_supplier, 0, 20);
            $namaPemasok = $sup ? $sup->nama_supplier : substr($request->kode_supplier, 0, 100);

            $totalQty = 0;
            $totalBruto = 0;
            $totalRafaksi = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $raf = (float) ($item['diskon_rafaksi'] ?? 0);
                $totalQty += $qty;
                $totalBruto += ($qty * $hrg);
                $totalRafaksi += $raf;
            }

            $ongkir = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;
            $tagihan = max(0, $totalBruto - $totalRafaksi);
            $payment = !empty($request->payment) ? (float) $request->payment : 0;
            $sisa = max(0, $tagihan - $payment);
            $statusLunas = $sisa <= 0 && $tagihan > 0 ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : 'BELUM LUNAS');

            $raw = CugilRawMaterial::create([
                'nomor_po' => $request->nomor_po,
                'tanggal' => $request->tanggal,
                'kode_supplier' => $kodeSupplier,
                'nama_pemasok' => $namaPemasok,
                'batch_produksi' => $request->batch_produksi,
                'total_qty' => $totalQty,
                'total_bruto' => $totalBruto,
                'total_rafaksi' => $totalRafaksi,
                'ongkos_angkut' => $ongkir,
                'tagihan' => $tagihan,
                'payment' => $payment,
                'sisa_tagihan' => $sisa,
                'status_lunas' => $statusLunas,
                'truk' => !empty($request->truk) ? substr($request->truk, 0, 50) : null,
                'status_truk_lunas' => $request->status_truk_lunas ?? 'BELUM',
                'timbangan' => $request->timbangan,
                'invoiced' => 'BELUM',
                'petugas' => $request->petugas ?? auth()->user()->name ?? 'WINARDI',
                'remark' => $request->remark,
                'bulan' => (int) date('n', strtotime($request->tanggal)),
                'tahun' => (int) date('Y', strtotime($request->tanggal)),
            ]);

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $raf = (float) ($item['diskon_rafaksi'] ?? 0);
                $subtotal = ($qty * $hrg) - $raf;

                $kodeBarang = !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null;
                if (!$kodeBarang && !empty($item['nama_barang'])) {
                    $brgFound = CugilBarang::where('nama_barang', $item['nama_barang'])->first();
                    if ($brgFound) {
                        $kodeBarang = $brgFound->kode_barang;
                    }
                }

                CugilRawItem::create([
                    'cugil_raw_id' => $raw->id,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty' => $qty,
                    'satuan' => substr($item['satuan'] ?? 'Kg', 0, 10),
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'diskon_rafaksi' => $raf,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Update stok masuk di master barang secara aman (Pessimistic Lock)
                if (!empty($kodeBarang)) {
                    $brg = CugilBarang::where('kode_barang', $kodeBarang)->lockForUpdate()->first();
                    if ($brg) {
                        $brg->barang_masuk += $qty;
                        $brg->stok_akhir = $brg->stok_awal + $brg->barang_masuk - $brg->barang_keluar;
                        $brg->save();
                    }
                }
            }

            // OTOMATIS UPDATE STATUS PO MENJADI 'YA' (DITERIMA) DENGAN PESSIMISTIC LOCK
            if (!empty($request->nomor_po)) {
                $po = CugilPurchaseOrder::where('nomor_po', $request->nomor_po)->lockForUpdate()->first();
                if ($po) {
                    $po->status_terima = 'YA';
                    $po->save();
                }
            }
        });

        // Cari record Raw Material yang baru dibuat
        $newRaw = CugilRawMaterial::where('tanggal', $request->tanggal)
            ->where('kode_supplier', substr($request->kode_supplier, 0, 20))
            ->latest()->first();

        // Generate Jurnal DRAFT otomatis secara aman
        if ($newRaw) {
            rescue(function () use ($newRaw) {
                $jurnalService = app(\App\Services\JurnalAutoService::class);
                $jurnalService->createJurnalPembelian($newRaw);
            });
        }

        // Auto-inisialisasi workflow checklist untuk Raw Material secara aman
        if ($newRaw) {
            rescue(function () use ($newRaw, $request) {
                $refCode = $newRaw->nomor_po ?? 'RAW-' . $newRaw->id;
                WorkflowService::initializeChecklist(
                    'cugil_raw', CugilRawMaterial::class, $newRaw->id, $refCode,
                    'raw_received', auth()->user()->name ?? 'System'
                );

                // Jika PO terkait, auto-complete langkah "Barang Diterima" di workflow PO
                if (!empty($request->nomor_po)) {
                    $po = CugilPurchaseOrder::where('nomor_po', $request->nomor_po)->first();
                    if ($po) {
                        WorkflowService::completeStep('cugil_po', $po->id, 'po_received', auth()->user()->name ?? 'System', 'Otomatis: barang diterima via RAW Material');
                    }
                }
            });
        }

        $msg = 'Penerimaan bahan baku (' . count($request->items) . ' SKU barang) berhasil dicatat.';
        if (!empty($request->nomor_po)) {
            $msg .= ' Status PO [' . $request->nomor_po . '] otomatis di-update menjadi DITERIMA (YA).';
        }
        $msg .= ' Jurnal DRAFT pembelian telah dibuat.';

        return redirect()->route('cugil.raw.index')->with('success', $msg);
    }

    public function updateRawMaterial(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_supplier' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $raw = CugilRawMaterial::with('items')->where('id', $id)->lockForUpdate()->firstOrFail();

            // 1. Rollback old stock additions
            foreach ($raw->items as $item) {
                if (!empty($item->kode_barang)) {
                    $barang = CugilBarang::where('kode_barang', $item->kode_barang)->lockForUpdate()->first();
                    if ($barang) {
                        $barang->barang_masuk = max(0, $barang->barang_masuk - (float) $item->qty);
                        $barang->stok_akhir = $barang->stok_awal + $barang->barang_masuk - $barang->barang_keluar;
                        $barang->save();
                    }
                }
            }

            // 2. Delete old items
            $raw->items()->delete();

            // 3. Supplier lookup with length guards
            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)
                ->orWhere('nama_supplier', $request->kode_supplier)
                ->first();
            $kodeSupplier = $sup ? $sup->kode_supplier : substr($request->kode_supplier, 0, 20);
            $namaPemasok = $sup ? $sup->nama_supplier : substr($request->kode_supplier, 0, 100);

            // 4. Calculate totals
            $totalQty = 0;
            $totalBruto = 0;
            $totalRafaksi = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $raf = (float) ($item['diskon_rafaksi'] ?? 0);
                $totalQty += $qty;
                $totalBruto += ($qty * $hrg);
                $totalRafaksi += $raf;
            }

            $ongkir = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;
            $tagihan = max(0, $totalBruto - $totalRafaksi);
            $payment = isset($request->payment) && $request->payment !== '' ? (float) $request->payment : (float) $raw->payment;
            $sisa = max(0, $tagihan - $payment);
            $statusLunas = $sisa <= 0 && $tagihan > 0 ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : 'BELUM LUNAS');

            // 5. Update header (nomor_po is immutable / not updated)
            $raw->update([
                'tanggal' => $request->tanggal,
                'kode_supplier' => $kodeSupplier,
                'nama_pemasok' => $namaPemasok,
                'batch_produksi' => $request->batch_produksi ?? $raw->batch_produksi,
                'total_qty' => $totalQty,
                'total_bruto' => $totalBruto,
                'total_rafaksi' => $totalRafaksi,
                'ongkos_angkut' => $ongkir,
                'tagihan' => $tagihan,
                'payment' => $payment,
                'sisa_tagihan' => $sisa,
                'status_lunas' => $statusLunas,
                'truk' => !empty($request->truk) ? substr($request->truk, 0, 50) : $raw->truk,
                'status_truk_lunas' => $request->status_truk_lunas ?? $raw->status_truk_lunas,
                'timbangan' => $request->timbangan ?? $raw->timbangan,
                'petugas' => $request->petugas ?? $raw->petugas,
                'remark' => $request->remark ?? $raw->remark,
                'bulan' => (int) date('n', strtotime($request->tanggal)),
                'tahun' => (int) date('Y', strtotime($request->tanggal)),
            ]);

            // 6. Create new items and update stock
            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $raf = (float) ($item['diskon_rafaksi'] ?? 0);
                $subtotal = ($qty * $hrg) - $raf;

                $kodeBarang = !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null;
                if (!$kodeBarang && !empty($item['nama_barang'])) {
                    $brgFound = CugilBarang::where('nama_barang', $item['nama_barang'])->first();
                    if ($brgFound) {
                        $kodeBarang = $brgFound->kode_barang;
                    }
                }

                CugilRawItem::create([
                    'cugil_raw_id' => $raw->id,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty' => $qty,
                    'satuan' => substr($item['satuan'] ?? 'Kg', 0, 10),
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'diskon_rafaksi' => $raf,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                if (!empty($kodeBarang)) {
                    $brg = CugilBarang::where('kode_barang', $kodeBarang)->lockForUpdate()->first();
                    if ($brg) {
                        $brg->barang_masuk += $qty;
                        $brg->stok_akhir = $brg->stok_awal + $brg->barang_masuk - $brg->barang_keluar;
                        $brg->save();
                    }
                }
            }
        });

        $raw = CugilRawMaterial::findOrFail($id);

        // Regenerate Jurnal DRAFT otomatis secara aman
        rescue(function () use ($raw) {
            $jurnalService = app(\App\Services\JurnalAutoService::class);
            $jurnalService->createJurnalPembelian($raw);
        });

        return redirect()->route('cugil.raw.index')->with('success', 'Data penerimaan bahan baku ' . ($raw->nomor_po ?? 'RAW-'.$raw->id) . ' berhasil diperbarui.');
    }

    public function updateRawMaterialStatus(Request $request, $id)
    {
        $autoJurnalMsg = '';
        DB::transaction(function () use ($request, $id, &$autoJurnalMsg) {
            $raw = CugilRawMaterial::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($request->filled('payment')) {
                $oldPayment = (float) $raw->payment;
                $newPayment = (float) $request->payment;
                $deltaPayment = $newPayment - $oldPayment;

                $raw->payment = $newPayment;
                $raw->sisa_tagihan = max(0, round($raw->tagihan - $raw->payment, 2));
                $raw->status_lunas = ($raw->sisa_tagihan <= 0) ? 'LUNAS' : ($raw->payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS');

                if ($deltaPayment > 0) {
                    rescue(function () use ($raw, $deltaPayment, &$autoJurnalMsg) {
                        $jurnalService = app(\App\Services\JurnalAutoService::class);
                        if ($jurnalService->createJurnalPelunasanHutang($raw, $deltaPayment)) {
                            $autoJurnalMsg = ' Jurnal DRAFT pelunasan hutang telah dibuat.';
                        }
                    });
                }
            }
            if ($request->filled('truk')) {
                $raw->truk = $request->truk;
            }
            if ($request->filled('status_truk_lunas')) {
                $raw->status_truk_lunas = $request->status_truk_lunas;
            }

            $raw->save();
        });

        return redirect()->route('cugil.raw.index')->with('success', 'Status pelunasan bahan baku berhasil diperbarui.' . $autoJurnalMsg);
    }

    // ─── SALES / PENJUALAN ───────────────────────────────────────────────────────
    public function sales(Request $request)
    {
        $query = CugilSale::with(['customer', 'items.barang']);

        if ($request->filled('customer')) {
            $custSearch = $request->customer;
            $query->where(function ($q) use ($custSearch) {
                $q->where('kode_customer', $custSearch)
                  ->orWhere('kode_customer', 'LIKE', '%' . $custSearch . '%')
                  ->orWhere('nama_buyer', 'LIKE', '%' . $custSearch . '%')
                  ->orWhereHas('customer', function ($cq) use ($custSearch) {
                      $cq->where('nama_customer', 'LIKE', '%' . $custSearch . '%')
                         ->orWhere('kode_customer', 'LIKE', '%' . $custSearch . '%');
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status_pelunasan', $request->status);
        }
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $sales = $query->latest('tanggal')->paginate(20)->withQueryString();
        $customers = CugilCustomer::orderBy('nama_customer')->get();
        $barangs = CugilBarang::orderBy('nama_barang')->get();
        $totalTagihan = CugilSale::sum('tagihan');
        $totalPayment = CugilSale::sum('payment');
        $totalTerjual = CugilSale::sum('total_qty');
        $belumLunasCount = CugilSale::where('status_pelunasan', 'BELUM LUNAS')->count();
        $nextIdPenjualan = CugilSale::generateNextIdPenjualan();

        return view('cugil.sales', compact(
            'sales', 'customers', 'barangs', 'totalTagihan', 'totalPayment', 'totalTerjual', 'belumLunasCount', 'nextIdPenjualan'
        ));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'nullable|string|max:50',
            'tanggal' => 'required|date',
            'kode_customer' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty_terjual' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'foto_timbangan' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:20480',
        ]);

        $fotoTimbanganPath = null;
        if ($request->hasFile('foto_timbangan')) {
            $fotoTimbanganPath = ImageCompressionService::compressAndStore($request->file('foto_timbangan'), 'timbangan_sales', 1400, 80);
        }

        // Generate or ensure unique ID Penjualan
        $idPenjualan = !empty($request->id_penjualan) ? trim($request->id_penjualan) : CugilSale::generateNextIdPenjualan();
        if (CugilSale::where('id_penjualan', $idPenjualan)->exists()) {
            $idPenjualan = CugilSale::generateNextIdPenjualan();
        }

        DB::transaction(function () use ($request, $fotoTimbanganPath, $idPenjualan) {
            $cust = CugilCustomer::where('kode_customer', $request->kode_customer)
                ->orWhere('nama_customer', $request->kode_customer)
                ->lockForUpdate()->first();
            $kodeCustomer = $cust ? $cust->kode_customer : substr($request->kode_customer, 0, 20);
            $namaBuyer = $cust ? $cust->nama_customer : substr($request->kode_customer, 0, 100);

            $totalQty = 0;
            $totalSak = 0;
            $totalBruto = 0;
            $totalDiskon = 0;

            foreach ($request->items as $item) {
                $qty = (float) ($item['qty_terjual'] ?? 0);
                $sak = !empty($item['jumlah_sak']) ? (int) $item['jumlah_sak'] : 0;
                $hrg = (float) ($item['harga_satuan'] ?? 0);
                $subtotalAwal = $qty * $hrg;
                $diskonP = !empty($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
                $diskonRp = !empty($item['diskon_rupiah']) ? (float) $item['diskon_rupiah'] : ($subtotalAwal * $diskonP);

                $totalQty += $qty;
                $totalSak += $sak;
                $totalBruto += $subtotalAwal;
                $totalDiskon += $diskonRp;
            }

            $ongkir = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;
            $tagihan = max(0, $totalBruto - $totalDiskon);
            $payment = !empty($request->payment) ? (float) $request->payment : 0;
            $feeMakelar = !empty($request->fee_makelar) ? (float) $request->fee_makelar : 0;
            $ongkosKuli = !empty($request->ongkos_kuli) ? (float) $request->ongkos_kuli : 0;
            $downPayment = !empty($request->down_payment) ? (float) $request->down_payment : 0;
            $sisaPiutang = max(0, $tagihan - $payment);
            $statusPelunasan = ($payment >= $tagihan && $tagihan > 0) ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : ($payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS'));

            $sale = CugilSale::create([
                'id_penjualan' => $idPenjualan,
                'tanggal' => $request->tanggal,
                'kode_customer' => $kodeCustomer,
                'nama_buyer' => $namaBuyer,
                'tanggal_kirim' => $request->tanggal_kirim ?? $request->tanggal,
                'sales' => !empty($request->sales) ? substr($request->sales, 0, 50) : 'ACH. CHUMAIDI',
                'broker' => !empty($request->broker) ? substr($request->broker, 0, 50) : null,
                'fee_makelar' => $feeMakelar,
                'truk' => !empty($request->truk) ? substr($request->truk, 0, 50) : null,
                'status_broker_truk_lunas' => !empty($request->status_broker_truk_lunas) ? substr($request->status_broker_truk_lunas, 0, 20) : 'LUNAS',
                'ongkos_kuli' => $ongkosKuli,
                'ongkos_angkut' => $ongkir,
                'down_payment' => $downPayment,
                'total_qty' => $totalQty,
                'total_sak' => $totalSak,
                'total_bruto' => $totalBruto,
                'total_diskon' => $totalDiskon,
                'tagihan' => $tagihan,
                'payment' => $payment,
                'sisa_piutang' => $sisaPiutang,
                'status_pelunasan' => $statusPelunasan,
                'status_timbangan' => $request->status_timbangan ?? ($fotoTimbanganPath ? 'SUDAH' : 'BELUM'),
                'foto_timbangan' => $fotoTimbanganPath,
                'invoiced' => 'SUDAH',
                'status' => 'TERJUAL',
                'remark' => $request->remark,
            ]);

            foreach ($request->items as $item) {
                $qty = (float) ($item['qty_terjual'] ?? 0);
                $qtyGudang = isset($item['qty_gudang']) && $item['qty_gudang'] !== '' ? (float) $item['qty_gudang'] : $qty;
                $sak = !empty($item['jumlah_sak']) ? (int) $item['jumlah_sak'] : 0;
                $hrg = (float) ($item['harga_satuan'] ?? 0);
                $subtotalAwal = $qty * $hrg;
                $diskonP = !empty($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
                $diskonRp = !empty($item['diskon_rupiah']) ? (float) $item['diskon_rupiah'] : ($subtotalAwal * $diskonP);
                $subtotalNet = $subtotalAwal - $diskonRp;

                $kodeBarang = !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null;
                if (!$kodeBarang && !empty($item['nama_barang'])) {
                    $brgFound = CugilBarang::where('nama_barang', $item['nama_barang'])->first();
                    if ($brgFound) {
                        $kodeBarang = $brgFound->kode_barang;
                    }
                }

                CugilSaleItem::create([
                    'cugil_sale_id' => $sale->id,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty_gudang' => $qtyGudang,
                    'qty_terjual' => $qty,
                    'jumlah_sak' => $sak,
                    'harga_satuan' => $hrg,
                    'harga_total' => $subtotalAwal,
                    'diskon_persen' => $diskonP,
                    'diskon_rupiah' => $diskonRp,
                    'subtotal' => $subtotalNet,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Update stok keluar di master barang secara aman (Pessimistic Lock)
                if (!empty($kodeBarang)) {
                    $brg = CugilBarang::where('kode_barang', $kodeBarang)->lockForUpdate()->first();
                    if ($brg) {
                        $brg->barang_keluar += $qty;
                        $brg->stok_akhir = $brg->stok_awal + $brg->barang_masuk - $brg->barang_keluar;
                        $brg->save();
                    }
                }
            }
        });

        $newSale = CugilSale::where('id_penjualan', $idPenjualan)->first();

        // Auto-Generate Jurnal DRAFT Penjualan secara aman
        if ($newSale) {
            rescue(function () use ($newSale) {
                $jurnalService = app(\App\Services\JurnalAutoService::class);
                $jurnalService->createJurnalPenjualan($newSale);
            });
        }

        // Auto-inisialisasi workflow checklist untuk Sale baru secara aman
        if ($newSale) {
            rescue(function () use ($newSale) {
                WorkflowService::initializeChecklist(
                    'cugil_sales', CugilSale::class, $newSale->id, $newSale->id_penjualan,
                    'sales_order_created', auth()->user()->name ?? 'System'
                );
                // Auto-complete timbangan jika foto sudah ada
                if ($newSale->foto_timbangan) {
                    WorkflowService::completeStep('cugil_sales', $newSale->id, 'sales_timbangan', auth()->user()->name ?? 'System', 'Foto timbangan dilampirkan saat pembuatan SO');
                }
            });
        }

        return redirect()->route('cugil.sales.index')->with('success', 'Penjualan ' . $idPenjualan . ' (' . count($request->items) . ' item produk cacahan) berhasil dicatat. Jurnal DRAFT penjualan telah dibuat.');
    }

    public function updateSale(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_customer' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty_terjual' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'foto_timbangan' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:20480',
        ]);

        DB::transaction(function () use ($request, $id) {
            $sale = CugilSale::with('items')->where('id', $id)->lockForUpdate()->firstOrFail();

            // 1. Rollback old stock out
            foreach ($sale->items as $item) {
                if (!empty($item->kode_barang)) {
                    $barang = CugilBarang::where('kode_barang', $item->kode_barang)->lockForUpdate()->first();
                    if ($barang) {
                        $barang->barang_keluar = max(0, $barang->barang_keluar - (float) $item->qty_terjual);
                        $barang->stok_akhir = $barang->stok_awal + $barang->barang_masuk - $barang->barang_keluar;
                        $barang->save();
                    }
                }
            }

            // 2. Delete old items
            $sale->items()->delete();

            // 3. Customer lookup with length guards
            $cust = CugilCustomer::where('kode_customer', $request->kode_customer)
                ->orWhere('nama_customer', $request->kode_customer)
                ->first();
            $kodeCustomer = $cust ? $cust->kode_customer : substr($request->kode_customer, 0, 20);
            $namaBuyer = $cust ? $cust->nama_customer : substr($request->kode_customer, 0, 100);

            // 4. Handle photo upload
            $fotoTimbanganPath = $sale->foto_timbangan;
            if ($request->hasFile('foto_timbangan')) {
                if ($sale->foto_timbangan) {
                    ImageCompressionService::deleteImage($sale->foto_timbangan);
                }
                $fotoTimbanganPath = ImageCompressionService::compressAndStore($request->file('foto_timbangan'), 'timbangan_sales', 1400, 80);
            }

            // 5. Calculate totals
            $totalQty = 0;
            $totalSak = 0;
            $totalBruto = 0;
            $totalDiskon = 0;

            foreach ($request->items as $item) {
                $qty = (float) ($item['qty_terjual'] ?? 0);
                $sak = !empty($item['jumlah_sak']) ? (int) $item['jumlah_sak'] : 0;
                $hrg = (float) ($item['harga_satuan'] ?? 0);
                $subtotalAwal = $qty * $hrg;
                $diskonP = !empty($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
                $diskonRp = !empty($item['diskon_rupiah']) ? (float) $item['diskon_rupiah'] : ($subtotalAwal * $diskonP);

                $totalQty += $qty;
                $totalSak += $sak;
                $totalBruto += $subtotalAwal;
                $totalDiskon += $diskonRp;
            }

            $ongkir = !empty($request->ongkos_angkut) ? (float) $request->ongkos_angkut : 0;
            $tagihan = max(0, $totalBruto - $totalDiskon);
            $payment = isset($request->payment) && $request->payment !== '' ? (float) $request->payment : (float) $sale->payment;
            $feeMakelar = isset($request->fee_makelar) && $request->fee_makelar !== '' ? (float) $request->fee_makelar : (float) $sale->fee_makelar;
            $ongkosKuli = isset($request->ongkos_kuli) && $request->ongkos_kuli !== '' ? (float) $request->ongkos_kuli : (float) $sale->ongkos_kuli;
            $downPayment = isset($request->down_payment) && $request->down_payment !== '' ? (float) $request->down_payment : (float) $sale->down_payment;
            $sisaPiutang = max(0, $tagihan - $payment);
            $statusPelunasan = ($payment >= $tagihan && $tagihan > 0) ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : ($payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS'));

            // 6. Update Sale header (id_penjualan remains read-only / unchanged)
            $sale->update([
                'tanggal' => $request->tanggal,
                'kode_customer' => $kodeCustomer,
                'nama_buyer' => $namaBuyer,
                'tanggal_kirim' => $request->tanggal_kirim ?? $request->tanggal,
                'sales' => !empty($request->sales) ? substr($request->sales, 0, 50) : $sale->sales,
                'broker' => !empty($request->broker) ? substr($request->broker, 0, 50) : $sale->broker,
                'fee_makelar' => $feeMakelar,
                'truk' => !empty($request->truk) ? substr($request->truk, 0, 50) : $sale->truk,
                'status_broker_truk_lunas' => !empty($request->status_broker_truk_lunas) ? substr($request->status_broker_truk_lunas, 0, 20) : $sale->status_broker_truk_lunas,
                'ongkos_kuli' => $ongkosKuli,
                'ongkos_angkut' => $ongkir,
                'down_payment' => $downPayment,
                'total_qty' => $totalQty,
                'total_sak' => $totalSak,
                'total_bruto' => $totalBruto,
                'total_diskon' => $totalDiskon,
                'tagihan' => $tagihan,
                'payment' => $payment,
                'sisa_piutang' => $sisaPiutang,
                'status_pelunasan' => $statusPelunasan,
                'status_timbangan' => $fotoTimbanganPath ? 'SUDAH' : 'BELUM',
                'foto_timbangan' => $fotoTimbanganPath,
                'remark' => $request->remark ?? $sale->remark,
            ]);

            // 7. Create new items and apply stock out
            foreach ($request->items as $item) {
                $qty = (float) ($item['qty_terjual'] ?? 0);
                $qtyGudang = isset($item['qty_gudang']) && $item['qty_gudang'] !== '' ? (float) $item['qty_gudang'] : $qty;
                $sak = !empty($item['jumlah_sak']) ? (int) $item['jumlah_sak'] : 0;
                $hrg = (float) ($item['harga_satuan'] ?? 0);
                $subtotalAwal = $qty * $hrg;
                $diskonP = !empty($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
                $diskonRp = !empty($item['diskon_rupiah']) ? (float) $item['diskon_rupiah'] : ($subtotalAwal * $diskonP);
                $subtotalNet = $subtotalAwal - $diskonRp;

                $kodeBarang = !empty($item['kode_barang']) ? substr($item['kode_barang'], 0, 20) : null;
                if (!$kodeBarang && !empty($item['nama_barang'])) {
                    $brgFound = CugilBarang::where('nama_barang', $item['nama_barang'])->first();
                    if ($brgFound) {
                        $kodeBarang = $brgFound->kode_barang;
                    }
                }

                CugilSaleItem::create([
                    'cugil_sale_id' => $sale->id,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => substr($item['nama_barang'], 0, 100),
                    'qty_gudang' => $qtyGudang,
                    'qty_terjual' => $qty,
                    'jumlah_sak' => $sak,
                    'harga_satuan' => $hrg,
                    'harga_total' => $subtotalAwal,
                    'diskon_persen' => $diskonP,
                    'diskon_rupiah' => $diskonRp,
                    'subtotal' => $subtotalNet,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                if (!empty($kodeBarang)) {
                    $brg = CugilBarang::where('kode_barang', $kodeBarang)->lockForUpdate()->first();
                    if ($brg) {
                        $brg->barang_keluar += $qty;
                        $brg->stok_akhir = $brg->stok_awal + $brg->barang_masuk - $brg->barang_keluar;
                        $brg->save();
                    }
                }
            }
        });

        $sale = CugilSale::findOrFail($id);

        // Regenerate auto journal for sales secara aman
        rescue(function () use ($sale) {
            $jurnalService = app(\App\Services\JurnalAutoService::class);
            $jurnalService->createJurnalPenjualan($sale);
        });

        return redirect()->route('cugil.sales.index')->with('success', 'Transaksi Penjualan ' . $sale->id_penjualan . ' berhasil diperbarui.');
    }

    public function updateSaleStatus(Request $request, $id)
    {
        $request->validate([
            'foto_timbangan' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:20480',
        ]);

        $autoJurnalMsg = '';
        DB::transaction(function () use ($request, $id, &$autoJurnalMsg) {
            $sale = CugilSale::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($request->filled('payment')) {
                $oldPayment = (float) $sale->payment;
                $newPayment = (float) $request->payment;
                $deltaPayment = $newPayment - $oldPayment;

                $sale->payment = $newPayment;
                $sale->sisa_piutang = max(0, round($sale->tagihan - $sale->payment, 2));
                $sale->status_pelunasan = ($sale->sisa_piutang <= 0) ? 'LUNAS' : ($sale->payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS');

                if ($deltaPayment > 0) {
                    rescue(function () use ($sale, $deltaPayment, &$autoJurnalMsg) {
                        $jurnalService = app(\App\Services\JurnalAutoService::class);
                        if ($jurnalService->createJurnalPelunasanPiutang($sale, $deltaPayment)) {
                            $autoJurnalMsg = ' Jurnal DRAFT pelunasan piutang telah dibuat.';
                        }
                    });
                }
            }
            if ($request->filled('broker')) {
                $sale->broker = $request->broker;
            }
            if ($request->filled('truk')) {
                $sale->truk = $request->truk;
            }

            if ($request->hasFile('foto_timbangan')) {
                if ($sale->foto_timbangan) {
                    ImageCompressionService::deleteImage($sale->foto_timbangan);
                }
                $sale->foto_timbangan = ImageCompressionService::compressAndStore($request->file('foto_timbangan'), 'timbangan_sales', 1400, 80);
                $sale->status_timbangan = 'SUDAH';
            }

            $sale->save();
        });

        return redirect()->route('cugil.sales.index')->with('success', 'Status pelunasan & data penjualan berhasil diperbarui.' . $autoJurnalMsg);
    }

    public function uploadFotoTimbangan(Request $request, $id)
    {
        $request->validate([
            'foto_timbangan' => 'required|image|mimes:jpeg,png,jpg,webp,heic|max:20480',
        ]);

        $sale = CugilSale::findOrFail($id);

        if ($sale->foto_timbangan) {
            ImageCompressionService::deleteImage($sale->foto_timbangan);
        }

        $path = ImageCompressionService::compressAndStore($request->file('foto_timbangan'), 'timbangan_sales', 1400, 80);

        $sale->foto_timbangan = $path;
        $sale->status_timbangan = 'SUDAH';
        $sale->save();

        WorkflowService::completeStep('cugil_sales', $sale->id, 'sales_timbangan', auth()->user()->name ?? 'System', 'Foto slip timbangan diunggah');

        return redirect()->route('cugil.sales.index')->with('success', 'Foto slip timbangan (' . $sale->id_penjualan . ') berhasil diupload & otomatis dikompresi.');
    }

    public function deleteFotoTimbangan($id)
    {
        $sale = CugilSale::findOrFail($id);

        if ($sale->foto_timbangan) {
            ImageCompressionService::deleteImage($sale->foto_timbangan);
            $sale->foto_timbangan = null;
            $sale->status_timbangan = 'BELUM';
            $sale->save();
        }

        return redirect()->route('cugil.sales.index')->with('success', 'Foto timbangan (' . $sale->id_penjualan . ') berhasil dihapus.');
    }

    // ─── PRINTABLE COMMERCIAL DOCUMENTS ─────────────────────────────────────────

    public function showSale($id)
    {
        $sale = CugilSale::with(['customer', 'items.barang'])->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($sale);
        }

        return redirect()->route('cugil.sales.so', $id);
    }

    public function printSalesOrder($id)
    {
        $sale = CugilSale::with(['customer', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.sales-order', compact('sale', 'perusahaan'));
    }

    public function printSuratJalan($id)
    {
        $sale = CugilSale::with(['customer', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.surat-jalan', compact('sale', 'perusahaan'));
    }

    public function printInvoice($id)
    {
        $sale = CugilSale::with(['customer', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.invoice', compact('sale', 'perusahaan'));
    }

    public function printFakturPajak($id)
    {
        $sale = CugilSale::with(['customer', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.faktur-pajak', compact('sale', 'perusahaan'));
    }

    public function showPurchaseOrder($id)
    {
        $po = CugilPurchaseOrder::with(['supplier', 'items.barang', 'rawMaterials'])->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($po);
        }

        return redirect()->route('cugil.po.print', $id);
    }

    public function printPurchaseOrder($id)
    {
        $po = CugilPurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.purchase-order', compact('po', 'perusahaan'));
    }

    public function showRawMaterial($id)
    {
        $raw = CugilRawMaterial::with(['supplier', 'items.barang', 'purchaseOrder'])->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($raw);
        }

        return redirect()->route('cugil.raw.tanda-terima', $id);
    }

    public function printTandaTerimaRaw($id)
    {
        $raw = CugilRawMaterial::with(['supplier', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.tanda-terima-raw', compact('raw', 'perusahaan'));
    }

    // ─── TRANSAKSI ROLLBACK & DELETE HANDLERS ────────────────────────────────────

    /**
     * Hapus Penjualan (CUGIL Sales) dengan Otomatis Rollback Stok Barang & Lampiran
     */
    public function destroySale($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = CugilSale::with('items')->where('id', $id)->lockForUpdate()->firstOrFail();
            $idPenjualan = $sale->id_penjualan;
            $itemsCount = $sale->items->count();

            // 1. Rollback Stok Keluar di Master Barang
            foreach ($sale->items as $item) {
                if (!empty($item->kode_barang)) {
                    $barang = CugilBarang::where('kode_barang', $item->kode_barang)->lockForUpdate()->first();
                    if ($barang) {
                        // Kembalikan stok yang sebelumnya keluar
                        $barang->barang_keluar = max(0, $barang->barang_keluar - (float) $item->qty_terjual);
                        $barang->stok_akhir = $barang->stok_awal + $barang->barang_masuk - $barang->barang_keluar;
                        $barang->save();
                    }
                }
            }

            // 2. Hapus file fisik foto slip timbangan dari storage jika ada
            if ($sale->foto_timbangan) {
                ImageCompressionService::deleteImage($sale->foto_timbangan);
            }

            // 3. Hapus item rincian penjualan
            $sale->items()->delete();

            // 4. Hapus record transaksi penjualan
            $sale->delete();

            // 5. Reverse jurnal otomatis
            $jurnalService = new JurnalAutoService();
            $reversed = $jurnalService->reverseByReference('AUTO-PENJUALAN-SALE-' . $id);
            $reverseMsg = $reversed > 0 ? " {$reversed} jurnal otomatis terkait telah di-reverse dan dihapus." : "";

            return redirect()->route('cugil.sales.index')
                ->with('success', "Transaksi Penjualan [{$idPenjualan}] berhasil dihapus. Stok {$itemsCount} SKU barang telah otomatis di-rollback ke gudang." . $reverseMsg);
        });
    }

    /**
     * Hapus Penerimaan Bahan Baku (CUGIL RAW) dengan Otomatis Rollback Stok & Status PO
     */
    public function destroyRawMaterial($id)
    {
        return DB::transaction(function () use ($id) {
            $raw = CugilRawMaterial::with('items')->where('id', $id)->lockForUpdate()->firstOrFail();
            $nomorPO = $raw->nomor_po;
            $itemsCount = $raw->items->count();
            $namaPemasok = $raw->nama_pemasok ?? $raw->kode_supplier;

            // 1. Rollback Stok Masuk di Master Barang
            foreach ($raw->items as $item) {
                if (!empty($item->kode_barang)) {
                    $barang = CugilBarang::where('kode_barang', $item->kode_barang)->lockForUpdate()->first();
                    if ($barang) {
                        // Kurangi stok yang sebelumnya masuk
                        $barang->barang_masuk = max(0, $barang->barang_masuk - (float) $item->qty);
                        $barang->stok_akhir = $barang->stok_awal + $barang->barang_masuk - $barang->barang_keluar;
                        $barang->save();
                    }
                }
            }

            // 2. Rollback Status PO Terkait menjadi 'Belum' jika tidak ada penerimaan RAW lain dengan nomor PO ini
            if (!empty($nomorPO)) {
                $otherRaw = CugilRawMaterial::where('nomor_po', $nomorPO)->where('id', '!=', $id)->exists();
                if (!$otherRaw) {
                    $po = CugilPurchaseOrder::where('nomor_po', $nomorPO)->lockForUpdate()->first();
                    if ($po) {
                        $po->status_terima = 'Belum';
                        $po->save();
                    }
                }
            }

            // 3. Hapus item rincian bahan masuk
            $raw->items()->delete();

            // 4. Hapus data penerimaan bahan baku
            $raw->delete();

            // 5. Reverse jurnal otomatis
            $jurnalService = new JurnalAutoService();
            $reversed = $jurnalService->reverseByReference('AUTO-PEMBELIAN-RAW-' . $id);
            $reverseMsg = $reversed > 0 ? " {$reversed} jurnal otomatis terkait telah di-reverse dan dihapus." : "";

            $msg = "Penerimaan Bahan Baku dari [{$namaPemasok}] berhasil dihapus. Stok {$itemsCount} item telah di-rollback." . $reverseMsg;
            if (!empty($nomorPO) && isset($po)) {
                $msg .= " Status PO [{$nomorPO}] telah dikembalikan menjadi 'Belum Diterima'.";
            }

            return redirect()->route('cugil.raw.index')->with('success', $msg);
        });
    }

    /**
     * Hapus Purchase Order (CUGIL PO)
     */
    public function destroyPurchaseOrder($id)
    {
        return DB::transaction(function () use ($id) {
            $po = CugilPurchaseOrder::with(['items', 'rawMaterials'])->where('id', $id)->lockForUpdate()->firstOrFail();
            $nomorPO = $po->nomor_po;

            // Validasi: Cek apakah PO sudah memiliki penerimaan RAW aktif
            if ($po->rawMaterials()->count() > 0) {
                return back()->with('error', "PO [{$nomorPO}] tidak dapat dihapus karena sudah ada penerimaan bahan baku (RAW) tercatat. Hapus transaksi penerimaan bahan bakunya terlebih dahulu.");
            }

            // Hapus items PO
            $po->items()->delete();
            $po->delete();

            return redirect()->route('cugil.po.index')->with('success', "Purchase Order [{$nomorPO}] berhasil dihapus.");
        });
    }

    /**
     * Otomatisasi Sinkronisasi & Perbaikan Anomali Status Pelunasan & Selisih Tagihan
     */
    public function syncAnomaliData()
    {
        return DB::transaction(function () {
            $salesMathCount = 0;
            $salesStatusCount = 0;
            $rawMathCount = 0;
            $rawStatusCount = 0;

            // 1. Penjualan
            $sales = CugilSale::all();
            foreach ($sales as $sale) {
                $dirty = false;
                $expectedSisa = max(0, round($sale->tagihan - $sale->payment, 2));
                if (abs($sale->sisa_piutang - $expectedSisa) > 0.01) {
                    $sale->sisa_piutang = $expectedSisa;
                    $dirty = true;
                    $salesMathCount++;
                }

                $expectedStatus = ($sale->sisa_piutang <= 0) ? 'LUNAS' : ($sale->payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS');
                if ($sale->status_pelunasan !== $expectedStatus && $sale->status_pelunasan !== 'RETUR') {
                    $sale->status_pelunasan = $expectedStatus;
                    $dirty = true;
                    $salesStatusCount++;
                }

                if ($dirty) {
                    $sale->save();
                }
            }

            // 2. Pembelian Bahan Baku
            $rawMaterials = CugilRawMaterial::all();
            foreach ($rawMaterials as $raw) {
                $dirty = false;
                $expectedSisa = max(0, round($raw->tagihan - $raw->payment, 2));
                if (abs($raw->sisa_tagihan - $expectedSisa) > 0.01) {
                    $raw->sisa_tagihan = $expectedSisa;
                    $dirty = true;
                    $rawMathCount++;
                }

                $expectedStatus = ($raw->sisa_tagihan <= 0) ? 'LUNAS' : ($raw->payment > 0 ? 'SEBAGIAN' : 'BELUM LUNAS');
                if ($raw->status_lunas !== $expectedStatus) {
                    $raw->status_lunas = $expectedStatus;
                    $dirty = true;
                    $rawStatusCount++;
                }

                if ($dirty) {
                    $raw->save();
                }
            }

            // 3. Sinkronisasi Saldo Master Customer
            $customers = CugilCustomer::all();
            foreach ($customers as $cust) {
                $totalSisaPiutang = CugilSale::where('kode_customer', $cust->kode_customer)->sum('sisa_piutang');
                if (abs($cust->piutang - $totalSisaPiutang) > 0.01) {
                    $cust->piutang = $totalSisaPiutang;
                    $cust->save();
                }
            }

            // 4. Sinkronisasi Saldo Master Supplier
            $suppliers = CugilSupplier::all();
            foreach ($suppliers as $sup) {
                $totalSisaHutang = CugilRawMaterial::where('kode_supplier', $sup->kode_supplier)->sum('sisa_tagihan');
                if (abs($sup->hutang - $totalSisaHutang) > 0.01) {
                    $sup->hutang = $totalSisaHutang;
                    $sup->save();
                }
            }

            return back()->with('success', "Sinkronisasi berhasil! Penjualan: {$salesStatusCount} status & {$salesMathCount} sisa piutang diselaraskan. Pembelian: {$rawStatusCount} status & {$rawMathCount} sisa hutang diselaraskan.");
        });
    }

    /**
     * Penyesuaian Faktual: Nolkan Seluruh Sisa Piutang Penjualan Selain MUSTOFA (CUST-12) & Set Status LUNAS
     */
    public function lunaskanSemuaPiutang()
    {
        return DB::transaction(function () {
            // 1. Update seluruh penjualan selain Mustofa (CUST-12): set payment = tagihan, sisa_piutang = 0, status_pelunasan = LUNAS (kecuali RETUR)
            $updatedSales = 0;
            $sales = CugilSale::where('kode_customer', '!=', 'CUST-12')
                ->where('sisa_piutang', '>', 0)
                ->get();

            foreach ($sales as $sale) {
                $sale->payment = $sale->tagihan;
                $sale->sisa_piutang = 0;
                if ($sale->status_pelunasan !== 'RETUR') {
                    $sale->status_pelunasan = 'LUNAS';
                }
                $sale->save();
                $updatedSales++;
            }

            // 2. Nolkan piutang di master data customer selain CUST-12 (Mustofa)
            CugilCustomer::where('kode_customer', '!=', 'CUST-12')->update(['piutang' => 0]);

            // 3. Pastikan saldo piutang Mustofa tersinkronisasi presisi sesuai sisa transaksi Mustofa
            $totalSisaMustofa = CugilSale::where('kode_customer', 'CUST-12')->sum('sisa_piutang');
            $custMustofa = CugilCustomer::where('kode_customer', 'CUST-12')->first();
            if ($custMustofa) {
                $custMustofa->piutang = $totalSisaMustofa;
                $custMustofa->save();
            }

            return back()->with('success', "Penyesuaian faktual berhasil! {$updatedSales} transaksi penjualan selain Mustofa telah diset LUNAS (Sisa = Rp 0). Sisa piutang hanya tercatat untuk MUSTOFA (CUST-12) sebesar Rp " . number_format($totalSisaMustofa, 0, ',', '.') . ".");
        });
    }
}

