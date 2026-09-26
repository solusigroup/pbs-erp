<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Bisnis (YoY) - Basis {{ $tahun }} vs {{ $tahunBanding }} - {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</title>
    
    <!-- Font Awesome for Toolbar Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style id="baseStyle">
        /* Standalone High-Speed Print & Display Stylesheet (Zero Heavy JS / Low Memory) */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 8.5pt;
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
            max-width: 960px;
            margin: 20px auto;
            padding: 0 15px;
            transition: max-width 0.2s ease;
        }

        .screen-container.landscape {
            max-width: 1140px;
        }

        /* Floating Toolbar (Screen only) */
        .toolbar {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
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
            color: #34d399;
        }

        .toolbar-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .toolbar-select-group {
            display: flex;
            align-items: center;
            gap: 6px;
            background-color: #1e293b;
            padding: 4px 8px;
            border-radius: 8px;
            border: 1px solid #334155;
            font-size: 11px;
        }

        .toolbar-select-group select {
            background: #0f172a;
            color: #ffffff;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 3px 6px;
            font-size: 11px;
            font-weight: bold;
            outline: none;
        }

        .btn-filter {
            background-color: #f59e0b;
            color: #0f172a;
            border: none;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-filter:hover {
            background-color: #fbbf24;
        }

        .btn-tool {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background-color: #1e293b;
            color: #e2e8f0;
            border: 1px solid #475569;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-tool:hover {
            background-color: #334155;
            color: #ffffff;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.15s ease;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #34d399, #10b981);
            transform: translateY(-1px);
        }

        /* Printable Paper Canvas */
        .paper {
            background-color: #ffffff;
            color: #0f172a;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            padding: 24px 28px;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 2.5px solid #0f172a;
        }

        .kop-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kop-logo {
            width: 54px;
            height: 54px;
            object-fit: contain;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 2px;
        }

        .kop-title {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        .kop-sub {
            font-size: 8.5pt;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .kop-desc {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 1px;
        }

        .kop-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 7px;
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
            margin-bottom: 10px;
        }

        /* Document Title */
        .doc-title-block {
            text-align: center;
            padding: 4px 0 8px 0;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.02em;
        }

        .doc-period {
            font-size: 8.5pt;
            font-weight: 700;
            color: #047857;
            margin-top: 2px;
        }

        /* Section Headings */
        .section-header {
            font-size: 8.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #0f172a;
            margin: 12px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header span.tag {
            font-size: 7pt;
            font-weight: 700;
            background: #e2e8f0;
            padding: 1px 6px;
            border-radius: 3px;
            color: #334155;
        }

        /* KPI Scorecard Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .kpi-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background-color: #f8fafc;
        }

        .kpi-title {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .kpi-badge {
            font-size: 6.5pt;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 3px;
        }

        .badge-up {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-down {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-neutral {
            background-color: #e2e8f0;
            color: #334155;
        }

        .kpi-main-val {
            font-size: 9.5pt;
            font-weight: 800;
            font-family: ui-monospace, monospace;
            color: #0f172a;
        }

        .kpi-sub-line {
            font-size: 7pt;
            color: #64748b;
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* High Speed Compact Tables */
        table.report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 7.5pt;
            border: 1.5px solid #0f172a;
            margin-bottom: 12px;
        }

        table.report-table thead {
            display: table-header-group;
        }

        table.report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.02em;
            padding: 4px 5px;
            border: 1px solid #334155;
            text-align: left;
            vertical-align: middle;
        }

        table.report-table th.subhead {
            background-color: #1e293b;
            font-size: 6.5pt;
            color: #e2e8f0;
        }

        table.report-table th.text-right,
        table.report-table td.text-right {
            text-align: right;
        }

        table.report-table th.text-center,
        table.report-table td.text-center {
            text-align: center;
        }

        table.report-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        table.report-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table.report-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            line-height: 1.2;
            vertical-align: middle;
        }

        table.report-table tfoot tr {
            background-color: #e2e8f0 !important;
            font-weight: 800;
            border-top: 2px solid #0f172a;
            border-bottom: 3px double #0f172a;
        }

        table.report-table tfoot td {
            border: 1px solid #94a3b8;
            font-weight: 800;
            padding: 4px 5px;
        }

        /* 2-Column Grid for Pareto & Secondary Details */
        .two-col-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        /* Signatures Section */
        .signature-section {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            font-size: 7.5pt;
            color: #334155;
            margin-bottom: 6px;
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
            padding: 6px;
            background-color: #ffffff;
        }

        .signature-role {
            font-size: 7pt;
            color: #475569;
            margin-bottom: 34px;
            display: block;
        }

        .signature-name {
            display: block;
            border-top: 1px solid #94a3b8;
            padding-top: 3px;
            font-size: 7.5pt;
            font-weight: 800;
            color: #0f172a;
        }

        .signature-title {
            display: block;
            font-size: 6.5pt;
            color: #64748b;
        }

        .doc-footer {
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 6.5pt;
            color: #64748b;
        }

        /* PRINT MEDIA OVERRIDES */
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

            table.report-table {
                border-color: #000000 !important;
            }

            table.report-table th {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.report-table td {
                border-color: #94a3b8 !important;
            }

            .kpi-card {
                background-color: #ffffff !important;
                border: 1px solid #94a3b8 !important;
            }

            .break-inside-avoid {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
        }
    </style>

    <style id="pageStyle">
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 10mm 10mm;
        }
    </style>
