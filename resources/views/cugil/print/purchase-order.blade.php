<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $po->nomor_po }} - PT Pinastika Bhakti Semesta</title>
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
        <a href="{{ route('cugil.po.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Purchase Order</span>
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-orange-500/20 flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Cetak / Simpan ke PDF</span>
        </button>
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
                <span class="inline-block px-3 py-1 rounded bg-amber-950 text-white font-black text-xs uppercase tracking-wider">
                    PURCHASE ORDER
                </span>
                <div class="mt-1 text-xs text-slate-800 font-mono font-bold">
                    No: {{ $po->nomor_po }}
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">SURAT PESANAN PEMBELIAN BAHAN BAKU (PO)</h2>
            <p class="text-[11px] text-slate-600">Pengadaan Bahan Mentah Limbah Plastik / Sumber RDF dari TPST Mitra</p>
        </div>

        <!-- PO Details -->
        <div class="grid grid-cols-2 gap-6 py-4 text-xs">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Pemasok / Mitra TPST:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-500 py-0.5">Nama Vendor</td>
                        <td class="font-bold text-slate-900">: {{ $po->nama_vendor ?? ($po->supplier->nama_supplier ?? $po->kode_supplier) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kode Supplier</td>
                        <td class="font-mono text-slate-800">: {{ $po->kode_supplier }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Lokasi / Kota</td>
                        <td class="text-slate-700">: {{ $po->supplier->kota ?? 'Jawa Timur' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Item Pasokan</td>
                        <td class="text-slate-700">: {{ $po->supplier->item_barang ?? 'Limbah Plastik & RDF Material' }}</td>
                    </tr>
                </table>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Informasi Transaksi &amp; Termin:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Tanggal PO</td>
                        <td class="font-mono font-semibold text-slate-900">: {{ $po->tanggal ? $po->tanggal->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Batas Pengiriman</td>
                        <td class="font-mono text-slate-800">: {{ $po->batas_tanggal ? $po->batas_tanggal->format('d F Y') : 'Sesuai Jadwal' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Termin Pembayaran</td>
                        <td class="font-semibold text-slate-900">: {{ $po->termin_payment ?? 'Beli Putus / Timbang Bayar' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Petugas / Buyer</td>
                        <td class="text-slate-800">: {{ $po->petugas ?? 'Tim Pengadaan PBS' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-2">
            <table class="w-full text-xs text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[10px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-2 px-2 text-center w-8">#</th>
                        <th class="border border-slate-400 py-2 px-3">Nama Barang / Spesifikasi Bahan</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Kuantitas / Qty</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Harga Satuan (Rp)</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($po->items && $po->items->count() > 0)
                        @foreach($po->items as $idx => $it)
                            <tr class="{{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="border border-slate-300 py-2 px-2 text-center text-slate-600">{{ $idx + 1 }}</td>
                                <td class="border border-slate-300 py-2 px-3 font-semibold text-slate-900">
                                    {{ $it->nama_barang }}
                                    @if($it->kode_barang)
                                        <span class="text-[10px] text-slate-500 font-mono font-normal">({{ $it->kode_barang }})</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold">{{ number_format($it->qty, 1, ',', '.') }} {{ $it->satuan ?? 'Kg' }}</td>
                                <td class="border border-slate-300 py-2 px-3 text-right font-mono">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold text-slate-950">Rp {{ number_format($it->harga_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-slate-300 py-2 px-2 text-center text-slate-600">1</td>
                            <td class="border border-slate-300 py-2 px-3 font-semibold text-slate-900">Bahan Mentah Kresek / RDF Material</td>
                            <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold">{{ number_format($po->total_qty, 1, ',', '.') }} Kg</td>
                            <td class="border border-slate-300 py-2 px-3 text-right font-mono">Rp {{ number_format($po->total_nilai > 0 && $po->total_qty > 0 ? $po->total_nilai / $po->total_qty : 0, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2 px-3 text-right font-mono font-bold text-slate-950">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="2" class="border border-slate-400 py-2 px-3 text-right uppercase">TOTAL VOLUME:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono">{{ number_format($po->total_qty, 1, ',', '.') }} Kg</td>
                        <td class="border border-slate-400 py-2 px-3 text-right uppercase">TOTAL NILAI PO:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono text-sm text-slate-950">
                            Rp {{ number_format($po->total_nilai, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terbilang -->
        <div class="mt-3 p-3 border border-slate-300 rounded-xl bg-slate-50 text-xs">
            <span class="text-slate-600 font-semibold">Terbilang:</span> 
            <span class="font-bold text-slate-950 italic font-mono">{{ \App\Helpers\TerbilangHelper::make($po->total_nilai) }}</span>
        </div>

        <!-- Dynamic Document Signatures -->
        @php
            $poSignatures = \App\Models\DocumentSignature::forDoc('purchase_order');
        @endphp

        <div class="grid grid-cols-{{ min(4, max(1, $poSignatures->count())) }} gap-6 pt-6 text-center text-xs">
            @if($poSignatures->count() > 0)
                @foreach($poSignatures as $sig)
                    <div class="border {{ str_contains(strtolower($sig->label_judul), 'sah') || str_contains(strtolower($sig->label_judul), 'setuju') ? 'border-slate-900 bg-slate-50' : 'border-slate-300' }} rounded-xl p-3 flex flex-col justify-between">
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
                    <span class="text-slate-600 block mb-12 font-medium">Dibuat Oleh (Purchasing),</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $po->petugas ?? 'Petugas Logistik' }}</strong>
                    <span class="text-[10px] text-slate-500">Divisi Pengadaan Bahan</span>
                </div>
                <div class="border border-slate-300 rounded-xl p-3">
                    <span class="text-slate-600 block mb-12 font-medium">Diterima &amp; Disetujui Vendor,</span>
                    <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $po->nama_vendor ?? 'Pemasok / TPST Mitra' }}</strong>
                    <span class="text-[10px] text-slate-500">Mitra Pemasok Bahan Baku</span>
                </div>
                <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                    <span class="text-slate-700 block mb-12 font-bold">Disahkan Oleh,</span>
                    <strong class="block border-t border-slate-900 pt-1 text-slate-950">{{ $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.' }}</strong>
                    <span class="text-[10px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
                </div>
            @endif
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Purchase Order Sah PT Pinastika Bhakti Semesta • Sistem PBS-ERP Enterprise
        </div>
    </div>
</body>
</html>
