<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - {{ $sale->id_penjualan }} - PT Pinastika Bhakti Semesta</title>
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
            <a href="{{ route('cugil.sales.surat-jalan', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-truck text-xs"></i>
                <span>Surat Jalan</span>
            </a>
            <a href="{{ route('cugil.sales.invoice', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-file-invoice-dollar text-xs"></i>
                <span>Invoice</span>
            </a>
            <a href="{{ route('cugil.sales.faktur-pajak', $sale->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i class="fas fa-stamp text-xs"></i>
                <span>Faktur Pajak</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-orange-500/20 flex items-center gap-2">
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
                <span class="inline-block px-3 py-1 rounded bg-slate-950 text-white font-black text-xs uppercase tracking-wider">
                    SALES ORDER
                </span>
                <div class="mt-1 text-xs text-slate-700 font-mono font-bold">
                    No: {{ $sale->id_penjualan }}
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">SURAT KONFIRMASI PESANAN PENJUALAN</h2>
            <p class="text-[11px] text-slate-600">Ref: Pengadaan Bahan Bakar Alternatif RDF / Olahan Daur Ulang Plastik</p>
        </div>

        <!-- Order & Customer Information -->
        <div class="grid grid-cols-2 gap-6 py-4 text-xs">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Pemesan / Pembeli:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-500 py-0.5">Nama Perusahaan</td>
                        <td class="font-bold text-slate-900">: {{ $sale->nama_buyer ?? ($sale->customer->nama_customer ?? $sale->kode_customer) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kode Customer</td>
                        <td class="font-mono text-slate-800">: {{ $sale->kode_customer }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Alamat / Lokasi</td>
                        <td class="text-slate-700">: {{ $sale->customer->alamat ?? 'Pabrik Tuban / Jawa Timur' }} {{ $sale->customer->kota ? '('.$sale->customer->kota.')' : '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">No. Kontak / PIC</td>
                        <td class="text-slate-700">: {{ $sale->customer->telepon ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Informasi Pesanan &amp; Logistik:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Tanggal Order</td>
                        <td class="font-mono font-semibold text-slate-900">: {{ $sale->tanggal ? $sale->tanggal->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Rencana Kirim</td>
                        <td class="font-mono text-slate-800">: {{ $sale->tanggal_kirim ? $sale->tanggal_kirim->format('d F Y') : 'Sesuai Jadwal Ritase' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Sales / PIC PBS</td>
                        <td class="text-slate-800">: {{ $sale->sales ?? 'Divisi Commercial & Supply' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Armada &amp; Broker</td>
                        <td class="text-slate-800">: {{ $sale->truk ?? '-' }} / {{ $sale->broker ?? '-' }}</td>
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
                        <th class="border border-slate-400 py-2 px-3">Uraian Komoditas / Produk</th>
                        <th class="border border-slate-400 py-2 px-2 text-center">Jumlah Sak</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Volume (Kg)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Harga Satuan</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Diskon</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sale->items && $sale->items->count() > 0)
                        @foreach($sale->items as $idx => $it)
                            <tr class="{{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="border border-slate-300 py-2 px-2 text-center text-slate-600">{{ $idx + 1 }}</td>
                                <td class="border border-slate-300 py-2 px-3">
                                    <strong class="text-slate-900">{{ $it->nama_barang }}</strong>
                                    @if($it->catatan)
                                        <div class="text-[10px] text-slate-500">{{ $it->catatan }}</div>
                                    @endif
                                </td>
                                <td class="border border-slate-300 py-2 px-2 text-center font-mono">{{ $it->jumlah_sak ?? '-' }}</td>
                                <td class="border border-slate-300 py-2 px-2 text-right font-mono font-bold">{{ number_format($it->qty_terjual, 1, ',', '.') }} Kg</td>
                                <td class="border border-slate-300 py-2 px-2 text-right font-mono">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2 px-2 text-right font-mono text-amber-700">
                                    {{ $it->diskon_rupiah > 0 ? '-Rp '.number_format($it->diskon_rupiah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold text-slate-950">
                                    Rp {{ number_format($it->subtotal ?? ($it->harga_total - $it->diskon_rupiah), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-slate-300 py-2 px-2 text-center text-slate-600">1</td>
                            <td class="border border-slate-300 py-2 px-3 font-bold text-slate-900">{{ $sale->nama_barang ?? 'Pasokan RDF / Olahan Plastik' }}</td>
                            <td class="border border-slate-300 py-2 px-2 text-center font-mono">{{ $sale->total_sak ?? '-' }}</td>
                            <td class="border border-slate-300 py-2 px-2 text-right font-mono font-bold">{{ number_format($sale->total_qty, 1, ',', '.') }} Kg</td>
                            <td class="border border-slate-300 py-2 px-2 text-right font-mono">Rp {{ number_format($sale->total_bruto > 0 && $sale->total_qty > 0 ? $sale->total_bruto / $sale->total_qty : 0, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2 px-2 text-right font-mono text-amber-700">
                                {{ $sale->total_diskon > 0 ? '-Rp '.number_format($sale->total_diskon, 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold text-slate-950">
                                Rp {{ number_format($sale->tagihan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="3" class="border border-slate-400 py-2 px-3 text-right uppercase">TOTAL VOLUME:</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">{{ number_format($sale->total_qty, 1, ',', '.') }} Kg</td>
                        <td colspan="2" class="border border-slate-400 py-2 px-2 text-right uppercase">TOTAL NILAI ORDER:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono text-sm text-slate-950">
                            Rp {{ number_format($sale->tagihan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terbilang & Terms -->
        <div class="mt-3 p-3 border border-slate-300 rounded-xl bg-slate-50 text-xs">
            <div class="text-slate-600 font-semibold mb-1">
                Terbilang: <span class="font-bold text-slate-900 italic font-mono">{{ \App\Helpers\TerbilangHelper::make($sale->tagihan) }}</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-200 grid grid-cols-2 gap-2">
                <div>• Spesifikasi: Sesuai parameter kalori dan kadar air maksimum yang disepakati.</div>
                <div>• Pembayaran: Transfer Rekening PT PBS (Bank Mandiri 142-00-1234567-8).</div>
            </div>
        </div>

        <!-- Dynamic Document Signatures -->
        @php
            $soSignatures = \App\Models\DocumentSignature::forDoc('sales_order');
        @endphp

        <div class="grid grid-cols-{{ min(4, max(1, $soSignatures->count())) }} gap-6 pt-6 text-center text-xs">
            @if($soSignatures->count() > 0)
                @foreach($soSignatures as $sig)
                    <div class="border {{ str_contains(strtolower($sig->label_judul), 'sah') ? 'border-slate-900 bg-slate-50' : 'border-slate-300' }} rounded-xl p-3 flex flex-col justify-between">
                        <span class="{{ str_contains(strtolower($sig->label_judul), 'sah') ? 'text-slate-900 font-bold' : 'text-slate-600 font-medium' }} block mb-12">{{ $sig->label_judul }}</span>
                        <div>
                            <strong class="block border-t {{ str_contains(strtolower($sig->label_judul), 'sah') ? 'border-slate-900 text-slate-950 font-black' : 'border-slate-400 text-slate-900' }} pt-1">
                                {{ $sig->nama_penandatangan }}
                            </strong>
                            <span class="text-[10px] text-slate-600 block">{{ $sig->jabatan_penandatangan }}</span>
                            @if($sig->organisasi)
                                <span class="text-[9px] text-slate-500 block">{{ $sig->organisasi }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="border border-slate-300 rounded-xl p-3">
                    <span class="text-slate-600 block mb-12 font-medium">Disiapkan Oleh,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $sale->sales ?? 'Divisi Commercial' }}</strong>
                    <span class="text-[10px] text-slate-500">Commercial &amp; Supply PBS</span>
                </div>
                <div class="border border-slate-300 rounded-xl p-3">
                    <span class="text-slate-600 block mb-12 font-medium">Disetujui Pemesan,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">Procurement / Buyer</strong>
                    <span class="text-[10px] text-slate-500">{{ $sale->nama_buyer ?? 'PT Semen Indonesia Group' }}</span>
                </div>
                <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                    <span class="text-slate-700 block mb-12 font-bold">Disahkan Oleh,</span>
                    <strong class="block border-t border-slate-900 pt-1 text-slate-950">{{ $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.' }}</strong>
                    <span class="text-[10px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
                </div>
            @endif
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dokumen Resmi Sales Order PT Pinastika Bhakti Semesta • Sistem PBS-ERP Enterprise
        </div>
    </div>
</body>
</html>
