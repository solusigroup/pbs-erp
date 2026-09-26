@php
    $d = $labaRugiPbs;
    $isKomp = isset($mode) && $mode === 'komparatif' && isset($labaRugiPbsKomparatif);
    $dKomp = $isKomp ? $labaRugiPbsKomparatif : null;

    $fmt = function($num) {
        return number_format($num, 0, ',', '.');
    };

    $diffHtml = function($key) use ($d, $dKomp, $fmt) {
        if (!$dKomp) return '';
        $val1 = $d[$key] ?? 0;
        $val2 = $dKomp[$key] ?? 0;
        $diff = $val1 - $val2;
        $cls = $diff >= 0 ? 'text-emerald-800' : 'text-rose-800 font-bold';
        $sign = $diff > 0 ? '+' : '';
        return '<td class="py-1 px-2 border border-slate-300 text-right font-mono text-[11px] ' . $cls . '">' . $sign . $fmt($diff) . '</td>';
    };
@endphp

<table class="w-full text-xs border-collapse border border-slate-400">
    <thead>
        <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-400 text-[11px]">
            <th class="py-1.5 px-2 border border-slate-300 text-left w-10">No</th>
            <th class="py-1.5 px-2 border border-slate-300 text-left w-12">Sub</th>
            <th class="py-1.5 px-3 border border-slate-300 text-left">Pos Rekening / Deskripsi Transaksi</th>
            <th class="py-1.5 px-2 border border-slate-300 text-right w-28">Rincian (Rp)</th>
            <th class="py-1.5 px-2 border border-slate-300 text-right w-28">Sub-Total (Rp)</th>
            <th class="py-1.5 px-2 border border-slate-300 text-right w-32 font-bold">Total (Rp)</th>
            @if($isKomp)
            <th class="py-1.5 px-2 border border-slate-300 text-right w-28 text-slate-600">Pembanding (Rp)</th>
            <th class="py-1.5 px-2 border border-slate-300 text-right w-24">Selisih</th>
            @endif
        </tr>
    </thead>
    <tbody class="text-slate-800">

        <!-- ========================================== -->
        <!-- A. PENJUALAN -->
        <!-- ========================================== -->
        <tr class="bg-slate-100 font-bold text-slate-900">
            <td class="py-1 px-2 border border-slate-300">A.</td>
            <td class="py-1 px-2 border border-slate-300"></td>
            <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-1 px-3 border border-slate-300 uppercase tracking-wide">
                PENJUALAN
            </td>
        </tr>

        <!-- A. 1. Penjualan Jasa (Green highlight) -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">A.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">1.</td>
            <td class="py-1 px-3 border border-slate-300">
                <span style="background-color: #d9ead3; padding: 2px 6px; border-radius: 3px; font-weight: 600;">
                    Penjualan Jasa
                </span>
            </td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['A1_penjualan_jasa']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['A1_penjualan_jasa']) }}</td>
            {!! $diffHtml('A1_penjualan_jasa') !!}
            @endif
        </tr>

        <!-- A. 2. Penjualan Barang (Green highlight) -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">A.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2.</td>
            <td class="py-1 px-3 border border-slate-300">
                <span style="background-color: #d9ead3; padding: 2px 6px; border-radius: 3px; font-weight: 600;">
                    Penjualan Barang
                </span>
            </td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['A2_penjualan_barang']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['A2_penjualan_barang']) }}</td>
            {!! $diffHtml('A2_penjualan_barang') !!}
            @endif
        </tr>

        <!-- A. 3. Diskon Penjualan -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">A.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">3.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">Diskon Penjualan</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-rose-700">({{ $fmt($d['A3_diskon_penjualan']) }})</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">({{ $fmt($dKomp['A3_diskon_penjualan']) }})</td>
            {!! $diffHtml('A3_diskon_penjualan') !!}
            @endif
        </tr>

        <!-- PENJUALAN BRUTO -->
        <tr class="bg-slate-50 font-bold">
            <td class="py-1 px-2 border border-slate-300"></td>
            <td class="py-1 px-2 border border-slate-300"></td>
            <td class="py-1 px-3 border border-slate-300 pl-8 uppercase">PENJUALAN BRUTO</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-bold">{{ $fmt($d['penjualan_bruto']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['penjualan_bruto']) }}</td>
            {!! $diffHtml('penjualan_bruto') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 6px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-white"></td></tr>

        <!-- A. 4. Penjualan Barang Bersih (Blue highlight) -->
        <tr class="font-bold">
            <td class="py-1.5 px-2 border border-slate-300 font-mono">A.</td>
            <td class="py-1.5 px-2 border border-slate-300 font-mono">4.</td>
            <td class="py-1.5 px-3 border border-slate-300">
                <span style="background-color: #cfe2f3; padding: 2px 8px; border-radius: 3px; font-weight: bold;">
                    Penjualan Barang Bersih
                </span>
            </td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold">{{ $fmt($d['A4_penjualan_barang_bersih']) }}</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold text-emerald-800">
                {{ $fmt($d['total_penjualan_bersih']) }}
            </td>
            @if($isKomp)
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold text-slate-700">{{ $fmt($dKomp['total_penjualan_bersih']) }}</td>
            {!! $diffHtml('total_penjualan_bersih') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 8px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-slate-50"></td></tr>


        <!-- ========================================== -->
        <!-- B. HARGA POKOK PENJUALAN -->
        <!-- ========================================== -->
        <tr class="bg-slate-100 font-bold text-slate-900">
            <td class="py-1 px-2 border border-slate-300">B.</td>
            <td class="py-1 px-2 border border-slate-300"></td>
            <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-1 px-3 border border-slate-300 uppercase tracking-wide underline">
                HARGA POKOK PENJUALAN
            </td>
        </tr>

        <!-- B. 1. PERSEDIAAN AWAL BRNG JADI -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">1.</td>
            <td class="py-1 px-3 border border-slate-300 font-semibold">PERSEDIAAN AWAL BRNG JADI</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B1_persediaan_awal_bj']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B1_persediaan_awal_bj']) }}</td>
            {!! $diffHtml('B1_persediaan_awal_bj') !!}
            @endif
        </tr>

        <!-- B. 2. HRG POKOK PRODUKSI Header -->
        <tr class="bg-slate-50 font-bold">
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2.</td>
            <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-1 px-3 border border-slate-300 uppercase underline">
                HRG POKOK PRODUKSI
            </td>
        </tr>

        <!-- B. 2. a. PERSEDIAAN AWAL BAHAN BAKU -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2. a.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">PERSEDIAAN AWAL BAHAN BAKU</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B2a_persediaan_awal_bb']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B2a_persediaan_awal_bb']) }}</td>
            {!! $diffHtml('B2a_persediaan_awal_bb') !!}
            @endif
        </tr>

        <!-- B. 2. b. PEMBELIAN BB -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2. b.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">PEMBELIAN BB</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B2b_pembelian_bb']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B2b_pembelian_bb']) }}</td>
            {!! $diffHtml('B2b_pembelian_bb') !!}
            @endif
        </tr>

        <!-- B. 2. c. ONGKOS ANGKUT PEMBELIAN+TIMBANG -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2. c.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">ONGKOS ANGKUT PEMBELIAN+TIMBANG</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B2c_ongkos_angkut_pembelian']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B2c_ongkos_angkut_pembelian']) }}</td>
            {!! $diffHtml('B2c_ongkos_angkut_pembelian') !!}
            @endif
        </tr>

        <!-- B. 2. d. DISKON PEMBELIAN BB -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2. d.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">DISKON PEMBELIAN BB</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-rose-700">({{ $fmt($d['B2d_diskon_pembelian_bb']) }})</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">({{ $fmt($dKomp['B2d_diskon_pembelian_bb']) }})</td>
            {!! $diffHtml('B2d_diskon_pembelian_bb') !!}
            @endif
        </tr>

        <!-- B. 2. f. TOTAL PEMBELIAN -->
        <tr class="bg-slate-50 font-bold">
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">2. f.</td>
            <td class="py-1 px-3 border border-slate-300 pl-10 uppercase underline">TOTAL PEMBELIAN</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-bold">{{ $fmt($d['B2f_total_pembelian']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B2f_total_pembelian']) }}</td>
            {!! $diffHtml('B2f_total_pembelian') !!}
            @endif
        </tr>

        <!-- (-) STOCK AKHIR BB -->
        <tr>
            <td class="py-1 px-2 border border-slate-300"></td>
            <td class="py-1 px-2 border border-slate-300"></td>
            <td class="py-1 px-3 border border-slate-300 pl-10">(-) STOCK AKHIR BB</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-rose-700">({{ $fmt($d['stock_akhir_bb']) }})</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">({{ $fmt($dKomp['stock_akhir_bb']) }})</td>
            {!! $diffHtml('stock_akhir_bb') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 6px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-white"></td></tr>

        <!-- B. 3. FOH-BIAYA TENAGA KERJA LANGSUNG -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">3.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">FOH-BIAYA TENAGA KERJA LANGSUNG</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B3_foh_btkl']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B3_foh_btkl']) }}</td>
            {!! $diffHtml('B3_foh_btkl') !!}
            @endif
        </tr>

        <!-- B. 4. FOH-LISTRIK -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">4.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">FOH-LISTRIK</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B4_foh_listrik']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B4_foh_listrik']) }}</td>
            {!! $diffHtml('B4_foh_listrik') !!}
            @endif
        </tr>

        <!-- B. 5. FOH-MAINTENANCE -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">5.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6">FOH-MAINTENANCE</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B5_foh_maintenance']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B5_foh_maintenance']) }}</td>
            {!! $diffHtml('B5_foh_maintenance') !!}
            @endif
        </tr>

        <!-- B. 6. TOTAL OVERHEAD PABRIK -->
        <tr class="bg-slate-50 font-bold">
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">6.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6 uppercase">TOTAL OVERHEAD PABRIK</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-bold">{{ $fmt($d['B6_total_overhead_pabrik']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B6_total_overhead_pabrik']) }}</td>
            {!! $diffHtml('B6_total_overhead_pabrik') !!}
            @endif
        </tr>

        <!-- B. 7. TOTAL HRG POKOK PRODUKSI -->
        <tr class="bg-slate-100 font-bold italic">
            <td class="py-1.5 px-2 border border-slate-300 font-mono">B.</td>
            <td class="py-1.5 px-2 border border-slate-300 font-mono">7.</td>
            <td class="py-1.5 px-3 border border-slate-300 pl-10 uppercase">TOTAL HRG POKOK PRODUKSI</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold">
                {{ $fmt($d['B7_total_hrg_pokok_produksi']) }}
            </td>
            @if($isKomp)
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-600 font-bold">{{ $fmt($dKomp['B7_total_hrg_pokok_produksi']) }}</td>
            {!! $diffHtml('B7_total_hrg_pokok_produksi') !!}
            @endif
        </tr>

        <!-- B. 8. ONGKOS ANGKUT PENJUALAN -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">8.</td>
            <td class="py-1 px-3 border border-slate-300 font-semibold">ONGKOS ANGKUT PENJUALAN</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B8_ongkos_angkut_penjualan']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B8_ongkos_angkut_penjualan']) }}</td>
            {!! $diffHtml('B8_ongkos_angkut_penjualan') !!}
            @endif
        </tr>

        <!-- B. 9. PERSEDIAAN AKHIR BRNG JADI -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">9.</td>
            <td class="py-1 px-3 border border-slate-300 font-semibold">PERSEDIAAN AKHIR BRNG JADI</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-rose-700">({{ $fmt($d['B9_persediaan_akhir_bj']) }})</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">({{ $fmt($dKomp['B9_persediaan_akhir_bj']) }})</td>
            {!! $diffHtml('B9_persediaan_akhir_bj') !!}
            @endif
        </tr>

        <!-- B. 10. KOMISI SALES (fee marketing) -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">10.</td>
            <td class="py-1 px-3 border border-slate-300 font-semibold">KOMISI SALES (fee marketing)</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B10_komisi_sales']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B10_komisi_sales']) }}</td>
            {!! $diffHtml('B10_komisi_sales') !!}
            @endif
        </tr>

        <!-- B. 11. KOMISI LAINNYA (ongkos kuli,satpam) -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">11.</td>
            <td class="py-1 px-3 border border-slate-300 font-semibold">KOMISI LAINNYA (ongkos kuli,satpam)</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono">{{ $fmt($d['B11_komisi_lainnya']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B11_komisi_lainnya']) }}</td>
            {!! $diffHtml('B11_komisi_lainnya') !!}
            @endif
        </tr>

        <!-- B. 12. BIAYA PENJUALAN & STOK AKHIR -->
        <tr class="bg-slate-50 font-bold">
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">B.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono">12.</td>
            <td class="py-1 px-3 border border-slate-300 pl-6 uppercase">BIAYA PENJUALAN &amp; STOK AKHIR</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-bold">{{ $fmt($d['B12_biaya_penjualan_stok_akhir']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['B12_biaya_penjualan_stok_akhir']) }}</td>
            {!! $diffHtml('B12_biaya_penjualan_stok_akhir') !!}
            @endif
        </tr>


        <!-- ========================================== -->
        <!-- C. TOTAL HRG POKOK PENJUALAN [COGS] -->
        <!-- ========================================== -->
        <tr class="bg-slate-100 font-bold italic border-t-2 border-b-2 border-slate-400">
            <td class="py-1.5 px-2 border border-slate-300 font-mono">C.</td>
            <td class="py-1.5 px-2 border border-slate-300 font-mono"></td>
            <td class="py-1.5 px-3 border border-slate-300 uppercase">TOTAL HRG POKOK PENJUALAN [COGS]</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold text-rose-800">
                {{ $fmt($d['C_total_cogs']) }}
            </td>
            @if($isKomp)
            <td class="py-1.5 px-2 border border-slate-300 text-right font-mono font-bold text-slate-700">{{ $fmt($dKomp['C_total_cogs']) }}</td>
            {!! $diffHtml('C_total_cogs') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 8px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-white"></td></tr>


        <!-- ========================================== -->
        <!-- D. LABA / RUGI BRUTO -->
        <!-- ========================================== -->
        <tr class="bg-slate-100 font-bold border-t-2 border-b-2 border-slate-500 text-[12px]">
            <td class="py-2 px-2 border border-slate-300 font-mono">D.</td>
            <td class="py-2 px-2 border border-slate-300 font-mono"></td>
            <td class="py-2 px-3 border border-slate-300 uppercase">
                LABA / <span style="color: red; font-weight: bold;">RUGI</span> BRUTO
            </td>
            <td class="py-2 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-2 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-2 px-2 border border-slate-300 text-right font-mono font-black {{ $d['is_laba_bruto'] ? 'text-emerald-800' : 'text-rose-600' }}">
                {{ $fmt($d['D_laba_rugi_bruto']) }}
            </td>
            @if($isKomp)
            <td class="py-2 px-2 border border-slate-300 text-right font-mono font-bold {{ $dKomp['is_laba_bruto'] ? 'text-emerald-800' : 'text-rose-600' }}">
                {{ $fmt($dKomp['D_laba_rugi_bruto']) }}
            </td>
            {!! $diffHtml('D_laba_rugi_bruto') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 8px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-white"></td></tr>


        <!-- ========================================== -->
        <!-- E. GAJI MANAJEMEN -->
        <!-- ========================================== -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">E.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono"></td>
            <td class="py-1 px-3 border border-slate-300 font-bold uppercase">GAJI MANAJEMEN</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-semibold">{{ $fmt($d['E_gaji_manajemen']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['E_gaji_manajemen']) }}</td>
            {!! $diffHtml('E_gaji_manajemen') !!}
            @endif
        </tr>


        <!-- ========================================== -->
        <!-- F. BIAYA ADMINISTRASI DAN UMUM -->
        <!-- ========================================== -->
        <tr>
            <td class="py-1 px-2 border border-slate-300 font-mono text-slate-600">F.</td>
            <td class="py-1 px-2 border border-slate-300 font-mono"></td>
            <td class="py-1 px-3 border border-slate-300 font-bold uppercase">BIAYA ADMINISTRASI DAN UMUM</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono font-semibold">{{ $fmt($d['F_biaya_administrasi_umum']) }}</td>
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-400">-</td>
            @if($isKomp)
            <td class="py-1 px-2 border border-slate-300 text-right font-mono text-slate-600">{{ $fmt($dKomp['F_biaya_administrasi_umum']) }}</td>
            {!! $diffHtml('F_biaya_administrasi_umum') !!}
            @endif
        </tr>

        <!-- Empty Row Separator -->
        <tr style="height: 8px;"><td colspan="{{ $isKomp ? 8 : 6 }}" class="border border-slate-300 bg-white"></td></tr>


        <!-- ========================================== -->
        <!-- G. NET INCOME (DEFISIT / RUGI) -->
        <!-- ========================================== -->
        <tr class="bg-slate-200 font-black border-t-2 border-b-4 border-double border-slate-900 text-[13px]">
            <td class="py-2.5 px-2 border border-slate-400 font-mono">G.</td>
            <td class="py-2.5 px-2 border border-slate-400 font-mono"></td>
            <td class="py-2.5 px-3 border border-slate-400 uppercase">
                NET INCOME <span style="color: red; font-weight: bold;">(DEFISIT / RUGI)</span>
            </td>
            <td class="py-2.5 px-2 border border-slate-400 text-right font-mono text-slate-400">-</td>
            <td class="py-2.5 px-2 border border-slate-400 text-right font-mono text-slate-400">-</td>
            <td class="py-2.5 px-2 border border-slate-400 text-right font-mono font-black {{ $d['is_net_laba'] ? 'text-emerald-900' : 'text-rose-700' }}">
                Rp {{ $fmt($d['G_net_income']) }}
            </td>
            @if($isKomp)
            <td class="py-2.5 px-2 border border-slate-400 text-right font-mono font-black {{ $dKomp['is_net_laba'] ? 'text-emerald-900' : 'text-rose-700' }}">
                Rp {{ $fmt($dKomp['G_net_income']) }}
            </td>
            {!! $diffHtml('G_net_income') !!}
            @endif
        </tr>

    </tbody>
</table>
