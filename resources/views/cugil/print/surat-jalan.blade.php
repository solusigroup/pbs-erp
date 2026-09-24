<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $sale->id_penjualan }} - PT Pinastika Bhakti Semesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 10pt; }
            @page { size: A4 portrait; margin: 10mm 12mm; }
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
            <a href="{{ route('cugil.sales.invoice', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-file-invoice-dollar text-xs"></i>
                <span>Invoice</span>
            </a>
            <a href="{{ route('cugil.sales.faktur-pajak', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-stamp text-xs"></i>
                <span>Faktur Pajak</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- Paper Canvas -->
    <div class="max-w-4xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-950">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                <p class="text-xs text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                <p class="text-[11px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '01.234.567.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} | Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-blue-950 text-white font-black text-xs uppercase tracking-wider">
                    SURAT JALAN
                </span>
                <div class="mt-1 text-xs text-slate-700 font-mono font-bold">
                    No. DO: SJ-{{ str_replace(['CUGILPBS/', 'SALES/'], '', $sale->id_penjualan) }}/{{ date('Y') }}
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">SURAT JALAN &amp; BUKTI PENGIRIMAN BARANG</h2>
            <p class="text-[11px] text-slate-600">Dokumen Resmi Pengawalan Muatan / Logistik Ekspedisi</p>
        </div>

        <!-- Delivery Information -->
        <div class="grid grid-cols-2 gap-6 py-4 text-xs">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Tujuan Pengiriman / Penerima:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-500 py-0.5">Penerima</td>
                        <td class="font-bold text-slate-900">: {{ $sale->nama_buyer ?? ($sale->customer->nama_customer ?? $sale->kode_customer) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Alamat Kirim</td>
                        <td class="text-slate-700">: {{ $sale->customer->alamat ?? 'Pabrik Semen Indonesia Tuban, Desa Sumberarum, Kec. Kerek, Tuban' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kota Tujuan</td>
                        <td class="text-slate-700">: {{ $sale->customer->kota ?? 'Tuban' }}, Jawa Timur</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">No. Kontak / PIC</td>
                        <td class="text-slate-700">: {{ $sale->customer->telepon ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Informasi Transportasi &amp; Ekspedisi:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Tanggal Kirim</td>
                        <td class="font-mono font-semibold text-slate-900">: {{ $sale->tanggal_kirim ? $sale->tanggal_kirim->format('d F Y') : ($sale->tanggal ? $sale->tanggal->format('d F Y') : '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Armada / Truk</td>
                        <td class="font-bold text-slate-900 font-mono">: {{ $sale->truk ?? 'Dump Truck / Tronton' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Transporter / Broker</td>
                        <td class="text-slate-800">: {{ $sale->broker ?? 'Logistik PBS' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Ref. Sales Order</td>
                        <td class="font-mono text-slate-800">: {{ $sale->id_penjualan }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Goods Table -->
        <div class="py-2">
            <table class="w-full text-xs text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[10px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-2 px-2 text-center w-8">#</th>
                        <th class="border border-slate-400 py-2 px-3">Nama Komoditas / Jenis Muatan</th>
                        <th class="border border-slate-400 py-2 px-2 text-center w-28">Jumlah Kemasan</th>
                        <th class="border border-slate-400 py-2 px-3 text-right w-36">Tonase Netto Kirim</th>
                        <th class="border border-slate-400 py-2 px-3">Catatan / Segel Muatan</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sale->items && $sale->items->count() > 0)
                        @foreach($sale->items as $idx => $it)
                            <tr class="{{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="border border-slate-300 py-3 px-2 text-center text-slate-600">{{ $idx + 1 }}</td>
                                <td class="border border-slate-300 py-3 px-3">
                                    <strong class="text-slate-900">{{ $it->nama_barang }}</strong>
                                    <div class="text-[10px] text-slate-500">Pasokan Bahan Bakar Alternatif / Daur Ulang</div>
                                </td>
                                <td class="border border-slate-300 py-3 px-2 text-center font-mono font-bold">{{ $it->jumlah_sak > 0 ? $it->jumlah_sak.' Sak' : 'Curah (Bulk)' }}</td>
                                <td class="border border-slate-300 py-3 px-3 text-right font-mono font-bold text-sm text-slate-950">
                                    {{ number_format($it->qty_terjual, 1, ',', '.') }} Kg
                                    <span class="text-[10px] text-slate-500 block">({{ number_format($it->qty_terjual / 1000, 3, ',', '.') }} Ton)</span>
                                </td>
                                <td class="border border-slate-300 py-3 px-3 text-[11px] text-slate-600">
                                    {{ $it->catatan ?? ($sale->remark ?? 'Kondisi kering & siap proses') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-slate-300 py-3 px-2 text-center text-slate-600">1</td>
                            <td class="border border-slate-300 py-3 px-3">
                                <strong class="text-slate-900">{{ $sale->nama_barang ?? 'RDF Fluff / Pelet Kalori Tinggi' }}</strong>
                                <div class="text-[10px] text-slate-500">Pasokan Alternatif Fuel PT SIG Tuban</div>
                            </td>
                            <td class="border border-slate-300 py-3 px-2 text-center font-mono font-bold">{{ $sale->total_sak > 0 ? $sale->total_sak.' Sak' : 'Curah (Bulk)' }}</td>
                            <td class="border border-slate-300 py-3 px-3 text-right font-mono font-bold text-sm text-slate-950">
                                {{ number_format($sale->total_qty, 1, ',', '.') }} Kg
                                <span class="text-[10px] text-slate-500 block">({{ number_format($sale->total_qty / 1000, 3, ',', '.') }} Ton)</span>
                            </td>
                            <td class="border border-slate-300 py-3 px-3 text-[11px] text-slate-600">
                                {{ $sale->remark ?? 'Sesuai spesifikasi kontrak pengadaan' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="2" class="border border-slate-400 py-2.5 px-3 text-right uppercase">TOTAL MUATAN:</td>
                        <td class="border border-slate-400 py-2.5 px-2 text-center font-mono">{{ $sale->total_sak > 0 ? $sale->total_sak.' Sak' : 'Curah' }}</td>
                        <td class="border border-slate-400 py-2.5 px-3 text-right font-mono text-sm text-slate-950">
                            {{ number_format($sale->total_qty, 1, ',', '.') }} Kg
                        </td>
                        <td class="border border-slate-400 py-2.5 px-3 font-mono text-[11px] text-slate-700">
                            ({{ number_format($sale->total_qty / 1000, 3, ',', '.') }} Metric Ton)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Weighbridge Confirmation Box -->
        <div class="mt-4 p-3 border border-slate-300 rounded-xl bg-slate-50 text-xs grid grid-cols-3 gap-3">
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Timbangan Asal (PBS / Hub)</span>
                <span class="font-mono text-slate-900 font-bold">{{ number_format($sale->total_qty, 1, ',', '.') }} Kg</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Timbangan Tujuan (SIG Tuban)</span>
                <span class="font-mono text-slate-600 italic">.................... Kg</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Selisih Timbangan / Toleransi</span>
                <span class="font-mono text-slate-600 italic">.................... Kg</span>
            </div>
        </div>

        <!-- Dynamic Verification Signatures -->
        @php
            $sjSignatures = \App\Models\DocumentSignature::forDoc('surat_jalan');
        @endphp

        <div class="grid grid-cols-{{ min(4, max(1, $sjSignatures->count())) }} gap-3 pt-6 text-center text-[11px]">
            @if($sjSignatures->count() > 0)
                @foreach($sjSignatures as $sig)
                    <div class="border {{ str_contains(strtolower($sig->label_judul), 'penerima') || str_contains(strtolower($sig->label_judul), 'sah') ? 'border-slate-900 bg-slate-50' : 'border-slate-300' }} rounded-xl p-2.5 flex flex-col justify-between">
                        <span class="{{ str_contains(strtolower($sig->label_judul), 'penerima') || str_contains(strtolower($sig->label_judul), 'sah') ? 'text-slate-700 font-bold' : 'text-slate-500 font-medium' }} block mb-12">
                            {{ $sig->label_judul }}
                        </span>
                        <div>
                            <strong class="block border-t {{ str_contains(strtolower($sig->label_judul), 'penerima') || str_contains(strtolower($sig->label_judul), 'sah') ? 'border-slate-900 text-slate-950 font-bold' : 'border-slate-400 text-slate-900' }} pt-1">
                                {{ $sig->nama_penandatangan }}
                            </strong>
                            <span class="text-[9px] text-slate-600 block">{{ $sig->jabatan_penandatangan }}</span>
                            @if($sig->organisasi)
                                <span class="text-[8px] text-slate-400 block">{{ $sig->organisasi }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="border border-slate-300 rounded-xl p-2.5">
                    <span class="text-slate-500 block mb-12 font-medium">Petugas Timbang Asal,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">Timbangan PBS</strong>
                    <span class="text-[9px] text-slate-500">Logistik &amp; Gudang</span>
                </div>
                <div class="border border-slate-300 rounded-xl p-2.5">
                    <span class="text-slate-500 block mb-12 font-medium">Pengemudi / Supir,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $sale->truk ?? 'Driver Ekspedisi' }}</strong>
                    <span class="text-[9px] text-slate-500">Transporter Muatan</span>
                </div>
                <div class="border border-slate-300 rounded-xl p-2.5">
                    <span class="text-slate-500 block mb-12 font-medium">Security / Gate Keluar,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">Pos Keamanan</strong>
                    <span class="text-[9px] text-slate-500">Pemeriksaan Segel</span>
                </div>
                <div class="border border-slate-900 rounded-xl p-2.5 bg-slate-50">
                    <span class="text-slate-700 block mb-12 font-bold">Penerima Pabrik SIG,</span>
                    <strong class="block border-t border-slate-900 pt-1 text-slate-950">AF / Gudang RDF</strong>
                    <span class="text-[9px] text-slate-600 font-semibold">PT Semen Indonesia Tuban</span>
                </div>
            @endif
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Surat Jalan Sah PT Pinastika Bhakti Semesta • Harap periksa segel dan muatan saat diterima di Pabrik SIG Tuban.
        </div>
    </div>
</body>
</html>
