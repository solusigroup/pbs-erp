<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * JurnalAutoService
 * 
 * Service otomatis untuk membuat jurnal akuntansi dari transaksi operasional CUGIL.
 * Jurnal dibuat dengan status is_posted = false (DRAFT), sehingga perlu approval
 * sebelum masuk ke General Ledger (update saldo_berjalan akun).
 * 
 * COA Default Kas: 1-1210 (sesuai ketetapan perusahaan)
 */
class JurnalAutoService
{
    /**
     * Default cash account code untuk transaksi otomatis
     */
    const DEFAULT_KAS_CODE = '1-1210';

    /**
     * Prefix untuk nomor transaksi otomatis
     */
    const PREFIX_PEMBELIAN = 'JU-AUTO-BLI';
    const PREFIX_PENJUALAN = 'JU-AUTO-JUL';
    const PREFIX_PELUNASAN_HUTANG = 'JU-AUTO-PLH';
    const PREFIX_PELUNASAN_PIUTANG = 'JU-AUTO-PLP';

    // ─── PEMBELIAN BAHAN BAKU ────────────────────────────────────────────────────

    /**
     * Buat jurnal draft dari penerimaan bahan baku.
     * 
     * Dr. 1-1500 Persediaan Bahan Baku ... tagihan
     *     Cr. 1-1210 Kas/Bank ............. payment (jika ada DP)
     *     Cr. 2-1100 Hutang Usaha ......... sisa_tagihan
     */
    public function createJurnalPembelian(CugilRawMaterial $rawMaterial): ?JurnalUmum
    {
        // Anti duplikasi: cek apakah sudah ada jurnal dengan referensi yang sama
        $refKey = 'AUTO-PEMBELIAN-RAW-' . $rawMaterial->id;
        if ($this->jurnalExists($refKey)) {
            return null;
        }

        $tagihan = (float) $rawMaterial->tagihan;
        if ($tagihan <= 0) {
            return null;
        }

        $payment = (float) $rawMaterial->payment;
        $sisaTagihan = (float) $rawMaterial->sisa_tagihan;
        $tanggal = $rawMaterial->tanggal;
        $namaSupplier = $rawMaterial->nama_pemasok ?? $rawMaterial->kode_supplier;
        $nomorPO = $rawMaterial->nomor_po ?? '-';

        $noTransaksi = $this->generateNoTransaksi(self::PREFIX_PEMBELIAN, $tanggal);

        $details = [];

        // Debit: Persediaan Bahan Baku
        $details[] = [
            'kode_akun' => '1-1500',
            'keterangan_baris' => "Pembelian bahan baku dari {$namaSupplier} (PO: {$nomorPO})",
            'debit' => $tagihan,
            'kredit' => 0,
        ];

        // Kredit: Kas/Bank (jika ada pembayaran langsung / DP)
        if ($payment > 0) {
            $details[] = [
                'kode_akun' => self::DEFAULT_KAS_CODE,
                'keterangan_baris' => "Pembayaran kas pembelian bahan baku - {$namaSupplier}",
                'debit' => 0,
                'kredit' => $payment,
            ];
        }

        // Kredit: Hutang Usaha (jika ada sisa tagihan)
        if ($sisaTagihan > 0) {
            $details[] = [
                'kode_akun' => '2-1100',
                'keterangan_baris' => "Hutang usaha pembelian bahan baku - {$namaSupplier} (PO: {$nomorPO})",
                'debit' => 0,
                'kredit' => $sisaTagihan,
            ];
        }

        $deskripsi = "[AUTO] Pembelian bahan baku - {$namaSupplier} (PO: {$nomorPO})";

        return $this->createDraftJurnal($noTransaksi, $tanggal, 'Umum', $deskripsi, $refKey, $tagihan, $tagihan, $details);
    }

    // ─── PENJUALAN BARANG JADI ───────────────────────────────────────────────────

