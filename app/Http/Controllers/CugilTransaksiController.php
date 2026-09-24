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

        return view('cugil.purchase-order', compact(
            'purchaseOrders', 'suppliers', 'barangs', 'totalPO', 'totalQty', 'totalNilai'
        ));
    }

    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'nomor_po' => 'required|string|max:30|unique:cugil_purchase_orders,nomor_po',
            'tanggal' => 'required|date',
            'kode_supplier' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)->first();
            $namaVendor = $sup ? $sup->nama_supplier : $request->kode_supplier;

            $totalQty = 0;
            $totalNilai = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty'];
                $hrg = (float) $item['harga_satuan'];
                $totalQty += $qty;
                $totalNilai += ($qty * $hrg);
            }

            $po = CugilPurchaseOrder::create([
                'nomor_po' => $request->nomor_po,
                'tanggal' => $request->tanggal,
                'kode_supplier' => $request->kode_supplier,
                'nama_vendor' => $namaVendor,
                'total_qty' => $totalQty,
                'total_nilai' => $totalNilai,
                'termin_payment' => $request->termin_payment ?? 'BELI PUTUS',
                'ongkos_angkut' => $request->ongkos_angkut ?? 0,
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
                    'kode_barang' => $item['kode_barang'] ?? null,
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $qty,
                    'satuan' => $item['satuan'] ?? 'Kg',
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        });

        return redirect()->route('cugil.po.index')->with('success', 'Purchase Order ' . $request->nomor_po . ' dengan ' . count($request->items) . ' item barang berhasil dibuat.');
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
            $sup = CugilSupplier::where('kode_supplier', $request->kode_supplier)->lockForUpdate()->first();
            $namaPemasok = $sup ? $sup->nama_supplier : $request->kode_supplier;

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

            $tagihan = max(0, $totalBruto - $totalRafaksi);
            $payment = (float) ($request->payment ?? 0);
            $sisa = max(0, $tagihan - $payment);
            $statusLunas = $sisa <= 0 && $tagihan > 0 ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : 'BELUM LUNAS');

            $raw = CugilRawMaterial::create([
                'nomor_po' => $request->nomor_po,
                'tanggal' => $request->tanggal,
                'kode_supplier' => $request->kode_supplier,
                'nama_pemasok' => $namaPemasok,
                'batch_produksi' => $request->batch_produksi,
                'total_qty' => $totalQty,
                'total_bruto' => $totalBruto,
                'total_rafaksi' => $totalRafaksi,
                'ongkos_angkut' => $request->ongkos_angkut ?? 0,
                'tagihan' => $tagihan,
                'payment' => $payment,
                'sisa_tagihan' => $sisa,
                'status_lunas' => $statusLunas,
                'truk' => $request->truk,
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

                CugilRawItem::create([
                    'cugil_raw_id' => $raw->id,
                    'kode_barang' => $item['kode_barang'] ?? null,
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $qty,
                    'satuan' => $item['satuan'] ?? 'Kg',
                    'harga_satuan' => $hrg,
                    'harga_total' => $qty * $hrg,
                    'diskon_rafaksi' => $raf,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                // Update stok masuk di master barang secara aman (Pessimistic Lock)
                if (!empty($item['kode_barang'])) {
                    $brg = CugilBarang::where('kode_barang', $item['kode_barang'])->lockForUpdate()->first();
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

        $msg = 'Penerimaan bahan baku (' . count($request->items) . ' SKU barang) berhasil dicatat.';
        if (!empty($request->nomor_po)) {
            $msg .= ' Status PO [' . $request->nomor_po . '] otomatis di-update menjadi DITERIMA (YA).';
        }

        return redirect()->route('cugil.raw.index')->with('success', $msg);
    }

    public function updateRawMaterialStatus(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $raw = CugilRawMaterial::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($request->filled('payment')) {
                $raw->payment = (float) $request->payment;
                $raw->sisa_tagihan = max(0, $raw->tagihan - $raw->payment);
                $raw->status_lunas = $raw->sisa_tagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS';
            }
            if ($request->filled('truk')) {
                $raw->truk = $request->truk;
            }
            if ($request->filled('status_truk_lunas')) {
                $raw->status_truk_lunas = $request->status_truk_lunas;
            }

            $raw->save();
        });

        return redirect()->route('cugil.raw.index')->with('success', 'Status pelunasan bahan baku berhasil diperbarui.');
    }

    // ─── SALES / PENJUALAN ───────────────────────────────────────────────────────
    public function sales(Request $request)
    {
        $query = CugilSale::with(['customer', 'items.barang']);

        if ($request->filled('customer')) {
            $query->where('kode_customer', $request->customer);
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

        return view('cugil.sales', compact(
            'sales', 'customers', 'barangs', 'totalTagihan', 'totalPayment', 'totalTerjual', 'belumLunasCount'
        ));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|string|max:50|unique:cugil_sales,id_penjualan',
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

        DB::transaction(function () use ($request, $fotoTimbanganPath) {
            $cust = CugilCustomer::where('kode_customer', $request->kode_customer)->lockForUpdate()->first();
            $namaBuyer = $cust ? $cust->nama_customer : $request->kode_customer;

            $totalQty = 0;
            $totalSak = 0;
            $totalBruto = 0;
            $totalDiskon = 0;

            foreach ($request->items as $item) {
                $qty = (float) $item['qty_terjual'];
                $sak = (int) ($item['jumlah_sak'] ?? 0);
                $hrg = (float) $item['harga_satuan'];
                $subtotalAwal = $qty * $hrg;
                $diskonP = (float) ($item['diskon_persen'] ?? 0);
                $diskonRp = (float) ($item['diskon_rupiah'] ?? ($subtotalAwal * $diskonP));

                $totalQty += $qty;
                $totalSak += $sak;
                $totalBruto += $subtotalAwal;
                $totalDiskon += $diskonRp;
            }

            $ongkir = (float) ($request->ongkos_angkut ?? 0);
            $tagihan = max(0, $totalBruto - $totalDiskon);
            $payment = (float) ($request->payment ?? 0);
            $sisaPiutang = max(0, $tagihan - $payment);
            $statusPelunasan = ($payment >= $tagihan && $tagihan > 0) ? 'LUNAS' : ($tagihan == 0 && $payment == 0 ? 'LUNAS' : 'BELUM LUNAS');

            $sale = CugilSale::create([
                'id_penjualan' => $request->id_penjualan,
                'tanggal' => $request->tanggal,
                'kode_customer' => $request->kode_customer,
                'nama_buyer' => $namaBuyer,
                'tanggal_kirim' => $request->tanggal_kirim ?? $request->tanggal,
                'sales' => $request->sales ?? 'ACH. CHUMAIDI',
                'broker' => $request->broker,
                'fee_makelar' => $request->fee_makelar ?? 0,
                'truk' => $request->truk,
                'status_broker_truk_lunas' => $request->status_broker_truk_lunas ?? 'LUNAS',
                'ongkos_kuli' => $request->ongkos_kuli ?? 0,
                'ongkos_angkut' => $ongkir,
                'down_payment' => $request->down_payment ?? 0,
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
                $qty = (float) $item['qty_terjual'];
                $sak = (int) ($item['jumlah_sak'] ?? 0);
                $hrg = (float) $item['harga_satuan'];
                $subtotalAwal = $qty * $hrg;
                $diskonP = (float) ($item['diskon_persen'] ?? 0);
                $diskonRp = (float) ($item['diskon_rupiah'] ?? ($subtotalAwal * $diskonP));
                $subtotalNet = $subtotalAwal - $diskonRp;

                CugilSaleItem::create([
                    'cugil_sale_id' => $sale->id,
                    'kode_barang' => $item['kode_barang'] ?? null,
                    'nama_barang' => $item['nama_barang'],
                    'qty_gudang' => $item['qty_gudang'] ?? $qty,
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
                if (!empty($item['kode_barang'])) {
                    $brg = CugilBarang::where('kode_barang', $item['kode_barang'])->lockForUpdate()->first();
                    if ($brg) {
                        $brg->barang_keluar += $qty;
                        $brg->stok_akhir = $brg->stok_awal + $brg->barang_masuk - $brg->barang_keluar;
                        $brg->save();
                    }
                }
            }
        });

        return redirect()->route('cugil.sales.index')->with('success', 'Penjualan ' . $request->id_penjualan . ' (' . count($request->items) . ' item produk cacahan) berhasil dicatat.');
    }

    public function updateSaleStatus(Request $request, $id)
    {
        $request->validate([
            'foto_timbangan' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:20480',
        ]);

        DB::transaction(function () use ($request, $id) {
            $sale = CugilSale::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($request->filled('payment')) {
                $sale->payment = (float) $request->payment;
                $sale->sisa_piutang = max(0, $sale->tagihan - $sale->payment);
                $sale->status_pelunasan = ($sale->payment >= $sale->tagihan && $sale->tagihan > 0) ? 'LUNAS' : 'BELUM LUNAS';
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

        return redirect()->route('cugil.sales.index')->with('success', 'Status pelunasan & data penjualan berhasil diperbarui.');
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

    public function printPurchaseOrder($id)
    {
        $po = CugilPurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        return view('cugil.print.purchase-order', compact('po', 'perusahaan'));
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

            return redirect()->route('cugil.sales.index')
                ->with('success', "Transaksi Penjualan [{$idPenjualan}] berhasil dihapus. Stok {$itemsCount} SKU barang telah otomatis di-rollback ke gudang.");
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

            $msg = "Penerimaan Bahan Baku dari [{$namaPemasok}] berhasil dihapus. Stok {$itemsCount} item telah di-rollback.";
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
}

