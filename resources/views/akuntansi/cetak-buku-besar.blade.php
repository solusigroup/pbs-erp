<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar {{ $akun->kode_akun }} - {{ $akun->nama_akun }} - {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</title>
    
    <style>
        /* Standalone High-Speed Print & Display Stylesheet (Zero Heavy JS / Low Memory) */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9pt;
            color: #0f172a;
            background-color: #0b1329;
            line-height: 1.3;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        /* Screen Wrapper */
        .screen-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 0 15px;
        }

        /* Floating Toolbar (Screen only) */
        .toolbar {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #f8fafc;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background-color: #1e293b;
            color: #e2e8f0;
            border: 1px solid #475569;
            border-radius: 8px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .btn-back:hover {
            background-color: #334155;
            color: #ffffff;
        }

        .toolbar-info {
            font-size: 11px;
            color: #94a3b8;
        }

        .toolbar-info strong {
            color: #38bdf8;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0f172a;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            transition: all 0.15s ease;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            transform: translateY(-1px);
        }

        /* Printable Paper Canvas */
        .paper {
            background-color: #ffffff;
            color: #0f172a;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            padding: 24px 30px;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 2.5px solid #0f172a;
        }

        .kop-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kop-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 2px;
        }

        .kop-title {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        .kop-sub {
            font-size: 9pt;
            font-weight: 700;
            color: #b45309;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .kop-desc {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }

        .kop-meta {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 1px;
        }

        .kop-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .kop-print-date {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 4px;
            text-align: right;
            font-family: ui-monospace, monospace;
        }

        .kop-rule-thin {
            border-bottom: 1px solid #94a3b8;
            margin-top: 2px;
            margin-bottom: 12px;
        }

        /* Document Title */
        .doc-title-block {
            text-align: center;
            padding: 6px 0 10px 0;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.03em;
        }

        .doc-account {
            font-size: 10pt;
            font-weight: 700;
            color: #0284c7;
            margin-top: 2px;
        }

        .doc-period {
            font-size: 8.5pt;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
        }

        /* KPI Summary Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .kpi-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background-color: #f8fafc;
            text-align: center;
        }

        .kpi-label {
            display: block;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.03em;
        }

        .kpi-value {
            display: block;
            font-size: 10pt;
            font-weight: 800;
            font-family: ui-monospace, monospace;
            margin-top: 2px;
        }

        /* Fixed Table Layout for Max Speed & Low Memory */
        table.ledger-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 8pt;
            border: 1.5px solid #0f172a;
        }

        table.ledger-table thead {
            display: table-header-group;
        }

        table.ledger-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.03em;
            padding: 5px 6px;
            border: 1px solid #334155;
            text-align: left;
        }

        table.ledger-table th.text-right {
            text-align: right;
        }

        table.ledger-table th.text-center {
            text-align: center;
        }

        table.ledger-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        table.ledger-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table.ledger-table td {
            padding: 3.5px 6px;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            line-height: 1.25;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.ledger-table td.text-right {
            text-align: right;
        }

        table.ledger-table td.text-center {
            text-align: center;
        }

        .row-saldo-awal {
            background-color: #f1f5f9 !important;
            font-style: italic;
            font-weight: 600;
        }

        .row-total {
            background-color: #e2e8f0 !important;
            font-weight: 800;
            border-top: 2px solid #0f172a;
            border-bottom: 3px double #0f172a;
        }

        /* Lembar Pengesahan */
        .signature-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            font-size: 8pt;
            color: #334155;
            margin-bottom: 8px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            text-align: center;
        }

        .signature-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px;
            background-color: #ffffff;
        }

        .signature-role {
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 38px;
            display: block;
        }

        .signature-name {
            display: block;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
        }

        .signature-title {
            display: block;
            font-size: 7pt;
            color: #64748b;
        }

        .doc-footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }

        /* PRINT MEDIA RULES (Fast, Minimal Overhead, No Shadow, Fixed Table) */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .screen-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .paper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 10mm 10mm 10mm;
            }

            table.ledger-table {
                border-color: #000000 !important;
            }

            table.ledger-table th {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.ledger-table td {
                border-color: #94a3b8 !important;
                color: #000000 !important;
            }

            .kpi-box {
                background-color: #f8fafc !important;
                border-color: #94a3b8 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .signature-box {
                border-color: #94a3b8 !important;
            }
        }
    </style>