    /**
     * Buat jurnal draft dari penjualan barang jadi.
     * 
     * Dr. 1-1400 Piutang Usaha ........... sisa_piutang
     * Dr. 1-1210 Kas/Bank ................ payment (jika ada DP)
     *     Cr. 4-1000 Pendapatan Penjualan . tagihan
     */
    public function createJurnalPenjualan(CugilSale $sale): ?JurnalUmum
    {
        // Anti duplikasi
        $refKey = 'AUTO-PENJUALAN-SALE-' . $sale->id;
        if ($this->jurnalExists($refKey)) {
            return null;
        }

        $tagihan = (float) $sale->tagihan;
        if ($tagihan <= 0) {
            return null;
        }

        $payment = (float) $sale->payment;
        $sisaPiutang = (float) $sale->sisa_piutang;
        $tanggal = $sale->tanggal;
        $namaCustomer = $sale->nama_buyer ?? $sale->kode_customer;
        $nomorInvoice = $sale->id_penjualan;

        $noTransaksi = $this->generateNoTransaksi(self::PREFIX_PENJUALAN, $tanggal);

        $details = [];

        // Debit: Piutang Usaha (jika ada sisa piutang)
        if ($sisaPiutang > 0) {
            $details[] = [
                'kode_akun' => '1-1400',
                'keterangan_baris' => "Piutang penjualan ke {$namaCustomer} (INV: {$nomorInvoice})",
                'debit' => $sisaPiutang,
                'kredit' => 0,
            ];
        }

        // Debit: Kas/Bank (jika ada pembayaran langsung / DP)
        if ($payment > 0) {
            $details[] = [
                'kode_akun' => self::DEFAULT_KAS_CODE,
                'keterangan_baris' => "Penerimaan kas penjualan - {$namaCustomer} (INV: {$nomorInvoice})",
                'debit' => $payment,
                'kredit' => 0,
            ];
        }

        // Kredit: Pendapatan Penjualan
        $details[] = [
            'kode_akun' => '4-1000',
            'keterangan_baris' => "Pendapatan penjualan ke {$namaCustomer} (INV: {$nomorInvoice})",
            'debit' => 0,
            'kredit' => $tagihan,
        ];

        $deskripsi = "[AUTO] Penjualan barang jadi - {$namaCustomer} (INV: {$nomorInvoice})";

        return $this->createDraftJurnal($noTransaksi, $tanggal, 'Umum', $deskripsi, $refKey, $tagihan, $tagihan, $details);
    }

    // ─── PELUNASAN HUTANG SUPPLIER ───────────────────────────────────────────────

    /**
     * Buat jurnal draft dari pelunasan hutang ke supplier.
     * 
     * Dr. 2-1100 Hutang Usaha ............ jumlah_bayar
     *     Cr. 1-1210 Kas/Bank ............ jumlah_bayar
     */
    public function createJurnalPelunasanHutang(CugilRawMaterial $rawMaterial, float $jumlahBayar): ?JurnalUmum
    {
        if ($jumlahBayar <= 0) {
            return null;
        }

        // Anti duplikasi menggunakan timestamp agar pelunasan bertahap tetap bisa di-create
        $refKey = 'AUTO-PELUNASAN-HUTANG-RAW-' . $rawMaterial->id . '-' . now()->format('YmdHis');

        $tanggal = Carbon::now();
        $namaSupplier = $rawMaterial->nama_pemasok ?? $rawMaterial->kode_supplier;
        $nomorPO = $rawMaterial->nomor_po ?? '-';

        $noTransaksi = $this->generateNoTransaksi(self::PREFIX_PELUNASAN_HUTANG, $tanggal);

        $details = [
            [
                'kode_akun' => '2-1100',
                'keterangan_baris' => "Pelunasan hutang ke {$namaSupplier} (PO: {$nomorPO})",
                'debit' => $jumlahBayar,
                'kredit' => 0,
            ],
            [
                'kode_akun' => self::DEFAULT_KAS_CODE,
                'keterangan_baris' => "Pembayaran kas pelunasan hutang - {$namaSupplier}",
                'debit' => 0,
                'kredit' => $jumlahBayar,
            ],
        ];

        $deskripsi = "[AUTO] Pelunasan hutang - {$namaSupplier} (PO: {$nomorPO})";

        return $this->createDraftJurnal($noTransaksi, $tanggal, 'Umum', $deskripsi, $refKey, $jumlahBayar, $jumlahBayar, $details);
    }

    // ─── PELUNASAN PIUTANG CUSTOMER ──────────────────────────────────────────────

    /**
     * Buat jurnal draft dari pelunasan piutang customer.
     * 
     * Dr. 1-1210 Kas/Bank ................ jumlah_terima
     *     Cr. 1-1400 Piutang Usaha ....... jumlah_terima
     */
    public function createJurnalPelunasanPiutang(CugilSale $sale, float $jumlahTerima): ?JurnalUmum
    {
        if ($jumlahTerima <= 0) {
            return null;
        }

        $refKey = 'AUTO-PELUNASAN-PIUTANG-SALE-' . $sale->id . '-' . now()->format('YmdHis');

        $tanggal = Carbon::now();
        $namaCustomer = $sale->nama_buyer ?? $sale->kode_customer;
        $nomorInvoice = $sale->id_penjualan;

        $noTransaksi = $this->generateNoTransaksi(self::PREFIX_PELUNASAN_PIUTANG, $tanggal);

        $details = [
            [
                'kode_akun' => self::DEFAULT_KAS_CODE,
                'keterangan_baris' => "Penerimaan kas pelunasan piutang - {$namaCustomer} (INV: {$nomorInvoice})",
                'debit' => $jumlahTerima,
                'kredit' => 0,
            ],
            [
                'kode_akun' => '1-1400',
                'keterangan_baris' => "Pelunasan piutang dari {$namaCustomer} (INV: {$nomorInvoice})",
                'debit' => 0,
                'kredit' => $jumlahTerima,
            ],
        ];

        $deskripsi = "[AUTO] Pelunasan piutang - {$namaCustomer} (INV: {$nomorInvoice})";

        return $this->createDraftJurnal($noTransaksi, $tanggal, 'Umum', $deskripsi, $refKey, $jumlahTerima, $jumlahTerima, $details);
    }