</head>
<body>

    <div class="screen-container" id="screenWrapper">
        
        <!-- Top Action Floating Toolbar (Screen Only) -->
        <div class="toolbar no-print">
            <div class="toolbar-left">
                <a href="{{ route('analisis.index', ['tahun' => $tahun, 'tahun_banding' => $tahunBanding]) }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                <div class="toolbar-info">
                    Format Dokumen Cetak: <strong>Ringan &amp; Rapi</strong> (Zero Bloat)
                </div>
            </div>

            <div class="toolbar-controls">
                <!-- Year Comparison Switcher inside Cetak -->
                <form method="GET" action="{{ route('analisis.cetak') }}" class="toolbar-select-group">
                    <span>Basis:</span>
                    <select name="tahun">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <span style="color:#64748b; font-weight:bold;">VS</span>

                    <span>Pembanding:</span>
                    <select name="tahun_banding">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $tahunBanding == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-filter" title="Ganti Tahun Pembanding">
                        <i class="fas fa-rotate"></i>
                    </button>
                </form>

                <!-- Orientation Switcher -->
                <button type="button" onclick="toggleOrientation()" class="btn-tool" id="btnOrient" title="Ganti Orientasi Kertas (Portrait / Landscape)">
                    <i class="fas fa-file-invoice"></i>
                    <span id="orientText">Landscape</span>
                </button>

                <!-- Print Trigger Button -->
                <button type="button" onclick="window.print()" class="btn-print">
                    <i class="fas fa-print"></i>
                    <span>Cetak Sekarang / PDF</span>
                </button>
            </div>
        </div>

        <!-- Printable Paper Canvas -->
        <div class="paper">
            
            <!-- Kop Surat Resmi Korporasi -->
            <div class="kop-header">
                <div class="kop-left">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PBS" class="kop-logo">
                    <div>
                        <div class="kop-title">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</div>
                        <div class="kop-sub">Executive Business Intelligence &bull; Laporan Evaluasi Komparatif YoY</div>
                        <div class="kop-desc">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto' }} | NPWP: {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }}</div>
                    </div>
                </div>
                <div>
                    <div style="text-align: right;">
                        <span class="kop-badge">LAPORAN RESMI EKSEKUTIF</span>
                        <div class="kop-print-date">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
                        <div class="kop-print-date">User: {{ auth()->user()->name ?? 'Administrator' }}</div>
                    </div>
                </div>
            </div>
            <div class="kop-rule-thin"></div>

            <!-- Judul Dokumen -->
            <div class="doc-title-block">
                <div class="doc-title">LAPORAN ANALISIS BISNIS &amp; EVALUASI KOMPARATIF (YoY)</div>
                <div class="doc-period">PERIODE KOMPARATIF: TAHUN BASIS {{ $tahun }} vs. TAHUN PEMBANDING {{ $tahunBanding }}</div>
            </div>

            <!-- Section 1: Executive KPI Scorecard -->
            <div class="section-header">
                <span>I. Ringkasan Indikator Kinerja Utama (Executive Scorecard)</span>
                <span class="tag">Komparatif YoY</span>
            </div>

            <div class="kpi-grid">
                <!-- KPI 1: Penjualan -->
                <div class="kpi-card">
                    <div class="kpi-title">
                        <span>Penjualan (Omset)</span>
                        <span class="kpi-badge {{ $yoyMetrics['sales_delta_pct'] >= 0 ? 'badge-up' : 'badge-down' }}">
                            {{ $yoyMetrics['sales_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_pct'] }}%
                        </span>
                    </div>
                    <div class="kpi-main-val">Rp {{ number_format($yoyMetrics['sales1'], 0, ',', '.') }}</div>
                    <div class="kpi-sub-line">
                        <span>Thn {{ $tahunBanding }}: Rp {{ number_format($yoyMetrics['sales2'] / 1000000, 1, ',', '.') }} Jt</span>
                        <span>{{ $yoyMetrics['sales_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['sales_delta_rp'] / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                    <div class="kpi-sub-line" style="border-top: 1px dotted #cbd5e1; margin-top: 3px; padding-top: 2px;">
                        <span>Vol: {{ number_format($yoyMetrics['sales_qty1'], 0, ',', '.') }} Kg</span>
                        <span>Delta: {{ $yoyMetrics['sales_delta_qty_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_qty_pct'] }}%</span>
                    </div>
                </div>

                <!-- KPI 2: Pembelian Bahan Baku -->
                <div class="kpi-card">
                    <div class="kpi-title">
                        <span>Pembelian Bahan</span>
                        <span class="kpi-badge {{ $yoyMetrics['raw_delta_pct'] <= 0 ? 'badge-up' : 'badge-down' }}">
                            {{ $yoyMetrics['raw_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_pct'] }}%
                        </span>
                    </div>
                    <div class="kpi-main-val">Rp {{ number_format($yoyMetrics['raw1'], 0, ',', '.') }}</div>
                    <div class="kpi-sub-line">
                        <span>Thn {{ $tahunBanding }}: Rp {{ number_format($yoyMetrics['raw2'] / 1000000, 1, ',', '.') }} Jt</span>
                        <span>{{ $yoyMetrics['raw_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['raw_delta_rp'] / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                    <div class="kpi-sub-line" style="border-top: 1px dotted #cbd5e1; margin-top: 3px; padding-top: 2px;">
                        <span>Pasok: {{ number_format($yoyMetrics['raw_qty1'], 0, ',', '.') }} Kg</span>
                        <span>Delta: {{ $yoyMetrics['raw_delta_qty_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_qty_pct'] }}%</span>
                    </div>
                </div>

                <!-- KPI 3: Laba Kotor & Gross Margin -->
                <div class="kpi-card">
                    <div class="kpi-title">
                        <span>Laba Kotor (Gross Margin)</span>
                        <span class="kpi-badge {{ $yoyMetrics['margin_pct1'] >= 30 ? 'badge-up' : 'badge-neutral' }}">
                            {{ $yoyMetrics['margin_pct1'] }}% Margin
                        </span>
                    </div>
                    <div class="kpi-main-val">Rp {{ number_format($yoyMetrics['margin1'], 0, ',', '.') }}</div>
                    <div class="kpi-sub-line">
                        <span>Thn {{ $tahunBanding }}: {{ $yoyMetrics['margin_pct2'] }}% Margin</span>
                        <span>Delta: {{ $yoyMetrics['margin_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['margin_delta_pct'] }}%</span>
                    </div>
                    <div class="kpi-sub-line" style="border-top: 1px dotted #cbd5e1; margin-top: 3px; padding-top: 2px;">
                        <span>Selisih Nominal:</span>
                        <span>{{ $yoyMetrics['margin_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['margin_delta_rp'] / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                </div>

                <!-- KPI 4: Rendemen & Posisi Kas/Piutang -->
                <div class="kpi-card">
                    <div class="kpi-title">
                        <span>Rendemen Cuci Giling</span>
                        <span class="kpi-badge {{ $rendemenPersen >= 80 ? 'badge-up' : 'badge-down' }}">
                            {{ $rendemenPersen }}% Yield
                        </span>
                    </div>
                    <div class="kpi-main-val">Susut: {{ $susutPersen }}%</div>
                    <div class="kpi-sub-line">
                        <span>Piutang Usaha (AR):</span>
                        <span style="font-weight: bold;">Rp {{ number_format($totalPiutang / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                    <div class="kpi-sub-line" style="border-top: 1px dotted #cbd5e1; margin-top: 3px; padding-top: 2px;">
                        <span>Hutang Pemasok (AP):</span>
                        <span style="font-weight: bold; color: #991b1b;">Rp {{ number_format($totalHutang / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tabel Rincian Komparatif Bulanan Detail (12 Bulan) -->
            <div class="section-header">
                <span>II. Matriks Komparatif Bulanan Detail (Januari s/d Desember)</span>
                <span class="tag">Basis: {{ $tahun }} vs {{ $tahunBanding }}</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 10%; text-align: center;">Bulan</th>
                        <th colspan="3" style="text-align: center; border-right: 1.5px solid #334155;">PENJUALAN (OMSET)</th>
                        <th colspan="3" style="text-align: center; border-right: 1.5px solid #334155;">PEMBELIAN (BAHAN BAKU)</th>
                        <th colspan="3" style="text-align: center;">LABA KOTOR (GROSS PROFIT)</th>
                    </tr>
                    <tr>
                        <!-- Penjualan -->
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahun }}</th>
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahunBanding }}</th>
                        <th class="subhead text-center" style="width: 8%; border-right: 1.5px solid #334155;">YoY %</th>
                        <!-- Pembelian -->
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahun }}</th>
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahunBanding }}</th>
                        <th class="subhead text-center" style="width: 8%; border-right: 1.5px solid #334155;">YoY %</th>
                        <!-- Laba Kotor -->
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahun }}</th>
                        <th class="subhead text-right" style="width: 10.5%;">Thn {{ $tahunBanding }}</th>
                        <th class="subhead text-center" style="width: 8%;">YoY %</th>
                    </tr>
                </thead>
                <tbody class="font-mono">
                    @foreach($monthlyYoYMatrix as $m)
                        <tr>
                            <td class="text-center font-sans" style="font-weight: 700;">{{ $m['bulan_nama'] }}</td>

                            <!-- Penjualan 1 & 2 & Delta -->
                            <td class="text-right">
                                {{ $m['sales_1'] > 0 ? number_format($m['sales_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right" style="color: #64748b;">
                                {{ $m['sales_2'] > 0 ? number_format($m['sales_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-center font-sans" style="border-right: 1.5px solid #cbd5e1;">
                                @if($m['sales_1'] == 0 && $m['sales_2'] == 0)
                                    <span style="color:#94a3b8;">-</span>
                                @else
                                    <span style="font-size: 7pt; font-weight: bold; color: {{ $m['sales_delta'] >= 0 ? '#047857' : '#b91c1c' }};">
                                        {{ $m['sales_grow'] >= 0 ? '+' : '' }}{{ $m['sales_grow'] }}%
                                    </span>
                                @endif
                            </td>

                            <!-- Pembelian 1 & 2 & Delta -->
                            <td class="text-right">
                                {{ $m['raw_1'] > 0 ? number_format($m['raw_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right" style="color: #64748b;">
                                {{ $m['raw_2'] > 0 ? number_format($m['raw_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-center font-sans" style="border-right: 1.5px solid #cbd5e1;">
                                @if($m['raw_1'] == 0 && $m['raw_2'] == 0)
                                    <span style="color:#94a3b8;">-</span>
                                @else
                                    <span style="font-size: 7pt; font-weight: bold; color: {{ $m['raw_delta'] <= 0 ? '#047857' : '#b45309' }};">
                                        {{ $m['raw_grow'] >= 0 ? '+' : '' }}{{ $m['raw_grow'] }}%
                                    </span>
                                @endif
                            </td>

                            <!-- Laba Kotor 1 & 2 & Delta -->
                            <td class="text-right" style="font-weight: 700; color: {{ $m['margin_1'] >= 0 ? '#0f172a' : '#b91c1c' }};">
                                {{ $m['margin_1'] != 0 ? number_format($m['margin_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right" style="color: #64748b;">
                                {{ $m['margin_2'] != 0 ? number_format($m['margin_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-center font-sans">
                                @if($m['margin_1'] == 0 && $m['margin_2'] == 0)
                                    <span style="color:#94a3b8;">-</span>
                                @else
                                    <span style="font-size: 7pt; font-weight: bold; color: {{ $m['margin_delta'] >= 0 ? '#047857' : '#b91c1c' }};">
                                        {{ $m['margin_grow'] >= 0 ? '+' : '' }}{{ $m['margin_grow'] }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="font-mono">
                    <tr>
                        <td class="text-center font-sans" style="font-weight: 900; text-transform: uppercase;">TOTAL TAHUNAN</td>
                        <!-- Total Penjualan -->
                        <td class="text-right font-bold">Rp {{ number_format($yoyMetrics['sales1'], 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #475569;">Rp {{ number_format($yoyMetrics['sales2'], 0, ',', '.') }}</td>
                        <td class="text-center font-sans" style="border-right: 1.5px solid #94a3b8;">
                            <span style="font-size: 7.5pt; font-weight: 900; color: {{ $yoyMetrics['sales_delta_pct'] >= 0 ? '#047857' : '#b91c1c' }};">
                                {{ $yoyMetrics['sales_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_pct'] }}%
                            </span>
                        </td>
                        <!-- Total Pembelian -->
                        <td class="text-right font-bold">Rp {{ number_format($yoyMetrics['raw1'], 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #475569;">Rp {{ number_format($yoyMetrics['raw2'], 0, ',', '.') }}</td>
                        <td class="text-center font-sans" style="border-right: 1.5px solid #94a3b8;">
                            <span style="font-size: 7.5pt; font-weight: 900; color: {{ $yoyMetrics['raw_delta_pct'] <= 0 ? '#047857' : '#b45309' }};">
                                {{ $yoyMetrics['raw_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_pct'] }}%
                            </span>
                        </td>
                        <!-- Total Laba Kotor -->
                        <td class="text-right font-bold">Rp {{ number_format($yoyMetrics['margin1'], 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #475569;">Rp {{ number_format($yoyMetrics['margin2'], 0, ',', '.') }}</td>
                        <td class="text-center font-sans">
                            <span style="font-size: 7.5pt; font-weight: 900; color: {{ $yoyMetrics['margin_delta_pct'] >= 0 ? '#047857' : '#b91c1c' }};">
                                {{ $yoyMetrics['margin_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['margin_delta_pct'] }}%
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Section 3: Histori Kinerja Multi-Tahun -->
            <div class="break-inside-avoid">
                <div class="section-header">
                    <span>III. Rekapitulasi Trayektori Kinerja Multi-Tahun (Historical Performance)</span>
                    <span class="tag">Audit Trail Tahunan</span>
                </div>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 8%;">Tahun</th>
                            <th class="text-right" style="width: 15%;">Omset Penjualan (Rp)</th>
                            <th class="text-right" style="width: 12%;">Volume (Kg)</th>
                            <th class="text-center" style="width: 10%;">YoY Sales</th>
                            <th class="text-right" style="width: 15%;">Pembelian Bahan (Rp)</th>
                            <th class="text-right" style="width: 12%;">Pasokan (Kg)</th>
                            <th class="text-center" style="width: 10%;">YoY Cost</th>
                            <th class="text-right" style="width: 14%;">Gross Profit (Rp)</th>
                            <th class="text-center" style="width: 8%;">Margin %</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono">
                        @foreach($multiYearSummary as $my)
                            <tr style="{{ $my['tahun'] == $tahun ? 'background-color: #fef3c7; font-weight: bold;' : '' }}">
                                <td class="text-center font-sans font-bold">
                                    {{ $my['tahun'] }}{{ $my['tahun'] == $tahun ? ' *' : '' }}
                                </td>
                                <td class="text-right">Rp {{ number_format($my['sales_rp'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($my['sales_qty'], 0, ',', '.') }}</td>
                                <td class="text-center font-sans">
                                    @if($my['sales_growth'] !== null)
                                        <span style="font-size: 7pt; font-weight: bold; color: {{ $my['sales_growth'] >= 0 ? '#047857' : '#b91c1c' }};">
                                            {{ $my['sales_growth'] >= 0 ? '+' : '' }}{{ $my['sales_growth'] }}%
                                        </span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 7pt;">Baseline</span>
                                    @endif
                                </td>
                                <td class="text-right">Rp {{ number_format($my['raw_rp'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($my['raw_qty'], 0, ',', '.') }}</td>
                                <td class="text-center font-sans">
                                    @if($my['raw_growth'] !== null)
                                        <span style="font-size: 7pt; font-weight: bold; color: {{ $my['raw_growth'] <= 0 ? '#047857' : '#b45309' }};">
                                            {{ $my['raw_growth'] >= 0 ? '+' : '' }}{{ $my['raw_growth'] }}%
                                        </span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 7pt;">Baseline</span>
                                    @endif
                                </td>
                                <td class="text-right font-bold">Rp {{ number_format($my['profit_rp'], 0, ',', '.') }}</td>
                                <td class="text-center font-sans">
                                    <span style="font-size: 7pt; font-weight: bold; color: {{ $my['margin_pct'] >= 40 ? '#047857' : ($my['margin_pct'] >= 20 ? '#b45309' : '#b91c1c') }};">
                                        {{ $my['margin_pct'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Section 4: Analisis Pareto & Umur Piutang (2 Columns) -->
            <div class="two-col-grid break-inside-avoid">
                <!-- Top 5 Pembeli -->
                <div>
                    <div class="section-header">
                        <span>IV.A. Top 5 Pembeli Terbesar</span>
                        <span class="tag">Pareto Sales</span>
                    </div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Nama Pelanggan</th>
                                <th class="text-right" style="width: 30%;">Total Omset</th>
                                <th class="text-center" style="width: 10%;">Order</th>
                                <th class="text-right" style="width: 25%;">Piutang</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($topBuyers as $b)
                                <tr>
                                    <td class="font-sans" style="font-weight: 600;">{{ $b->nama_buyer ?: $b->kode_customer }}</td>
                                    <td class="text-right font-bold">Rp {{ number_format($b->total_omset, 0, ',', '.') }}</td>
                                    <td class="text-center font-sans">{{ $b->frekuensi_beli }}x</td>
                                    <td class="text-right" style="color: {{ $b->sisa_piutang > 0 ? '#b91c1c' : '#047857' }};">
                                        {{ $b->sisa_piutang > 0 ? 'Rp ' . number_format($b->sisa_piutang, 0, ',', '.') : 'Lunas' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="color:#64748b; padding:8px;">Tidak ada data buyer</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Top 5 Pemasok -->
                <div>
                    <div class="section-header">
                        <span>IV.B. Top 5 Pemasok Bahan Baku</span>
                        <span class="tag">Pareto Supply</span>
                    </div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Nama Pemasok</th>
                                <th class="text-right" style="width: 30%;">Nilai Pasok</th>
                                <th class="text-center" style="width: 10%;">Trip</th>
                                <th class="text-right" style="width: 25%;">Hutang</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($topSuppliers as $s)
                                <tr>
                                    <td class="font-sans" style="font-weight: 600;">{{ $s->nama_pemasok ?: $s->kode_supplier }}</td>
                                    <td class="text-right font-bold">Rp {{ number_format($s->total_pasokan, 0, ',', '.') }}</td>
                                    <td class="text-center font-sans">{{ $s->frekuensi_pasok }}x</td>
                                    <td class="text-right" style="color: {{ $s->sisa_hutang > 0 ? '#b91c1c' : '#047857' }};">
                                        {{ $s->sisa_hutang > 0 ? 'Rp ' . number_format($s->sisa_hutang, 0, ',', '.') : 'Lunas' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="color:#64748b; padding:8px;">Tidak ada data supplier</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 5: Ringkasan Umur Piutang (AR Aging Matrix) -->
            <div class="break-inside-avoid">
                <div class="section-header">
                    <span>V. Analisis Umur Piutang Usaha (AR Aging Breakdown)</span>
                    <span class="tag">Kolektibilitas Faktur</span>
                </div>
                <div class="kpi-grid" style="margin-bottom: 8px;">
                    <div class="kpi-card" style="text-align: center;">
                        <span class="kpi-title" style="justify-content: center;">Lancar (0 - 15 Hari)</span>
                        <div class="kpi-main-val font-mono" style="color: #047857;">Rp {{ number_format($agingAR['current'], 0, ',', '.') }}</div>
                        <span style="font-size: 6.5pt; color: #64748b;">Jatuh tempo normal</span>
                    </div>
                    <div class="kpi-card" style="text-align: center;">
                        <span class="kpi-title" style="justify-content: center;">16 - 30 Hari</span>
                        <div class="kpi-main-val font-mono" style="color: #1d4ed8;">Rp {{ number_format($agingAR['day16_30'], 0, ',', '.') }}</div>
                        <span style="font-size: 6.5pt; color: #64748b;">Follow-up penagihan</span>
                    </div>
                    <div class="kpi-card" style="text-align: center;">
                        <span class="kpi-title" style="justify-content: center;">31 - 60 Hari</span>
                        <div class="kpi-main-val font-mono" style="color: #b45309;">Rp {{ number_format($agingAR['day31_60'], 0, ',', '.') }}</div>
                        <span style="font-size: 6.5pt; color: #64748b;">Peringatan 1 / SP-1</span>
                    </div>
                    <div class="kpi-card" style="text-align: center;">
                        <span class="kpi-title" style="justify-content: center;">&gt; 60 Hari (Macet/Kritis)</span>
                        <div class="kpi-main-val font-mono" style="color: #b91c1c;">Rp {{ number_format($agingAR['over60'], 0, ',', '.') }}</div>
                        <span style="font-size: 6.5pt; color: #64748b;">Risiko kerugian piutang</span>
                    </div>
                </div>
            </div>

            <!-- Lembar Pengesahan Resmi -->
            <div class="signature-section break-inside-avoid">
                <div class="signature-date">
                    {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ date('d F Y') }}
                </div>
                <div class="signature-grid">
                    <div class="signature-box">
                        <span class="signature-role">Dibuat Oleh (Staff Analisis &amp; Keuangan):</span>
                        <span class="signature-name">{{ auth()->user()->name ?? 'Staff Akuntansi' }}</span>
                        <span class="signature-title">Financial &amp; Business Analyst</span>
                    </div>
                    <div class="signature-box">
                        <span class="signature-role">Diperiksa Oleh (Manager Operasional):</span>
                        <span class="signature-name">( _________________________ )</span>
                        <span class="signature-title">Operational &amp; Factory Manager</span>
                    </div>
                    <div class="signature-box">
                        <span class="signature-role">Disetujui Oleh (Direktur / BOD):</span>
                        <span class="signature-name">( _________________________ )</span>
                        <span class="signature-title">Direktur Utama / Board of Directors</span>
                    </div>
                </div>
            </div>

            <!-- Footer Resmi -->
            <div class="doc-footer">
                Dokumen Laporan Analisis &amp; Business Intelligence ini dihasilkan secara otomatis oleh PBS-ERP Sistem Terpadu &bull; Sah dan otentik untuk keperluan evaluasi internal manajemen korporasi.
            </div>

        </div>
    </div>

    <script>
        let isLandscape = false;

        function toggleOrientation() {
            isLandscape = !isLandscape;
            const screenWrapper = document.getElementById('screenWrapper');
            const orientText = document.getElementById('orientText');
            const pageStyle = document.getElementById('pageStyle');

            if (isLandscape) {
                screenWrapper.classList.add('landscape');
                orientText.innerText = 'Portrait';
                pageStyle.innerHTML = '@page { size: A4 landscape; margin: 8mm 10mm 8mm 10mm; }';
            } else {
                screenWrapper.classList.remove('landscape');
                orientText.innerText = 'Landscape';
                pageStyle.innerHTML = '@page { size: A4 portrait; margin: 8mm 10mm 10mm 10mm; }';
            }
        }
    </script>
</body>
</html>