</head>
<body>

    <div class="screen-container">
        <!-- Floating Toolbar (Screen only) -->
        <div class="toolbar no-print">
            <div class="toolbar-left">
                <a href="{{ route('akuntansi.buku-besar', request()->query()) }}" class="btn-back">
                    &larr; Kembali ke Buku Besar
                </a>
                <div class="toolbar-info">
                    Akun: <strong>{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</strong> &bull; Total Mutasi: <strong>{{ count($mutasiDetails) }} baris</strong>
                </div>
            </div>
            <div>
                <button onclick="window.print()" class="btn-print">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <!-- Main Paper Canvas -->
        <div class="paper">
            <!-- Kop Surat Resmi -->
            <div class="kop-header">
                <div class="kop-left">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PBS" class="kop-logo">
                    <div>
                        <div class="kop-title">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</div>
                        <div class="kop-sub">Pengolahan Limbah Industri, Bahan Bakar Alternatif (RDF) &amp; Pengelolaan Lingkungan</div>
                        <div class="kop-desc">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto' }}, {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ $perusahaan->provinsi ?? 'Jawa Timur' }}</div>
                        <div class="kop-meta">NPWP: {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }} &bull; Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} &bull; Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</div>
                    </div>
                </div>
                <div style="text-align:right">
                    <div class="kop-badge">BUKU BESAR</div>
                    <div class="kop-print-date">Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
            <div class="kop-rule-thin"></div>

            <!-- Title & Account Details -->
            <div class="doc-title-block">
                <div class="doc-title">BUKU BESAR (GENERAL LEDGER)</div>
                <div class="doc-account">{{ $akun->kode_akun }} - {{ $akun->nama_akun }} ({{ $akun->kategori }})</div>
                <div class="doc-period">
                    Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}</strong> &bull; Saldo Normal: <strong>{{ $akun->saldo_normal }}</strong>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Saldo Awal</span>
                    <span class="kpi-value" style="color:#d97706">Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Mutasi Debit</span>
                    <span class="kpi-value" style="color:#059669">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Mutasi Kredit</span>
                    <span class="kpi-value" style="color:#dc2626">Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
                </div>
                <div class="kpi-box" style="border-color:#0284c7; background-color:#f0f9ff">
                    <span class="kpi-label" style="color:#0369a1">Saldo Akhir</span>
                    <span class="kpi-value" style="color:#0369a1">Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Fast Fixed Layout Ledger Table -->
            <table class="ledger-table">
                <colgroup>
                    <col style="width: 10%;"> <!-- Tanggal -->
                    <col style="width: 15%;"> <!-- No Transaksi -->
                    <col style="width: 12%;"> <!-- Tipe -->
                    <col style="width: 33%;"> <!-- Keterangan -->
                    <col style="width: 10%;"> <!-- Debit -->
                    <col style="width: 10%;"> <!-- Kredit -->
                    <col style="width: 10%;"> <!-- Saldo Berjalan -->
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th>No Transaksi</th>
                        <th>Tipe</th>
                        <th>Keterangan / Uraian</th>
                        <th class="text-right">Debit (Rp)</th>
                        <th class="text-right">Kredit (Rp)</th>
                        <th class="text-right">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal Row -->
                    <tr class="row-saldo-awal">
                        <td class="text-center font-mono">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="font-mono">-</td>
                        <td>SALDO AWAL</td>
                        <td>Saldo per awal periode {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="text-right font-mono">-</td>
                        <td class="text-right font-mono">-</td>
                        <td class="text-right font-mono" style="font-weight:700">{{ number_format($saldoAwalPeriode, 0, ',', '.') }}</td>
                    </tr>

                    @forelse($mutasiDetails as $m)
                        <tr>
                            <td class="text-center font-mono">{{ $m->jurnal ? $m->jurnal->tanggal->format('d/m/Y') : '-' }}</td>
                            <td class="font-mono" style="font-weight:700">{{ $m->jurnal ? $m->jurnal->no_transaksi : '-' }}</td>
                            <td>{{ $m->jurnal ? $m->jurnal->tipe_jurnal : '-' }}</td>
                            <td>
                                {{ $m->keterangan_baris ?: ($m->jurnal ? $m->jurnal->deskripsi : '-') }}
                                @if($m->jurnal && $m->jurnal->sumber_referensi)
                                    <span style="font-size:7pt; color:#64748b; display:block">Ref: {{ $m->jurnal->sumber_referensi }}</span>
                                @endif
                            </td>
                            <td class="text-right font-mono" style="color:{{ $m->debit > 0 ? '#047857' : '#64748b' }}">
                                {{ $m->debit > 0 ? number_format($m->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-mono" style="color:{{ $m->kredit > 0 ? '#b91c1c' : '#64748b' }}">
                                {{ $m->kredit > 0 ? number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-mono" style="font-weight:600">
                                {{ number_format($m->saldo_berjalan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding:16px; color:#64748b; font-style:italic">
                                Tidak ada mutasi transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Grand Total Row -->
                    <tr class="row-total">
                        <td colspan="4" class="text-right" style="text-transform:uppercase">TOTAL MUTASI &amp; SALDO AKHIR:</td>
                        <td class="text-right font-mono" style="color:#047857">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color:#b91c1c">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="font-size:9pt">{{ number_format($saldoAkhirPeriode, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Signature Blocks -->
            <div class="signature-section">
                <div class="signature-date">
                    {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}
                </div>
                <div class="signature-grid">
                    <div class="signature-box">
                        <span class="signature-role">Dibuat &amp; Disusun Oleh,</span>
                        <span class="signature-name">Staff Akuntansi</span>
                        <span class="signature-title">Divisi Keuangan &amp; Akuntansi</span>
                    </div>
                    <div class="signature-box">
                        <span class="signature-role">Diperiksa &amp; Diverifikasi Oleh,</span>
                        <span class="signature-name">Manager Keuangan</span>
                        <span class="signature-title">Divisi Keuangan &amp; Akuntansi</span>
                    </div>
                    <div class="signature-box">
                        <span class="signature-role">Disetujui &amp; Disahkan Oleh,</span>
                        <span class="signature-name">{{ $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.' }}</span>
                        <span class="signature-title">Board of Director (Finance &amp; Tax)</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="doc-footer">
                Buku Besar Resmi PT Pinastika Bhakti Semesta &bull; Sistem ERP Terpadu PBS-ERP &bull; Standar Akuntansi Keuangan SAK EP / EMKM
            </div>
        </div>
    </div>

</body>
</html>
