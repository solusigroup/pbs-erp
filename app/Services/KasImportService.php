<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasImportService
{
    /**
     * Parse uploaded file (.xlsx, .xls, .csv) into array of rows
     */
    public function parseFile(string $filePath, string $extension): array
    {
        $extension = strtolower($extension);

        // 1. Try PhpSpreadsheet if available and file is Excel
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory') && in_array($extension, ['xlsx', 'xls', 'csv'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (\Throwable $e) {
                Log::warning('PhpSpreadsheet parse failed, falling back to native parser: ' . $e->getMessage());
            }
        }

        // 2. If CSV or fallback
        if ($extension === 'csv' || $extension === 'txt') {
            return $this->parseCsv($filePath);
        }

        // 3. If XLSX fallback via ZipArchive & XML
        if ($extension === 'xlsx') {
            return $this->parseXlsxNative($filePath);
        }

        return [];
    }

    /**
     * Native CSV Parser with auto-detect delimiter
     */
    private function parseCsv(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        // Detect delimiter (; or , or \t)
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Native XLSX Parser using ZipArchive and OpenXML
     */
    private function parseXlsxNative(string $filePath): array
    {
        $rows = [];
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // Read Shared Strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    $sharedStrings[] = (string)($si->t ?? $si->r->t ?? '');
                }
            }
        }

        // Read Sheet1
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml) {
            $xml = simplexml_load_string($sheetXml);
            if ($xml && isset($xml->sheetData->row)) {
                foreach ($xml->sheetData->row as $r) {
                    $row = [];
                    foreach ($r->c as $c) {
                        $cellValue = (string)$c->v;
                        $type = (string)$c['t'];

                        if ($type === 's' && isset($sharedStrings[(int)$cellValue])) {
                            $cellValue = $sharedStrings[(int)$cellValue];
                        }
                        $row[] = $cellValue;
                    }
                    $rows[] = $row;
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Process and import parsed rows into PBS-ERP Kas transactions
     */
    public function importTransactions(array $rawRows, string $importedBy = 'System'): array
    {
        $successCount = 0;
        $totalNominal = 0;
        $errors = [];
        $importedTrans = [];

        if (empty($rawRows)) {
            return [
                'success' => false,
                'message' => 'File Excel/CSV kosong atau tidak dapat dibaca.',
                'imported_count' => 0,
                'total_nominal' => 0,
                'errors' => ['File tidak memiliki data.'],
            ];
        }

        // Cache all active accounts for fast lookup
        $accounts = Akun::all()->keyBy('kode_akun');

        // Loop rows (skip header if detected)
        $isFirstRow = true;
        $rowNum = 0;

        foreach ($rawRows as $row) {
            $rowNum++;

            // Skip completely empty rows
            if (empty(array_filter($row, fn($v) => !is_null($v) && trim($v) !== ''))) {
                continue;
            }

            // Detect header row
            $firstCol = trim((string)($row[0] ?? ''));
            $secondCol = trim((string)($row[1] ?? ''));
            if ($isFirstRow && (
                stripos($firstCol, 'no') !== false ||
                stripos($firstCol, 'tanggal') !== false ||
                stripos($firstCol, 'tipe') !== false ||
                stripos($secondCol, 'tanggal') !== false ||
                stripos($secondCol, 'tipe') !== false
            )) {
                $isFirstRow = false;
                continue;
            }
            $isFirstRow = false;

            // Column Mapping:
            // Col 0: No Transaksi (Optional / Auto)
            // Col 1: Tanggal (YYYY-MM-DD or DD/MM/YYYY or Excel Serial)
            // Col 2: Tipe (Kas Masuk / BKM, Kas Keluar / BKK, Transfer / TRF)
            // Col 3: Kode Akun Kas/Bank (1-1100, 1-1200, 1-1210)
            // Col 4: Kode Akun Lawan (4-1100, 6-1200, 2-1100, etc.)
            // Col 5: Nominal (Number / Rp)
            // Col 6: Pihak Terkait (Diterima dari / Dibayar kepada)
            // Col 7: Keterangan (Uraian Transaksi)
            // Col 8: Referensi (No Bukti / Invoice)

            $noTransaksi = trim((string)($row[0] ?? ''));
            $rawTanggal  = trim((string)($row[1] ?? ''));
            $rawTipe     = trim((string)($row[2] ?? ''));
            $kodeKas     = trim((string)($row[3] ?? ''));
            $kodeLawan   = trim((string)($row[4] ?? ''));
            $rawNominal  = trim((string)($row[5] ?? '0'));
            $pihak       = trim((string)($row[6] ?? ''));
            $keterangan  = trim((string)($row[7] ?? ''));
            $referensi   = trim((string)($row[8] ?? ''));

            // Parse & Validate Tipe
            $tipe = $this->normalizeTipe($rawTipe);
            if (!$tipe) {
                $errors[] = "Baris $rowNum: Tipe transaksi '$rawTipe' tidak valid (Gunakan: Kas Masuk / BKM, Kas Keluar / BKK, atau Transfer / TRF).";
                continue;
            }

            // Parse & Validate Tanggal
            $tanggal = $this->normalizeTanggal($rawTanggal);
            if (!$tanggal) {
                $errors[] = "Baris $rowNum: Format tanggal '$rawTanggal' tidak valid (Gunakan format YYYY-MM-DD atau DD/MM/YYYY).";
                continue;
            }

            // Parse & Validate Nominal
            $nominal = $this->normalizeNominal($rawNominal);
            if ($nominal <= 0) {
                $errors[] = "Baris $rowNum: Nominal transaksi harus lebih besar dari 0 (Ditemukan: '$rawNominal').";
                continue;
            }

            // Validate Kas Account
            $kasAkun = $accounts->get($kodeKas);
            if (!$kasAkun) {
                $errors[] = "Baris $rowNum: Kode Akun Kas '$kodeKas' tidak ditemukan dalam Chart of Accounts.";
                continue;
            }
            if ($kasAkun->tipe_akun !== 'Kas & Bank') {
                $errors[] = "Baris $rowNum: Kode Akun '$kodeKas' ({$kasAkun->nama_akun}) bukan akun tipe 'Kas & Bank'.";
                continue;
            }

            // Validate Lawan Account
            $lawanAkun = $accounts->get($kodeLawan);
            if (!$lawanAkun) {
                $errors[] = "Baris $rowNum: Kode Akun Lawan '$kodeLawan' tidak ditemukan dalam Chart of Accounts.";
                continue;
            }

            // Generate No Transaksi if empty or check uniqueness
            if (empty($noTransaksi)) {
                $prefix = ($tipe === 'Kas Masuk' ? 'BKM' : ($tipe === 'Kas Keluar' ? 'BKK' : 'TRF'));
                $noTransaksi = $prefix . '-IMP-' . date('Ymd-His') . '-' . str_pad($rowNum, 3, '0', STR_PAD_LEFT);
            }

            // Check if already exists in DB
            if (JurnalUmum::where('no_transaksi', $noTransaksi)->exists()) {
                $noTransaksi = $noTransaksi . '-DUP' . time() . rand(10, 99);
            }

            if (empty($keterangan)) {
                $keterangan = "Impor Transaksi $tipe: " . ($pihak ? "[$pihak]" : "");
            }

            // Execute Transaction Posting
            try {
                DB::transaction(function () use (
                    $noTransaksi,
                    $tanggal,
                    $tipe,
                    $kodeKas,
                    $kodeLawan,
                    $nominal,
                    $pihak,
                    $keterangan,
                    $referensi,
                    $importedBy
                ) {
                    $kasAkun = Akun::where('kode_akun', $kodeKas)->lockForUpdate()->firstOrFail();
                    $lawanAkun = Akun::where('kode_akun', $kodeLawan)->lockForUpdate()->firstOrFail();

                    $sumberRef = $referensi ?: ($tipe === 'Kas Masuk' ? 'BKM Impor' : ($tipe === 'Kas Keluar' ? 'BKK Impor' : 'Transfer Impor'));
                    if ($pihak) {
                        $sumberRef .= " (" . $pihak . ")";
                    }

                    $jurnal = JurnalUmum::create([
                        'no_transaksi' => $noTransaksi,
                        'tanggal' => $tanggal,
                        'tipe_jurnal' => $tipe,
                        'deskripsi' => $keterangan,
                        'sumber_referensi' => $sumberRef,
                        'total_debit' => $nominal,
                        'total_kredit' => $nominal,
                        'created_by' => $importedBy,
                        'is_posted' => true,
                    ]);

                    if ($tipe === 'Kas Masuk') {
                        // Debit Kas, Kredit Lawan
                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $kasAkun->kode_akun,
                            'keterangan_baris' => 'Penerimaan Kas/Bank' . ($pihak ? " dari $pihak" : ""),
                            'debit' => $nominal,
                            'kredit' => 0,
                        ]);
                        $kasAkun->saldo_berjalan += ($kasAkun->saldo_normal === 'Debit' ? $nominal : -$nominal);
                        $kasAkun->save();

                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $lawanAkun->kode_akun,
                            'keterangan_baris' => $keterangan,
                            'debit' => 0,
                            'kredit' => $nominal,
                        ]);
                        $lawanAkun->saldo_berjalan += ($lawanAkun->saldo_normal === 'Kredit' ? $nominal : -$nominal);
                        $lawanAkun->save();

                    } elseif ($tipe === 'Kas Keluar') {
                        // Debit Lawan, Kredit Kas
                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $lawanAkun->kode_akun,
                            'keterangan_baris' => $keterangan,
                            'debit' => $nominal,
                            'kredit' => 0,
                        ]);
                        $lawanAkun->saldo_berjalan += ($lawanAkun->saldo_normal === 'Debit' ? $nominal : -$nominal);
                        $lawanAkun->save();

                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $kasAkun->kode_akun,
                            'keterangan_baris' => 'Pengeluaran Kas/Bank' . ($pihak ? " kepada $pihak" : ""),
                            'debit' => 0,
                            'kredit' => $nominal,
                        ]);
                        $kasAkun->saldo_berjalan += ($kasAkun->saldo_normal === 'Debit' ? -$nominal : $nominal);
                        $kasAkun->save();

                    } else { // Transfer
                        // Debit Lawan (Kas Tujuan), Kredit Kas (Kas Asal)
                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $lawanAkun->kode_akun,
                            'keterangan_baris' => 'Penerimaan transfer dari ' . $kasAkun->nama_akun,
                            'debit' => $nominal,
                            'kredit' => 0,
                        ]);
                        $lawanAkun->saldo_berjalan += $nominal;
                        $lawanAkun->save();

                        JurnalDetail::create([
                            'id_jurnal' => $jurnal->id_jurnal,
                            'kode_akun' => $kasAkun->kode_akun,
                            'keterangan_baris' => 'Transfer keluar ke ' . $lawanAkun->nama_akun,
                            'debit' => 0,
                            'kredit' => $nominal,
                        ]);
                        $kasAkun->saldo_berjalan -= $nominal;
                        $kasAkun->save();
                    }
                });

                $successCount++;
                $totalNominal += $nominal;
                $importedTrans[] = $noTransaksi;
            } catch (\Throwable $e) {
                $errors[] = "Baris $rowNum: Gagal menyimpan ke database ({$e->getMessage()}).";
            }
        }

        return [
            'success' => $successCount > 0,
            'imported_count' => $successCount,
            'total_nominal' => $totalNominal,
            'errors' => $errors,
            'transactions' => $importedTrans,
        ];
    }

    /**
     * Normalize transaction type
     */
    private function normalizeTipe(string $tipe): ?string
    {
        $clean = strtoupper(trim($tipe));
        if (in_array($clean, ['KAS MASUK', 'BKM', 'MASUK', 'RECEIPT', 'CR', 'DEBIT'])) {
            return 'Kas Masuk';
        }
        if (in_array($clean, ['KAS KELUAR', 'BKK', 'KELUAR', 'DISBURSEMENT', 'CD', 'PAYMENT', 'KREDIT'])) {
            return 'Kas Keluar';
        }
        if (in_array($clean, ['TRANSFER', 'TRF', 'MUTASI', 'PINDAH BUKU', 'OVERBOOKING'])) {
            return 'Transfer';
        }
        return null;
    }

    /**
     * Normalize date from string or Excel serial number
     */
    private function normalizeTanggal(string $dateStr): ?string
    {
        $dateStr = trim($dateStr);
        if (empty($dateStr)) return date('Y-m-d');

        // Check if numeric (Excel Serial Date e.g. 45558)
        if (is_numeric($dateStr) && (float)$dateStr > 20000) {
            $unixTimestamp = ((float)$dateStr - 25569) * 86400;
            return date('Y-m-d', (int)$unixTimestamp);
        }

        // Try standard formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd.m.Y', 'm/d/Y'];
        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $dateStr);
                if ($dt) return $dt->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        try {
            $dt = Carbon::parse($dateStr);
            if ($dt) return $dt->format('Y-m-d');
        } catch (\Throwable $e) {}

        return null;
    }

    /**
     * Normalize currency number
     */
    private function normalizeNominal(string $numStr): float
    {
        $cleaned = str_ireplace(['rp', 'idr', ' ', '`'], '', trim($numStr));

        // Format handling like 1.500.000,00 or 1500000
        if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
            if (strrpos($cleaned, ',') > strrpos($cleaned, '.')) {
                // Indonesian format: 1.500.000,50
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // US format: 1,500,000.50
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif (strpos($cleaned, ',') !== false) {
            // Check if comma is decimal or thousand
            $parts = explode(',', $cleaned);
            if (strlen(end($parts)) === 3 && count($parts) > 1) {
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                $cleaned = str_replace(',', '.', $cleaned);
            }
        } elseif (strpos($cleaned, '.') !== false) {
            $parts = explode('.', $cleaned);
            if (strlen(end($parts)) === 3 && count($parts) > 1) {
                $cleaned = str_replace('.', '', $cleaned);
            }
        }

        return (float)filter_var($cleaned, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Generate standard CSV template content
     */
    public function generateTemplateCsv(): string
    {
        $output = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'No Transaksi',
            'Tanggal',
            'Tipe Transaksi',
            'Kode Akun Kas',
            'Kode Akun Lawan',
            'Nominal',
            'Pihak Terkait',
            'Keterangan',
            'Referensi'
        ]);

        // Sample Row 1: BKM (Penerimaan Kas)
        fputcsv($output, [
            'BKM-2026-0001',
            date('Y-m-d'),
            'Kas Masuk',
            '1-1200', // Bank Mandiri
            '4-1100', // Pendapatan Jasa & Pengadaan
            '75000000',
            'PT Mitra Sejahtera Sentosa',
            'Penerimaan Termin 2 Proyek ERP PBS',
            'INV-PBS-2026-002'
        ]);

        // Sample Row 2: BKK (Pengeluaran Kas)
        fputcsv($output, [
            'BKK-2026-0001',
            date('Y-m-d'),
            'Kas Keluar',
            '1-1100', // Kas Operasional
            '6-1200', // Beban Operasional Kantor
            '4500000',
            'PLN & Telkom Indonesia',
            'Pembayaran Tagihan Listrik & Internet Kantor',
            'TAG-PLN-SEP26'
        ]);

        // Sample Row 3: Transfer Kas
        fputcsv($output, [
            'TRF-2026-0001',
            date('Y-m-d'),
            'Transfer',
            '1-1200', // Bank Mandiri Asal
            '1-1100', // Kas Operasional Tujuan
            '15000000',
            'Internal PBS',
            'Pengisian Kas Kecil & Operasional Pabrik',
            'MUTASI-MDR-01'
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
