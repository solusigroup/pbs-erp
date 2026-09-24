<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->id_penjualan }} - PT Pinastika Bhakti Semesta</title>
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
            <a href="{{ route('cugil.sales.surat-jalan', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-truck text-xs"></i>
                <span>Surat Jalan</span>
            </a>
            <a href="{{ route('cugil.sales.faktur-pajak', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-stamp text-xs"></i>
                <span>Faktur Pajak</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
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
                <span class="inline-block px-3 py-1 rounded bg-emerald-950 text-white font-black text-xs uppercase tracking-wider">
                    COMMERCIAL INVOICE
                </span>
                <div class="mt-1 text-xs text-slate-800 font-mono font-bold">
                    No: INV-PBS/{{ date('Y') }}/{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">FAKTUR PENJUALAN / INVOICE PENAGIHAN</h2>
            <p class="text-[11px] text-slate-600">Ref: Penyerahan Bahan Bakar Alternatif RDF / Olahan Daur Ulang Industri</p>
        </div>

        <!-- Billing Details -->
        <div class="grid grid-cols-2 gap-6 py-4 text-xs">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Tagihan Ditujukan Kepada:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-500 py-0.5">Nama Klien</td>
                        <td class="font-bold text-slate-900">: {{ $sale->nama_buyer ?? ($sale->customer->nama_customer ?? $sale->kode_customer) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Alamat Klien</td>
                        <td class="text-slate-700">: {{ $sale->customer->alamat ?? 'Gedung Utama Pabrik Tuban, Desa Sumberarum, Kec. Kerek, Tuban' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">NPWP Klien</td>
                        <td class="font-mono text-slate-800">: {{ $sale->customer->npwp ?? '01.001.609.5-092.000 (WAPU BUMN)' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kode Customer</td>
                        <td class="font-mono text-slate-800">: {{ $sale->kode_customer }}</td>
                    </tr>
                </table>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Rincian Dokumen &amp; Termin:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Tanggal Invoice</td>
                        <td class="font-mono font-semibold text-slate-900">: {{ $sale->tanggal ? $sale->tanggal->format('d F Y') : date('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Jatuh Tempo</td>
                        <td class="font-mono text-rose-700 font-bold">: {{ $sale->tanggal ? $sale->tanggal->copy()->addDays(30)->format('d F Y') : date('d F Y', strtotime('+30 days')) }} (Termin 30 Hari)</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Ref. Sales Order</td>
                        <td class="font-mono text-slate-800">: {{ $sale->id_penjualan }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Status Pembayaran</td>
                        <td>: 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $sale->status_pelunasan == 'LUNAS' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $sale->status_pelunasan }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="py-2">
            <table class="w-full text-xs text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[10px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-2 px-2 text-center w-8">#</th>
                        <th class="border border-slate-400 py-2 px-3">Deskripsi Komoditas / Jasa</th>
                        <th class="border border-slate-400 py-2 px-2 text-center">Volume (Kg)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Harga Satuan</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Bruto (Rp)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Potongan</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Subtotal DPP (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sale->items && $sale->items->count() > 0)
                        @foreach($sale->items as $idx => $it)
                            <tr class="{{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="border border-slate-300 py-2.5 px-2 text-center text-slate-600">{{ $idx + 1 }}</td>
                                <td class="border border-slate-300 py-2.5 px-3">
                                    <strong class="text-slate-900">{{ $it->nama_barang }}</strong>
                                    <div class="text-[10px] text-slate-500">Pasokan Bahan Bakar Alternatif RDF ke SIG Tuban</div>
                                </td>
                                <td class="border border-slate-300 py-2.5 px-2 text-center font-mono font-bold">{{ number_format($it->qty_terjual, 1, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2.5 px-2 text-right font-mono">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2.5 px-2 text-right font-mono">Rp {{ number_format($it->harga_total, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2.5 px-2 text-right font-mono text-amber-700">
                                    {{ $it->diskon_rupiah > 0 ? '-Rp '.number_format($it->diskon_rupiah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold text-slate-950">
                                    Rp {{ number_format($it->subtotal ?? ($it->harga_total - $it->diskon_rupiah), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-slate-300 py-2.5 px-2 text-center text-slate-600">1</td>
                            <td class="border border-slate-300 py-2.5 px-3">
                                <strong class="text-slate-900">{{ $sale->nama_barang ?? 'Pasokan RDF / Olahan Daur Ulang Plastik' }}</strong>
                                <div class="text-[10px] text-slate-500">Penyerahan Bahan Bakar Alternatif SIG Tuban</div>
                            </td>
                            <td class="border border-slate-300 py-2.5 px-2 text-center font-mono font-bold">{{ number_format($sale->total_qty, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2.5 px-2 text-right font-mono">Rp {{ number_format($sale->total_bruto > 0 && $sale->total_qty > 0 ? $sale->total_bruto / $sale->total_qty : 0, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2.5 px-2 text-right font-mono">Rp {{ number_format($sale->total_bruto, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2.5 px-2 text-right font-mono text-amber-700">
                                {{ $sale->total_diskon > 0 ? '-Rp '.number_format($sale->total_diskon, 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold text-slate-950">
                                Rp {{ number_format($sale->tagihan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Calculation & Financial Summary -->
        @php
            $dpp = (float)$sale->tagihan;
            $ppn = round($dpp * 0.11);
            $totalWithPpn = $dpp + $ppn;
        @endphp

        <div class="grid grid-cols-2 gap-4 mt-2">
            <!-- Payment Instruction -->
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50 text-xs flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-slate-900 uppercase text-[11px] mb-1">Instruksi Pembayaran Transfer Bank:</h4>
                    <p class="text-[11px] text-slate-600 mb-2">Harap mencantumkan nomor invoice pada berita transfer bank:</p>
                    <div class="p-2.5 bg-white border border-slate-300 rounded-lg text-xs space-y-1 font-mono">
                        <div><strong>Bank:</strong> Bank Mandiri KCP Mojokerto</div>
                        <div><strong>No. Rekening:</strong> <span class="text-slate-950 font-bold">142-00-1234567-8</span></div>
                        <div><strong>Atas Nama:</strong> PT Pinastika Bhakti Semesta</div>
                    </div>
                </div>
                <div class="mt-3 text-[10px] text-slate-500 italic">
                    * Pembayaran sah apabila dana telah efektif masuk ke rekening koran resmi PT PBS.
                </div>
            </div>

            <!-- Calculation Box -->
            <div class="border border-slate-900 rounded-xl p-3 bg-white text-xs">
                <table class="w-full text-xs">
                    <tr>
                        <td class="py-1 text-slate-600">Dasar Pengenaan Pajak (DPP)</td>
                        <td class="py-1 text-right font-mono font-bold text-slate-900">Rp {{ number_format($dpp, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-slate-600">
                            PPN 11% (Kode 030 WAPU BUMN)
                            <span class="text-[9px] text-slate-400 block font-normal">*Dipungut oleh SIG Tuban</span>
                        </td>
                        <td class="py-1 text-right font-mono font-semibold text-slate-700">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t-2 border-slate-900">
                        <td class="py-2 font-bold text-slate-950 uppercase">TOTAL TAGIHAN NETTO:</td>
                        <td class="py-2 text-right font-mono font-black text-sm text-slate-950">Rp {{ number_format($sale->tagihan, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->payment > 0)
                        <tr class="border-t border-slate-200 text-[11px]">
                            <td class="py-1 text-emerald-700">Telah Dibayar (DP / Payment)</td>
                            <td class="py-1 text-right font-mono text-emerald-700 font-bold">-Rp {{ number_format($sale->payment, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-1 font-bold text-rose-700">Sisa Tagihan (Piutang)</td>
                            <td class="py-1 text-right font-mono font-bold text-rose-700">Rp {{ number_format($sale->sisa_piutang, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Terbilang Banner -->
        <div class="mt-3 p-3 border border-slate-300 rounded-xl bg-slate-50 text-xs font-semibold text-slate-800">
            Terbilang: <span class="font-bold text-slate-950 italic font-mono">{{ \App\Helpers\TerbilangHelper::make($sale->tagihan) }}</span>
        </div>

        <!-- Dynamic Legal Signature Box with Meterai placeholder -->
        @php
            $invSignatures = \App\Models\DocumentSignature::forDoc('invoice');
            $mainSig = $invSignatures->first();
        @endphp

        <div class="grid grid-cols-2 gap-6 pt-6 text-center text-xs">
            <div class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between text-left text-[11px] text-slate-500">
                <div>
                    <h5 class="font-bold text-slate-800 uppercase mb-1">Catatan Tagihan:</h5>
                    <p>1. Faktur ini merupakan dokumen penagihan sah PT Pinastika Bhakti Semesta.</p>
                    <p>2. Bukti potong/pungut PPN &amp; PPh mohon disampaikan ke email: <span class="text-slate-700 font-mono font-semibold">{{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</span>.</p>
                </div>
                <div class="text-[10px] text-slate-400 mt-2">
                    Lembar 1: Asli (Klien SIG) • Lembar 2: Copy (Accounting PBS)
                </div>
            </div>

            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50 text-center">
                <span class="text-slate-700 block mb-2 font-bold">{{ $mainSig->organisasi ?? ($perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA') }}</span>
                
                <div class="h-16 flex items-center justify-center">
                    <span class="inline-block border border-dashed border-slate-400 px-3 py-1 text-[9px] text-slate-400 rounded font-mono">
                        {{ $mainSig->catatan ?? 'METERAI ELEKTRONIK / RP 10.000' }}
                    </span>
                </div>

                <strong class="block border-t border-slate-900 pt-1 text-slate-950 font-black">
                    {{ $mainSig->nama_penandatangan ?? ($perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.') }}
                </strong>
                <span class="text-[10px] text-slate-600 font-semibold block">
                    {{ $mainSig->jabatan_penandatangan ?? 'Board of Director (Finance & Tax)' }}
                </span>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Commercial Invoice Resmi PT Pinastika Bhakti Semesta • SimpleAkunting 3-6
        </div>
    </div>
</body>
</html>
