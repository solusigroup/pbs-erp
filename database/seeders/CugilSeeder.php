<?php

namespace Database\Seeders;

use App\Models\CugilBarang;
use App\Models\CugilCustomer;
use App\Models\CugilPurchaseOrder;
use App\Models\CugilPurchaseOrderItem;
use App\Models\CugilRawItem;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\CugilSaleItem;
use App\Models\CugilSupplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CugilSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('cugil_migrated_data.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("File cugil_migrated_data.json tidak ditemukan!");
            return;
        }

        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);

        if (!$data) {
            $this->command->error("Gagal membaca JSON data cugil!");
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables first
        CugilSaleItem::truncate();
        CugilSale::truncate();
        CugilRawItem::truncate();
        CugilRawMaterial::truncate();
        CugilPurchaseOrderItem::truncate();
        CugilPurchaseOrder::truncate();
        CugilBarang::truncate();
        CugilCustomer::truncate();
        CugilSupplier::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. SEED SUPPLIERS (56 suppliers)
        $this->command->info("1. Migrasi Master Supplier...");
        $now = now();
        $supplierRows = [];
        foreach ($data['suppliers'] as $sup) {
            $supplierRows[] = [
                'kode_supplier' => $sup['kode_supplier'],
                'nama_supplier' => $sup['nama_supplier'],
                'alamat'        => $sup['alamat'],
                'kota'          => $sup['kota'],
                'telepon'       => $sup['telepon'],
                'item_barang'   => $sup['item_barang'],
                'bank'          => $sup['bank'],
                'no_rekening'   => $sup['no_rekening'],
                'hutang'        => $sup['hutang'] ?? 0,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        foreach (array_chunk($supplierRows, 100) as $chunk) {
            DB::table('cugil_suppliers')->insert($chunk);
        }

        // 2. SEED CUSTOMERS (30 customers)
        $this->command->info("2. Migrasi Master Kastamer...");
        $customerRows = [];
        foreach ($data['customers'] as $cust) {
            $customerRows[] = [
                'kode_customer' => $cust['kode_customer'],
                'nama_customer' => $cust['nama_customer'],
                'alamat'        => $cust['alamat'],
                'kota'          => $cust['kota'],
                'telepon'       => $cust['telepon'],
                'item_barang'   => $cust['item_barang'],
                'bank'          => $cust['bank'],
                'no_rekening'   => $cust['no_rekening'],
                'piutang'       => $cust['piutang'] ?? 0,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        foreach (array_chunk($customerRows, 100) as $chunk) {
            DB::table('cugil_customers')->insert($chunk);
        }

        // 3. SEED BARANG (40 SKU items)
        $this->command->info("3. Migrasi Master Barang / SKU...");
        $barangRows = [];
        foreach ($data['barangs'] as $brg) {
            $barangRows[] = [
                'kode_barang'   => $brg['kode_barang'],
                'nama_barang'   => $brg['nama_barang'],
                'kategori'      => $brg['kategori'],
                'kode_kategori' => $brg['kode_kategori'],
                'satuan'        => $brg['satuan'] ?? 'Kg',
                'stok_awal'     => $brg['stok_awal'] ?? 0,
                'barang_masuk'  => $brg['barang_masuk'] ?? 0,
                'barang_keluar' => $brg['barang_keluar'] ?? 0,
                'stok_akhir'    => $brg['stok_akhir'] ?? 0,
                'harga_beli'    => $brg['harga_beli'] ?? 0,
                'harga_jual'    => $brg['harga_jual'] ?? 0,
                'foto_produk'   => $brg['foto_produk'],
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        foreach (array_chunk($barangRows, 100) as $chunk) {
            DB::table('cugil_barang')->insert($chunk);
        }

        // 4. SEED PURCHASE ORDERS (1,265 POs)
        $this->command->info("4. Migrasi CUGIL Purchase Orders (1.265 Transaksi)...");
        DB::transaction(function () use ($data, $now) {
            $poHeaders = [];
            $poItems = [];
            $poId = 1;

            foreach ($data['purchase_orders'] as $po) {
                $poHeaders[] = [
                    'id'             => $poId,
                    'nomor_po'       => $po['nomor_po'],
                    'tanggal'        => $po['tanggal'],
                    'kode_supplier'  => $po['kode_supplier'],
                    'nama_vendor'    => $po['nama_vendor'],
                    'total_qty'      => $po['qty'],
                    'total_nilai'    => $po['harga_total'],
                    'termin_payment' => $po['termin_payment'],
                    'ongkos_angkut'  => $po['ongkos_angkut'],
                    'batas_tanggal'  => $po['batas_tanggal'],
                    'petugas'        => $po['petugas'],
                    'keterangan'     => $po['keterangan'],
                    'status_terima'  => $po['status_terima'] ?? 'YA',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];

                $poItems[] = [
                    'cugil_po_id'  => $poId,
                    'kode_barang'  => $po['kode_barang'],
                    'nama_barang'  => $po['nama_barang'] ?? $po['kode_barang'] ?? 'Bahan Baku Cugil',
                    'qty'          => $po['qty'],
                    'satuan'       => $po['satuan'] ?? 'Kg',
                    'harga_satuan' => $po['harga_satuan'],
                    'harga_total'  => $po['harga_total'],
                    'catatan'      => $po['keterangan'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                $poId++;
            }

            foreach (array_chunk($poHeaders, 200) as $chunk) {
                DB::table('cugil_purchase_orders')->insert($chunk);
            }
            foreach (array_chunk($poItems, 200) as $chunk) {
                DB::table('cugil_po_items')->insert($chunk);
            }
        });

        // 5. SEED RAW MATERIALS (1,231 Deliveries)
        $this->command->info("5. Migrasi CUGIL Raw Materials / Penerimaan Bahan (1.231 Transaksi)...");
        DB::transaction(function () use ($data, $now) {
            $rawHeaders = [];
            $rawItems = [];
            $rawId = 1;

            foreach ($data['raw_materials'] as $raw) {
                $rawHeaders[] = [
                    'id'                => $rawId,
                    'nomor_po'          => $raw['nomor_po'],
                    'tanggal'           => $raw['tanggal'],
                    'kode_supplier'     => $raw['kode_supplier'],
                    'nama_pemasok'      => $raw['nama_pemasok'],
                    'batch_produksi'    => $raw['batch_produksi'],
                    'total_qty'         => $raw['qty'],
                    'total_bruto'       => $raw['harga_total'],
                    'total_rafaksi'     => $raw['diskon_rafaksi'],
                    'ongkos_angkut'     => $raw['ongkos_angkut'],
                    'tagihan'           => $raw['tagihan'],
                    'payment'           => $raw['payment'],
                    'sisa_tagihan'      => $raw['sisa_tagihan'],
                    'status_lunas'      => $raw['status_lunas'],
                    'truk'              => $raw['truk'],
                    'status_truk_lunas' => $raw['status_truk_lunas'],
                    'timbangan'         => $raw['timbangan'],
                    'invoiced'          => $raw['invoiced'],
                    'petugas'           => $raw['petugas'],
                    'remark'            => $raw['remark'],
                    'bulan'             => $raw['bulan'],
                    'tahun'             => $raw['tahun'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                $rawItems[] = [
                    'cugil_raw_id'   => $rawId,
                    'kode_barang'    => null,
                    'nama_barang'    => $raw['nama_barang'],
                    'qty'            => $raw['qty'],
                    'satuan'         => 'Kg',
                    'harga_satuan'   => $raw['harga_satuan'],
                    'harga_total'    => $raw['harga_total'],
                    'diskon_rafaksi' => $raw['diskon_rafaksi'],
                    'subtotal'       => $raw['tagihan'],
                    'catatan'        => $raw['remark'],
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];

                $rawId++;
            }

            foreach (array_chunk($rawHeaders, 200) as $chunk) {
                DB::table('cugil_raw_materials')->insert($chunk);
            }
            foreach (array_chunk($rawItems, 200) as $chunk) {
                DB::table('cugil_raw_items')->insert($chunk);
            }
        });

        // 6. SEED PENJUALAN (510 Sales Records)
        $this->command->info("6. Migrasi CUGIL Penjualan / Sales (510 Transaksi)...");
        DB::transaction(function () use ($data, $now) {
            $saleHeaders = [];
            $saleItems = [];
            $saleId = 1;

            foreach ($data['sales'] as $sale) {
                $saleHeaders[] = [
                    'id'                       => $saleId,
                    'id_penjualan'             => $sale['id_penjualan'],
                    'tanggal'                  => $sale['tanggal'],
                    'kode_customer'            => $sale['kode_customer'],
                    'nama_buyer'               => $sale['nama_buyer'],
                    'tanggal_kirim'            => $sale['tanggal_kirim'],
                    'sales'                    => $sale['sales'],
                    'broker'                   => $sale['broker'],
                    'fee_makelar'              => $sale['fee_makelar'],
                    'truk'                     => $sale['truk'],
                    'status_broker_truk_lunas' => $sale['status_broker_truk_lunas'],
                    'ongkos_kuli'              => $sale['ongkos_kuli'],
                    'ongkos_angkut'            => $sale['ongkos_angkut'],
                    'down_payment'             => $sale['down_payment'],
                    'total_qty'                => $sale['qty_terjual'],
                    'total_sak'                => $sale['jumlah_sak'],
                    'total_bruto'              => $sale['harga_total'],
                    'total_diskon'             => $sale['diskon_rupiah'],
                    'tagihan'                  => $sale['tagihan'],
                    'payment'                  => $sale['payment'],
                    'sisa_piutang'             => $sale['sisa_piutang'],
                    'status_pelunasan'         => $sale['status_pelunasan'],
                    'status_timbangan'         => $sale['status_timbangan'],
                    'invoiced'                 => $sale['invoiced'],
                    'status'                   => $sale['status'],
                    'remark'                   => $sale['remark'],
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ];

                $saleItems[] = [
                    'cugil_sale_id' => $saleId,
                    'kode_barang'   => null,
                    'nama_barang'   => $sale['nama_barang'],
                    'qty_gudang'    => $sale['qty_gudang'],
                    'qty_terjual'   => $sale['qty_terjual'],
                    'jumlah_sak'    => $sale['jumlah_sak'],
                    'harga_satuan'  => $sale['harga_satuan'],
                    'harga_total'   => $sale['harga_total'],
                    'diskon_persen' => $sale['diskon_persen'],
                    'diskon_rupiah' => $sale['diskon_rupiah'],
                    'subtotal'      => $sale['tagihan'],
                    'catatan'       => $sale['remark'],
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];

                $saleId++;
            }

            foreach (array_chunk($saleHeaders, 200) as $chunk) {
                DB::table('cugil_sales')->insert($chunk);
            }
            foreach (array_chunk($saleItems, 200) as $chunk) {
                DB::table('cugil_sale_items')->insert($chunk);
            }
        });

        // 7. SINKRONISASI SALDO HUTANG SUPPLIER & PIUTANG CUSTOMER
        $this->command->info("7. Sinkronisasi Saldo Hutang & Piutang Mitra...");
        $supplierHutangs = DB::table('cugil_raw_materials')
            ->select('kode_supplier', DB::raw('SUM(sisa_tagihan) as total_hutang'))
            ->groupBy('kode_supplier')
            ->get();

        foreach ($supplierHutangs as $sh) {
            DB::table('cugil_suppliers')
                ->where('kode_supplier', $sh->kode_supplier)
                ->update(['hutang' => $sh->total_hutang]);
        }

        $customerPiutangs = DB::table('cugil_sales')
            ->select('kode_customer', DB::raw('SUM(sisa_piutang) as total_piutang'))
            ->groupBy('kode_customer')
            ->get();

        foreach ($customerPiutangs as $cp) {
            DB::table('cugil_customers')
                ->where('kode_customer', $cp->kode_customer)
                ->update(['piutang' => $cp->total_piutang]);
        }

        $this->command->info("MIGRASI DATA EXCEL SELESAI DENGAN SUKSES!");
    }
}
