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
        $cls = $diff >= 0 ? 'text-emerald-400 font-semibold' : 'text-rose-400 font-semibold';
        $sign = $diff > 0 ? '+' : '';
        return '<td class="py-1.5 px-3 text-right font-mono text-xs ' . $cls . '">' . $sign . $fmt($diff) . '</td>';
    };
@endphp

<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-xs border-collapse border border-slate-700/60 dark:border-slate-800 bg-slate-950/40 rounded-xl">
        <thead>
            <tr class="bg-slate-800/90 text-slate-200 uppercase tracking-wider text-[11px] font-bold border-b border-slate-700">
                <th class="py-2.5 px-3 text-left w-12 border-r border-slate-700/60">No</th>
                <th class="py-2.5 px-3 text-left w-16 border-r border-slate-700/60">Sub</th>
                <th class="py-2.5 px-4 text-left">Pos Rekening / Deskripsi Transaksi</th>
                <th class="py-2.5 px-3 text-right w-36 border-l border-slate-700/60">Rincian (Rp)</th>
                <th class="py-2.5 px-3 text-right w-36 border-l border-slate-700/60">Sub-Total (Rp)</th>
                <th class="py-2.5 px-3 text-right w-40 border-l border-slate-700/60 font-black text-amber-400">Total (Rp)</th>
                @if($isKomp)
                <th class="py-2.5 px-3 text-right w-36 border-l border-slate-700/60 text-slate-400">Pembanding (Rp)</th>
                <th class="py-2.5 px-3 text-right w-28 border-l border-slate-700/60 text-amber-400">Selisih</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 text-slate-300 font-medium">

            <!-- ========================================== -->
            <!-- A. PENJUALAN -->
            <!-- ========================================== -->
            <tr class="bg-slate-900/90 font-black text-white text-xs border-t border-slate-700">
                <td class="py-2 px-3 border-r border-slate-800">A.</td>
                <td class="py-2 px-3 border-r border-slate-800"></td>
                <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-2 px-4 uppercase tracking-wider text-amber-400">
                    PENJUALAN
                </td>
            </tr>

            <!-- A. 1. Penjualan Jasa (Green highlight) -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">A.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">1.</td>
                <td class="py-1.5 px-4">
                    <span class="inline-block px-2.5 py-0.5 rounded font-semibold text-emerald-950 bg-emerald-200 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-400/30">
                        Penjualan Jasa
                    </span>
                    <span class="text-[10px] text-slate-500 ml-2">(Pendapatan Jasa Cugil / Maklon - Akun 4-1200)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['A1_penjualan_jasa']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['A1_penjualan_jasa']) }}</td>
                {!! $diffHtml('A1_penjualan_jasa') !!}
                @endif
            </tr>

            <!-- A. 2. Penjualan Barang (Green highlight) -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">A.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2.</td>
                <td class="py-1.5 px-4">
                    <span class="inline-block px-2.5 py-0.5 rounded font-semibold text-emerald-950 bg-emerald-200 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-400/30">
                        Penjualan Barang
                    </span>
                    <span class="text-[10px] text-slate-500 ml-2">(Penjualan Kotor Gilingan CUGIL - Bruto)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['A2_penjualan_barang']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['A2_penjualan_barang']) }}</td>
                {!! $diffHtml('A2_penjualan_barang') !!}
                @endif
            </tr>

            <!-- A. 3. Diskon Penjualan -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">A.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">3.</td>
                <td class="py-1.5 px-4 pl-6 text-slate-300">
                    Diskon Penjualan <span class="text-[10px] text-slate-500 ml-1">(Potongan Penjualan / Rafaksi)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-rose-400">({{ $fmt($d['A3_diskon_penjualan']) }})</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">({{ $fmt($dKomp['A3_diskon_penjualan']) }})</td>
                {!! $diffHtml('A3_diskon_penjualan') !!}
                @endif
            </tr>

            <!-- PENJUALAN BRUTO -->
            <tr class="bg-slate-900/60 font-bold text-white">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500"></td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono"></td>
                <td class="py-1.5 px-4 pl-10 uppercase tracking-wide text-slate-200">
                    PENJUALAN BRUTO <span class="text-[10px] text-slate-400 font-normal">(A.1 + A.2)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 font-bold text-white">{{ $fmt($d['penjualan_bruto']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['penjualan_bruto']) }}</td>
                {!! $diffHtml('penjualan_bruto') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-2 bg-slate-950/20"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>

            <!-- A. 4. Penjualan Barang Bersih (Blue highlight) -->
            <tr class="bg-sky-950/20 font-bold border-y border-sky-500/20">
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-sky-400">A.</td>
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-sky-400">4.</td>
                <td class="py-2 px-4">
                    <span class="inline-block px-3 py-1 rounded font-bold text-sky-950 bg-sky-200 dark:bg-sky-900/70 dark:text-sky-200 border border-sky-400/40">
                        Penjualan Barang Bersih
                    </span>
                    <span class="text-[11px] text-sky-300/80 ml-2">(Penjualan Barang - Diskon Penjualan)</span>
                </td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 font-bold text-sky-300">{{ $fmt($d['A4_penjualan_barang_bersih']) }}</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 font-black text-emerald-400 text-sm">
                    {{ $fmt($d['total_penjualan_bersih']) }}
                </td>
                @if($isKomp)
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-300 font-bold">{{ $fmt($dKomp['total_penjualan_bersih']) }}</td>
                {!! $diffHtml('total_penjualan_bersih') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-3 bg-slate-950/40"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>


            <!-- ========================================== -->
            <!-- B. HARGA POKOK PENJUALAN -->
            <!-- ========================================== -->
            <tr class="bg-slate-900/90 font-black text-white text-xs border-t border-slate-700">
                <td class="py-2 px-3 border-r border-slate-800">B.</td>
                <td class="py-2 px-3 border-r border-slate-800"></td>
                <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-2 px-4 uppercase tracking-wider text-amber-400 underline underline-offset-4">
                    HARGA POKOK PENJUALAN
                </td>
            </tr>

            <!-- B. 1. PERSEDIAAN AWAL BRNG JADI -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">1.</td>
                <td class="py-1.5 px-4 font-semibold text-slate-200">
                    PERSEDIAAN AWAL BRNG JADI
                    <span class="text-[10px] text-slate-500 ml-2">(Stok Awal Produk Jadi Cuci Giling)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B1_persediaan_awal_bj']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B1_persediaan_awal_bj']) }}</td>
                {!! $diffHtml('B1_persediaan_awal_bj') !!}
                @endif
            </tr>

            <!-- B. 2. HRG POKOK PRODUKSI Header -->
            <tr class="bg-slate-900/40 font-bold text-slate-200">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2.</td>
                <td colspan="{{ $isKomp ? 6 : 4 }}" class="py-1.5 px-4 uppercase font-bold text-slate-200 underline underline-offset-2">
                    HRG POKOK PRODUKSI
                </td>
            </tr>

            <!-- B. 2. a. PERSEDIAAN AWAL BAHAN BAKU -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2. a.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    PERSEDIAAN AWAL BAHAN BAKU <span class="text-[10px] text-slate-500 ml-1">(Stok Awal Mentah Kresek/Campur)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B2a_persediaan_awal_bb']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B2a_persediaan_awal_bb']) }}</td>
                {!! $diffHtml('B2a_persediaan_awal_bb') !!}
                @endif
            </tr>

            <!-- B. 2. b. PEMBELIAN BB -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2. b.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    PEMBELIAN BB <span class="text-[10px] text-slate-500 ml-1">(Total Pembelian Kotor Bahan Mentah)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B2b_pembelian_bb']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B2b_pembelian_bb']) }}</td>
                {!! $diffHtml('B2b_pembelian_bb') !!}
                @endif
            </tr>

            <!-- B. 2. c. ONGKOS ANGKUT PEMBELIAN+TIMBANG -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2. c.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    ONGKOS ANGKUT PEMBELIAN+TIMBANG <span class="text-[10px] text-slate-500 ml-1">(Biaya Truk & Timbangan Pembelian)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B2c_ongkos_angkut_pembelian']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B2c_ongkos_angkut_pembelian']) }}</td>
                {!! $diffHtml('B2c_ongkos_angkut_pembelian') !!}
                @endif
            </tr>

            <!-- B. 2. d. DISKON PEMBELIAN BB -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2. d.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    DISKON PEMBELIAN BB <span class="text-[10px] text-slate-500 ml-1">(Rafaksi & Potongan Pembelian Bahan)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-rose-400">({{ $fmt($d['B2d_diskon_pembelian_bb']) }})</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">({{ $fmt($dKomp['B2d_diskon_pembelian_bb']) }})</td>
                {!! $diffHtml('B2d_diskon_pembelian_bb') !!}
                @endif
            </tr>

            <!-- B. 2. f. TOTAL PEMBELIAN -->
            <tr class="bg-slate-900/60 font-bold">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">2. f.</td>
                <td class="py-1.5 px-4 pl-12 font-bold text-white uppercase underline underline-offset-2">
                    TOTAL PEMBELIAN
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 font-bold text-white">{{ $fmt($d['B2f_total_pembelian']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B2f_total_pembelian']) }}</td>
                {!! $diffHtml('B2f_total_pembelian') !!}
                @endif
            </tr>

            <!-- (-) STOCK AKHIR BB -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500"></td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono"></td>
                <td class="py-1.5 px-4 pl-12 text-slate-300">
                    (-) STOCK AKHIR BB <span class="text-[10px] text-slate-500 ml-1">(Sisa Stok Bahan Baku di Pabrik)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-rose-400">({{ $fmt($d['stock_akhir_bb']) }})</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">({{ $fmt($dKomp['stock_akhir_bb']) }})</td>
                {!! $diffHtml('stock_akhir_bb') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-2 bg-slate-950/20"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>

            <!-- B. 3. FOH-BIAYA TENAGA KERJA LANGSUNG -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">3.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    FOH-BIAYA TENAGA KERJA LANGSUNG <span class="text-[10px] text-slate-500 ml-1">(Upah Borongan / Pekerja Pabrik Cuci Giling)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B3_foh_btkl']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B3_foh_btkl']) }}</td>
                {!! $diffHtml('B3_foh_btkl') !!}
                @endif
            </tr>

            <!-- B. 4. FOH-LISTRIK -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">4.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    FOH-LISTRIK <span class="text-[10px] text-slate-500 ml-1">(Beban Listrik Operasional Pabrik & Mesin Giling)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B4_foh_listrik']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B4_foh_listrik']) }}</td>
                {!! $diffHtml('B4_foh_listrik') !!}
                @endif
            </tr>

            <!-- B. 5. FOH-MAINTENANCE -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">5.</td>
                <td class="py-1.5 px-4 pl-8 text-slate-300">
                    FOH-MAINTENANCE <span class="text-[10px] text-slate-500 ml-1">(Perbaikan & Perawatan Mesin / Sparepart / Solar)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B5_foh_maintenance']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B5_foh_maintenance']) }}</td>
                {!! $diffHtml('B5_foh_maintenance') !!}
                @endif
            </tr>

            <!-- B. 6. TOTAL OVERHEAD PABRIK -->
            <tr class="bg-slate-900/60 font-bold">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">6.</td>
                <td class="py-1.5 px-4 pl-8 uppercase font-bold text-slate-200">
                    TOTAL OVERHEAD PABRIK
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 font-bold text-slate-200">{{ $fmt($d['B6_total_overhead_pabrik']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B6_total_overhead_pabrik']) }}</td>
                {!! $diffHtml('B6_total_overhead_pabrik') !!}
                @endif
            </tr>

            <!-- B. 7. TOTAL HRG POKOK PRODUKSI -->
            <tr class="bg-slate-900/80 font-black italic border-y border-slate-700">
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-amber-400">B.</td>
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-amber-400">7.</td>
                <td class="py-2 px-4 pl-12 uppercase text-amber-300 tracking-wide">
                    TOTAL HRG POKOK PRODUKSI <span class="text-[10px] font-normal not-italic text-slate-400">(Bahan Baku Digunakan + Total Overhead)</span>
                </td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 font-black text-amber-300 text-sm">
                    {{ $fmt($d['B7_total_hrg_pokok_produksi']) }}
                </td>
                @if($isKomp)
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-300 font-bold">{{ $fmt($dKomp['B7_total_hrg_pokok_produksi']) }}</td>
                {!! $diffHtml('B7_total_hrg_pokok_produksi') !!}
                @endif
            </tr>

            <!-- B. 8. ONGKOS ANGKUT PENJUALAN -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">8.</td>
                <td class="py-1.5 px-4 font-semibold text-slate-200">
                    ONGKOS ANGKUT PENJUALAN <span class="text-[10px] text-slate-500 ml-1">(Biaya Pengiriman Produk ke Pembeli)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B8_ongkos_angkut_penjualan']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B8_ongkos_angkut_penjualan']) }}</td>
                {!! $diffHtml('B8_ongkos_angkut_penjualan') !!}
                @endif
            </tr>

            <!-- B. 9. PERSEDIAAN AKHIR BRNG JADI -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">9.</td>
                <td class="py-1.5 px-4 font-semibold text-slate-200">
                    PERSEDIAAN AKHIR BRNG JADI <span class="text-[10px] text-slate-500 ml-1">(Stok Akhir Produk Hasil Olah Cuci Giling)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-rose-400">({{ $fmt($d['B9_persediaan_akhir_bj']) }})</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">({{ $fmt($dKomp['B9_persediaan_akhir_bj']) }})</td>
                {!! $diffHtml('B9_persediaan_akhir_bj') !!}
                @endif
            </tr>

            <!-- B. 10. KOMISI SALES (fee marketing) -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">10.</td>
                <td class="py-1.5 px-4 font-semibold text-slate-200">
                    KOMISI SALES (fee marketing) <span class="text-[10px] text-slate-500 ml-1">(Fee Broker & Makelar Penjualan)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B10_komisi_sales']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B10_komisi_sales']) }}</td>
                {!! $diffHtml('B10_komisi_sales') !!}
                @endif
            </tr>

            <!-- B. 11. KOMISI LAINNYA (ongkos kuli,satpam) -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">11.</td>
                <td class="py-1.5 px-4 font-semibold text-slate-200">
                    KOMISI LAINNYA (ongkos kuli,satpam) <span class="text-[10px] text-slate-500 ml-1">(Biaya Kuli Muat & Keamanan Pengiriman)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800">{{ $fmt($d['B11_komisi_lainnya']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B11_komisi_lainnya']) }}</td>
                {!! $diffHtml('B11_komisi_lainnya') !!}
                @endif
            </tr>

            <!-- B. 12. BIAYA PENJUALAN & STOK AKHIR -->
            <tr class="bg-slate-900/60 font-bold">
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono text-slate-500">B.</td>
                <td class="py-1.5 px-3 border-r border-slate-800 font-mono">12.</td>
                <td class="py-1.5 px-4 pl-8 uppercase font-bold text-slate-200">
                    BIAYA PENJUALAN &amp; STOK AKHIR <span class="text-[10px] text-slate-400 font-normal">(Ongkos Angkut + Komisi - Stok Akhir BJ)</span>
                </td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 font-bold text-slate-200">{{ $fmt($d['B12_biaya_penjualan_stok_akhir']) }}</td>
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-1.5 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['B12_biaya_penjualan_stok_akhir']) }}</td>
                {!! $diffHtml('B12_biaya_penjualan_stok_akhir') !!}
                @endif
            </tr>


            <!-- ========================================== -->
            <!-- C. TOTAL HRG POKOK PENJUALAN [COGS] -->
            <!-- ========================================== -->
            <tr class="bg-slate-900 font-black italic border-y-2 border-slate-600">
                <td class="py-2.5 px-3 border-r border-slate-800 font-mono text-rose-400">C.</td>
                <td class="py-2.5 px-3 border-r border-slate-800 font-mono text-rose-400"></td>
                <td class="py-2.5 px-4 uppercase text-rose-300 tracking-wider">
                    TOTAL HRG POKOK PENJUALAN [COGS]
                </td>
                <td class="py-2.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2.5 px-3 text-right font-mono border-l border-slate-800 font-black text-rose-400 text-sm">
                    {{ $fmt($d['C_total_cogs']) }}
                </td>
                @if($isKomp)
                <td class="py-2.5 px-3 text-right font-mono border-l border-slate-800 text-slate-300 font-bold">{{ $fmt($dKomp['C_total_cogs']) }}</td>
                {!! $diffHtml('C_total_cogs') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-3 bg-slate-950/40"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>


            <!-- ========================================== -->
            <!-- D. LABA / RUGI BRUTO -->
            <!-- ========================================== -->
            <tr class="bg-slate-900/90 font-black border-y-2 border-amber-500/40 text-sm">
                <td class="py-3 px-3 border-r border-slate-800 font-mono text-amber-400">D.</td>
                <td class="py-3 px-3 border-r border-slate-800 font-mono text-amber-400"></td>
                <td class="py-3 px-4 uppercase tracking-wider">
                    <span class="text-white">LABA / </span><span class="text-rose-500 font-black">RUGI</span><span class="text-white"> BRUTO</span>
                    <span class="text-[11px] font-normal text-slate-400 not-italic ml-2">(Penjualan Bersih - Total COGS)</span>
                </td>
                <td class="py-3 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-3 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-3 px-3 text-right font-mono border-l border-slate-800 font-black text-base {{ $d['is_laba_bruto'] ? 'text-emerald-400' : 'text-rose-500' }}">
                    {{ $fmt($d['D_laba_rugi_bruto']) }}
                </td>
                @if($isKomp)
                <td class="py-3 px-3 text-right font-mono border-l border-slate-800 font-bold {{ $dKomp['is_laba_bruto'] ? 'text-emerald-400' : 'text-rose-500' }}">
                    {{ $fmt($dKomp['D_laba_rugi_bruto']) }}
                </td>
                {!! $diffHtml('D_laba_rugi_bruto') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-3 bg-slate-950/40"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>


            <!-- ========================================== -->
            <!-- E. GAJI MANAJEMEN -->
            <!-- ========================================== -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-slate-500">E.</td>
                <td class="py-2 px-3 border-r border-slate-800 font-mono"></td>
                <td class="py-2 px-4 font-bold text-slate-200 uppercase tracking-wide">
                    GAJI MANAJEMEN <span class="text-[10px] text-slate-500 font-normal ml-1">(Gaji Direksi, Pimpinan & Staf Manajemen Kantor)</span>
                </td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 font-semibold text-slate-200">{{ $fmt($d['E_gaji_manajemen']) }}</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['E_gaji_manajemen']) }}</td>
                {!! $diffHtml('E_gaji_manajemen') !!}
                @endif
            </tr>


            <!-- ========================================== -->
            <!-- F. BIAYA ADMINISTRASI DAN UMUM -->
            <!-- ========================================== -->
            <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-2 px-3 border-r border-slate-800 font-mono text-slate-500">F.</td>
                <td class="py-2 px-3 border-r border-slate-800 font-mono"></td>
                <td class="py-2 px-4 font-bold text-slate-200 uppercase tracking-wide">
                    BIAYA ADMINISTRASI DAN UMUM <span class="text-[10px] text-slate-500 font-normal ml-1">(ATK, Operasional Kantor, Pajak, Perizinan, Perjalanan)</span>
                </td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 font-semibold text-slate-200">{{ $fmt($d['F_biaya_administrasi_umum']) }}</td>
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                @if($isKomp)
                <td class="py-2 px-3 text-right font-mono border-l border-slate-800 text-slate-400">{{ $fmt($dKomp['F_biaya_administrasi_umum']) }}</td>
                {!! $diffHtml('F_biaya_administrasi_umum') !!}
                @endif
            </tr>

            <!-- Empty Row Separator -->
            <tr class="h-3 bg-slate-950/40"><td colspan="{{ $isKomp ? 8 : 6 }}"></td></tr>


            <!-- ========================================== -->
            <!-- G. NET INCOME (DEFISIT / RUGI) -->
            <!-- ========================================== -->
            <tr class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 font-black border-y-4 border-double border-amber-500 text-sm">
                <td class="py-3.5 px-3 border-r border-slate-800 font-mono text-amber-400">G.</td>
                <td class="py-3.5 px-3 border-r border-slate-800 font-mono text-amber-400"></td>
                <td class="py-3.5 px-4 uppercase tracking-wider">
                    <span class="text-white text-base">NET INCOME </span>
                    <span class="text-rose-500 font-black text-base">(DEFISIT / RUGI)</span>
                    <span class="text-[11px] font-normal text-slate-400 ml-2 not-italic">(Laba Bruto - Gaji Manajemen - Biaya Admin & Umum)</span>
                </td>
                <td class="py-3.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-3.5 px-3 text-right font-mono border-l border-slate-800 text-slate-600">-</td>
                <td class="py-3.5 px-3 text-right font-mono border-l border-slate-800 font-black text-lg {{ $d['is_net_laba'] ? 'text-emerald-400' : 'text-rose-500' }}">
                    Rp {{ $fmt($d['G_net_income']) }}
                </td>
                @if($isKomp)
                <td class="py-3.5 px-3 text-right font-mono border-l border-slate-800 font-black text-base {{ $dKomp['is_net_laba'] ? 'text-emerald-400' : 'text-rose-500' }}">
                    Rp {{ $fmt($dKomp['G_net_income']) }}
                </td>
                {!! $diffHtml('G_net_income') !!}
                @endif
            </tr>

        </tbody>
    </table>
</div>
