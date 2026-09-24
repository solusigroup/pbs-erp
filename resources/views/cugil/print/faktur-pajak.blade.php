<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pajak - {{ $sale->id_penjualan }} - PT Pinastika Bhakti Semesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 9.5pt; }
            @page { size: A4 portrait; margin: 8mm 10mm; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('cugil.sales.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Penjualan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('cugil.sales.so', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-amber-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-file-contract text-xs"></i>
                <span>Sales Order</span>
            </a>
            <a href="{{ route('cugil.sales.surat-jalan', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-truck text-xs"></i>
                <span>Surat Jalan</span>
            </a>
            <a href="{{ route('cugil.sales.invoice', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-file-invoice-dollar text-xs"></i>
                <span>Invoice</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- Paper Canvas -->
    <div class="max-w-4xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Official Tax Status Notice -->
        <div class="border border-purple-300 bg-purple-50 text-purple-950 px-4 py-2 rounded-xl mb-4 text-[11px] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-info text-purple-700"></i>
                <span><strong>Status Perpajakan:</strong> Nomor Pengukuhan PKP PT Pinastika Bhakti Semesta dalam proses penetapan KPP Pratama Mojokerto.</span>
            </div>
            <span class="font-bold uppercase tracking-wider text-[10px] bg-purple-200 px-2 py-0.5 rounded text-purple-900">
                PROFORMA e-FAKTUR PPN
            </span>
        </div>

        <!-- Document Header Standard DJP -->
        <div class="border border-slate-950 p-3 text-center bg-slate-100">
            <h1 class="text-base font-black tracking-tight text-slate-950 uppercase">FAKTUR PAJAK</h1>
            <div class="text-xs font-mono font-bold mt-1 text-slate-900">
                Kode dan Nomor Seri Faktur Pajak: <span class="text-purple-900 font-black">030.000-26.{{ str_pad($sale->id, 8, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- Taxpayers Information Grid -->
        <div class="border-x border-b border-slate-950 text-xs">
            
            <!-- Pengusaha Kena Pajak (Penjual) -->
            <div class="p-3 border-b border-slate-300">
                <h3 class="font-bold text-slate-950 uppercase text-[11px] mb-2">Pengusaha Kena Pajak</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-600 py-0.5">Nama</td>
                        <td class="font-bold text-slate-950">: {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-600 py-0.5">Alamat</td>
                        <td class="text-slate-800">: {{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-600 py-0.5">NPWP</td>
                        <td class="font-mono font-bold text-slate-950">: {{ $perusahaan->npwp ?? '01.234.567.8-602.000' }} <span class="text-[10px] font-normal text-purple-700">(Status PKP: Dalam Proses Pengajuan)</span></td>
                    </tr>
                </table>
            </div>

            <!-- Pembeli BKP / Penerima JKP (Klien / Pemungut WAPU SIG) -->
            <div class="p-3 bg-slate-50/70">
                <h3 class="font-bold text-slate-950 uppercase text-[11px] mb-2">Pembeli Barang Kena Pajak / Penerima Jasa Kena Pajak</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-600 py-0.5">Nama</td>
                        <td class="font-bold text-slate-950">: {{ $sale->nama_buyer ?? ($sale->customer->nama_customer ?? 'PT Semen Indonesia (Persero) Tbk') }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-600 py-0.5">Alamat</td>
                        <td class="text-slate-800">: {{ $sale->customer->alamat ?? 'Gedung Utama Semen Indonesia, Pabrik Tuban, Desa Sumberarum, Kec. Kerek, Kab. Tuban, Jawa Timur' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-600 py-0.5">NPWP</td>
                        <td class="font-mono font-bold text-slate-950">: {{ $sale->customer->npwp ?? '01.001.609.5-092.000' }} <span class="text-[10px] font-bold text-indigo-700">(BUMN Pemungut WAPU)</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Line Items Table Standard DJP -->
        <div class="border-x border-b border-slate-950">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-950 uppercase font-bold text-[9.5px] border-b border-slate-950">
                        <th class="py-2 px-2 text-center w-8 border-r border-slate-400">No.</th>
                        <th class="py-2 px-3 border-r border-slate-400">Nama Barang Kena Pajak / Jasa Kena Pajak</th>
                        <th class="py-2 px-3 text-right border-r border-slate-400 w-32">Harga Jual / Penggantian (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sale->items && $sale->items->count() > 0)
                        @foreach($sale->items as $idx => $it)
                            <tr class="border-b border-slate-200 {{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="py-2.5 px-2 text-center text-slate-700 border-r border-slate-300">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3 border-r border-slate-300">
                                    <strong class="text-slate-950">{{ $it->nama_barang }}</strong>
                                    <div class="text-[10px] text-slate-600 font-mono">
                                        Volume: {{ number_format($it->qty_terjual, 1, ',', '.') }} Kg @ Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-950 border-r border-slate-300">
                                    {{ number_format($it->harga_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="border-b border-slate-200">
                            <td class="py-2.5 px-2 text-center text-slate-700 border-r border-slate-300">1</td>
                            <td class="py-2.5 px-3 border-r border-slate-300">
                                <strong class="text-slate-950">{{ $sale->nama_barang ?? 'Pasokan Bahan Bakar Alternatif RDF' }}</strong>
                                <div class="text-[10px] text-slate-600 font-mono">
                                    Volume: {{ number_format($sale->total_qty, 1, ',', '.') }} Kg @ Rp {{ number_format($sale->total_bruto > 0 && $sale->total_qty > 0 ? $sale->total_bruto / $sale->total_qty : 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-950 border-r border-slate-300">
                                {{ number_format($sale->total_bruto, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Tax Calculation Summary (DJP Standard Box) -->
        @php
            $dpp = (float)$sale->tagihan;
            $ppn = round($dpp * 0.11);
            $totalBruto = (float)$sale->total_bruto;
            $potongan = (float)$sale->total_diskon;
        @endphp

        <div class="border-x border-b border-slate-950 text-xs">
            <table class="w-full text-xs">
                <tr class="border-b border-slate-200">
                    <td class="py-1.5 px-3 font-semibold text-slate-800">Harga Jual / Penggantian</td>
                    <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-950 w-44">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-slate-200">
                    <td class="py-1.5 px-3 text-slate-700">Dikurangi Potongan Harga</td>
                    <td class="py-1.5 px-3 text-right font-mono text-amber-700 w-44">{{ $potongan > 0 ? 'Rp '.number_format($potongan, 0, ',', '.') : 'Rp 0' }}</td>
                </tr>
                <tr class="border-b border-slate-200">
                    <td class="py-1.5 px-3 text-slate-700">Dikurangi Uang Muka yang telah diterima</td>
                    <td class="py-1.5 px-3 text-right font-mono text-slate-700 w-44">{{ $sale->down_payment > 0 ? 'Rp '.number_format($sale->down_payment, 0, ',', '.') : 'Rp 0' }}</td>
                </tr>
                <tr class="border-b-2 border-slate-950 bg-slate-50 font-bold">
                    <td class="py-2 px-3 text-slate-950 uppercase">Dasar Pengenaan Pajak (DPP)</td>
                    <td class="py-2 px-3 text-right font-mono text-sm text-slate-950 w-44">Rp {{ number_format($dpp, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-slate-200 bg-purple-50/50">
                    <td class="py-2 px-3 font-bold text-purple-950">
                        Pajak Pertambahan Nilai (PPN) = 11% x DPP
                        <div class="text-[10px] text-purple-800 font-normal">
                            * Keterangan: PPN DIPUNGUT OLEH PEMUNGUT BUMN (PT SEMEN INDONESIA (PERSERO) TBK)
                        </div>
                    </td>
                    <td class="py-2 px-3 text-right font-mono font-bold text-purple-950 text-sm w-44">
                        Rp {{ number_format($ppn, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Dynamic Legal Statement & Signature -->
        @php
            $taxSignatures = \App\Models\DocumentSignature::forDoc('faktur_pajak');
            $taxSig = $taxSignatures->first();
        @endphp

        <div class="grid grid-cols-2 gap-6 pt-6 text-xs items-end">
            <div class="text-[10.5px] text-slate-500 leading-relaxed">
                <p><strong>Catatan Perpajakan:</strong></p>
                <p>1. Dokumen ini disiapkan sebagai dasar pencatatan perpajakan resmi transaksi pasokan RDF ke PT Semen Indonesia Group.</p>
                <p>2. Bukti Pemungutan PPN (NTPN/BPU) dari WAPU BUMN akan direkonsiliasikan pada SPT Masa PPN PT PBS.</p>
            </div>

            <div class="text-center">
                <p class="text-xs text-slate-700 mb-1">{{ $taxSig->catatan ?? 'Mojokerto' }}, {{ $sale->tanggal ? $sale->tanggal->format('d F Y') : date('d F Y') }}</p>
                <strong class="block text-slate-900 uppercase font-bold text-xs mb-14">{{ $taxSig->organisasi ?? ($perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA') }}</strong>

                <div class="inline-block border-t-2 border-slate-900 pt-1 text-center w-64">
                    <strong class="block text-slate-950 font-black text-xs">{{ $taxSig->nama_penandatangan ?? ($perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.') }}</strong>
                    <span class="text-[10px] text-slate-600 font-semibold block">{{ $taxSig->jabatan_penandatangan ?? 'Board of Director (Finance & Tax)' }}</span>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[9.5px] text-slate-400">
            Faktur Pajak Standar PT Pinastika Bhakti Semesta • Sesuai Ketentuan DJP Online &amp; BUMN WAPU
        </div>
    </div>
</body>
</html>