    // ─── APPROVAL (POST TO GL) ───────────────────────────────────────────────────

    /**
     * Approve (post) jurnal draft ke General Ledger.
     * Update saldo_berjalan setiap akun yang terlibat.
     */
    public function approveJurnal(JurnalUmum $jurnal): bool
    {
        if ($jurnal->is_posted) {
            return false; // Sudah diposting
        }

        return DB::transaction(function () use ($jurnal) {
            $jurnal = JurnalUmum::where('id_jurnal', $jurnal->id_jurnal)->lockForUpdate()->firstOrFail();

            foreach ($jurnal->details as $detail) {
                $akun = Akun::where('kode_akun', $detail->kode_akun)->lockForUpdate()->first();
                if ($akun) {
                    if ($akun->saldo_normal === 'Debit') {
                        $akun->saldo_berjalan += ((float) $detail->debit - (float) $detail->kredit);
                    } else {
                        $akun->saldo_berjalan += ((float) $detail->kredit - (float) $detail->debit);
                    }
                    $akun->save();
                }
            }

            $jurnal->is_posted = true;
            $jurnal->save();

            return true;
        });
    }

    /**
     * Bulk approve: posting beberapa jurnal draft sekaligus.
     */
    public function bulkApprove(array $jurnalIds): array
    {
        $approved = 0;
        $failed = 0;

        foreach ($jurnalIds as $id) {
            $jurnal = JurnalUmum::with('details')->find($id);
            if ($jurnal && !$jurnal->is_posted) {
                if ($this->approveJurnal($jurnal)) {
                    $approved++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        return ['approved' => $approved, 'failed' => $failed];
    }

    // ─── UNAPPROVE (ROLLBACK FROM GL) ────────────────────────────────────────────

    /**
     * Unapprove jurnal: rollback saldo_berjalan dan set is_posted = false.
     * Digunakan sebelum edit jurnal yang sudah di-approve.
     */
    public function unapproveJurnal(JurnalUmum $jurnal): bool
    {
        if (!$jurnal->is_posted) {
            return false; // Belum diposting, tidak perlu rollback
        }

        return DB::transaction(function () use ($jurnal) {
            $jurnal = JurnalUmum::with('details')->where('id_jurnal', $jurnal->id_jurnal)->lockForUpdate()->firstOrFail();

            foreach ($jurnal->details as $detail) {
                $akun = Akun::where('kode_akun', $detail->kode_akun)->lockForUpdate()->first();
                if ($akun) {
                    // Kebalikan dari approve
                    if ($akun->saldo_normal === 'Debit') {
                        $akun->saldo_berjalan -= ((float) $detail->debit - (float) $detail->kredit);
                    } else {
                        $akun->saldo_berjalan -= ((float) $detail->kredit - (float) $detail->debit);
                    }
                    $akun->save();
                }
            }

            $jurnal->is_posted = false;
            $jurnal->save();

            return true;
        });
    }

    // ─── UPDATE JURNAL (WITH ROLLBACK) ───────────────────────────────────────────

    /**
     * Update jurnal yang sudah ada.
     * Jika sudah is_posted = true, rollback dulu, update detail, lalu re-approve.
     * Jika masih draft, langsung update detail.
     */
    public function updateJurnal(JurnalUmum $jurnal, array $data): JurnalUmum
    {
        return DB::transaction(function () use ($jurnal, $data) {
            $wasPosted = $jurnal->is_posted;

            // Jika sudah posted, rollback dulu
            if ($wasPosted) {
                $this->unapproveJurnal($jurnal);
            }

            // Lock dan refresh
            $jurnal = JurnalUmum::where('id_jurnal', $jurnal->id_jurnal)->lockForUpdate()->firstOrFail();

            // Update header
            if (isset($data['tanggal'])) $jurnal->tanggal = $data['tanggal'];
            if (isset($data['deskripsi'])) $jurnal->deskripsi = $data['deskripsi'];

            // Update detail jika disediakan
            if (isset($data['details']) && is_array($data['details'])) {
                // Hapus detail lama
                $jurnal->details()->delete();

                $totalDebit = 0;
                $totalKredit = 0;

                // Create detail baru
                foreach ($data['details'] as $item) {
                    $debit = (float) ($item['debit'] ?? 0);
                    $kredit = (float) ($item['kredit'] ?? 0);

                    if ($debit > 0 || $kredit > 0) {
                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $item['kode_akun'],
                            'keterangan_baris' => $item['keterangan_baris'] ?? $jurnal->deskripsi,
                            'debit' => $debit,
                            'kredit' => $kredit,
                        ]);

                        $totalDebit += $debit;
                        $totalKredit += $kredit;
                    }
                }

                $jurnal->total_debit = $totalDebit;
                $jurnal->total_kredit = $totalKredit;
            }

            $jurnal->save();

            // Jika sebelumnya sudah posted, re-approve
            if ($wasPosted) {
                $jurnal->refresh();
                $jurnal->load('details');
                $this->approveJurnal($jurnal);
            }

            return $jurnal->fresh(['details.akun']);
        });
    }

    // ─── REVERSE / DELETE ────────────────────────────────────────────────────────

    /**
     * Reverse dan hapus jurnal auto-generated.
     * Jika sudah posted, rollback saldo dulu. Lalu hapus detail dan header.
     */
    public function reverseAndDeleteJurnal(JurnalUmum $jurnal): bool
    {
        return DB::transaction(function () use ($jurnal) {
            // Jika sudah posted, rollback saldo
            if ($jurnal->is_posted) {
                $this->unapproveJurnal($jurnal);
            }

            // Hapus detail dan header
            $jurnal->details()->delete();
            $jurnal->delete();

            return true;
        });
    }

    /**
     * Reverse semua jurnal yang terkait dengan sumber referensi tertentu.
     * Dipanggil saat transaksi operasional dihapus.
     */
    public function reverseByReference(string $refKey): int
    {
        $jurnals = JurnalUmum::where('sumber_referensi', $refKey)->get();
        $count = 0;

        foreach ($jurnals as $jurnal) {
            if ($this->reverseAndDeleteJurnal($jurnal)) {
                $count++;
            }
        }

        return $count;
    }

    // ─── UTILITIES ───────────────────────────────────────────────────────────────

    /**
     * Generate nomor transaksi unik.
     * Format: {PREFIX}-{YYYYMMDD}-{SEQUENCE}
     */
    private function generateNoTransaksi(string $prefix, $tanggal): string
    {
        $dateStr = Carbon::parse($tanggal)->format('Ymd');
        $baseNo = $prefix . '-' . $dateStr;

        // Cari sequence terakhir untuk tanggal ini
        $lastJurnal = JurnalUmum::where('no_transaksi', 'like', $baseNo . '-%')
            ->orderBy('no_transaksi', 'desc')
            ->first();

        if ($lastJurnal) {
            $lastSeq = (int) substr($lastJurnal->no_transaksi, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $baseNo . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah jurnal dengan sumber_referensi sudah ada (anti-duplikasi).
     */
    private function jurnalExists(string $refKey): bool
    {
        return JurnalUmum::where('sumber_referensi', $refKey)->exists();
    }

    /**
     * Buat jurnal draft (is_posted = false).
     */
    private function createDraftJurnal(
        string $noTransaksi,
        $tanggal,
        string $tipeJurnal,
        string $deskripsi,
        string $sumberReferensi,
        float $totalDebit,
        float $totalKredit,
        array $details
    ): JurnalUmum {
        $jurnal = JurnalUmum::create([
            'no_transaksi' => $noTransaksi,
            'tanggal' => $tanggal,
            'tipe_jurnal' => $tipeJurnal,
            'deskripsi' => $deskripsi,
            'sumber_referensi' => $sumberReferensi,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'created_by' => 'SYSTEM-AUTO',
            'is_posted' => false, // DRAFT — butuh approval
        ]);

        foreach ($details as $item) {
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $item['kode_akun'],
                'keterangan_baris' => $item['keterangan_baris'],
                'debit' => $item['debit'],
                'kredit' => $item['kredit'],
            ]);
        }

        return $jurnal->load('details.akun');
    }

    /**
     * Cek apakah jurnal dibuat oleh system auto-generate.
     */
    public static function isAutoGenerated(JurnalUmum $jurnal): bool
    {
        return $jurnal->created_by === 'SYSTEM-AUTO'
            || str_starts_with($jurnal->no_transaksi, 'JU-AUTO-');
    }
}
