<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Bahan - {{ $raw->nomor_po ?? $raw->id }} - PT Pinastika Bhakti Semesta</title>
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
        <a href="{{ route('cugil.raw.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Raw Materials</span>
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-lg shadow-blue-500/20 flex items-center gap-2">
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
                <span class="inline-block px-3 py-1 rounded bg-blue-950 text-white font-black text-xs uppercase tracking-wider">
                    BUKTI PENERIMAAN
                </span>
                <div class="mt-1 text-xs text-slate-800 font-mono font-bold">
                    Ref PO: {{ $raw->nomor_po ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">TANDA TERIMA &amp; TIKET TIMBANGAN BAHAN BAKU</h2>
            <p class="text-[11px] text-slate-600">Penerimaan Pasokan Limbah Mentah / Bahan Olahan RDF dari TPST Mitra</p>
        </div>

        <!-- Delivery & Supplier Information -->
        <div class="grid grid-cols-2 gap-6 py-4 text-xs">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Pemasok / TPST Pengirim:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-24 text-slate-500 py-0.5">Nama Pemasok</td>
                        <td class="font-bold text-slate-900">: {{ $raw->nama_pemasok ?? ($raw->supplier->nama_supplier ?? $raw->kode_supplier) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kode Supplier</td>
                        <td class="font-mono text-slate-800">: {{ $raw->kode_supplier }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kota Asal</td>
                        <td class="text-slate-700">: {{ $raw->supplier->kota ?? 'Jawa Timur' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Batch Produksi</td>
                        <td class="font-mono text-slate-800">: {{ $raw->batch_produksi ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-300 pb-1 mb-2">Informasi Penerimaan &amp; Armada:</h3>
                <table class="w-full text-xs">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Tanggal Tiba</td>
                        <td class="font-mono font-semibold text-slate-900">: {{ $raw->tanggal ? $raw->tanggal->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Armada / Truk</td>
                        <td class="font-mono font-bold text-slate-900">: {{ $raw->truk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Petugas Timbang</td>
                        <td class="text-slate-800">: {{ $raw->petugas ?? 'QC & Timbangan PBS' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Status Pelunasan</td>
                        <td>: 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $raw->status_lunas == 'LUNAS' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $raw->status_lunas }}
                            </span>
                        </td>
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
                        <th class="border border-slate-400 py-2 px-3">Nama Bahan Mentah / Komoditas</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Tonase Timbang (Kg)</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Harga Satuan</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Nilai Bruto</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Rafaksi (Kadar Air/Kotor)</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Tagihan Netto (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($raw->items && $raw->items->count() > 0)
                        @foreach($raw->items as $idx => $it)
                            <tr class="{{ $idx % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="border border-slate-300 py-2.5 px-2 text-center text-slate-600">{{ $idx + 1 }}</td>
                                <td class="border border-slate-300 py-2.5 px-3">
                                    <strong class="text-slate-900">{{ $it->nama_barang }}</strong>
                                    @if($it->catatan)
                                        <div class="text-[10px] text-slate-500">{{ $it->catatan }}</div>
                                    @endif
                                </td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold">{{ number_format($it->qty, 1, ',', '.') }} Kg</td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono">Rp {{ number_format($it->harga_total, 0, ',', '.') }}</td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono text-rose-700">
                                    {{ $it->diskon_rafaksi > 0 ? '-Rp '.number_format($it->diskon_rafaksi, 0, ',', '.') : '-' }}
                                </td>
                                <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold text-slate-950">
                                    Rp {{ number_format($it->subtotal ?? ($it->harga_total - $it->diskon_rafaksi), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-slate-300 py-2.5 px-2 text-center text-slate-600">1</td>
                            <td class="border border-slate-300 py-2.5 px-3 font-bold text-slate-900">{{ $raw->nama_barang ?? 'Bahan Baku Mentah Limbah Plastik / RDF' }}</td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold">{{ number_format($raw->total_qty, 1, ',', '.') }} Kg</td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono">Rp {{ number_format($raw->total_bruto > 0 && $raw->total_qty > 0 ? $raw->total_bruto / $raw->total_qty : 0, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono">Rp {{ number_format($raw->total_bruto, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono text-rose-700">
                                {{ $raw->total_rafaksi > 0 ? '-Rp '.number_format($raw->total_rafaksi, 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-300 py-2.5 px-3 text-right font-mono font-bold text-slate-950">
                                Rp {{ number_format($raw->tagihan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="2" class="border border-slate-400 py-2 px-3 text-right uppercase">TOTAL NETTO:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono">{{ number_format($raw->total_qty, 1, ',', '.') }} Kg</td>
                        <td colspan="3" class="border border-slate-400 py-2 px-3 text-right uppercase">TOTAL TAGIHAN BERSIH:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono text-sm text-slate-950">
                            Rp {{ number_format($raw->tagihan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Financial Status -->
        <div class="mt-3 p-3 border border-slate-300 rounded-xl bg-slate-50 text-xs grid grid-cols-3 gap-3">
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Tagihan</span>
                <span class="font-mono text-slate-900 font-bold">Rp {{ number_format($raw->tagihan, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Telah Dibayar</span>
                <span class="font-mono text-emerald-700 font-bold">Rp {{ number_format($raw->payment, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Sisa Hutang ke TPST</span>
                <span class="font-mono {{ $raw->sisa_tagihan > 0 ? 'text-rose-700' : 'text-slate-600' }} font-bold">
                    Rp {{ number_format($raw->sisa_tagihan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-6 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-12 font-medium">Petugas Timbangan &amp; QC,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $raw->petugas ?? 'Timbangan PBS' }}</strong>
                <span class="text-[10px] text-slate-500">Divisi Quality &amp; Receiving</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-12 font-medium">Pengirim / Pengemudi Truk,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $raw->truk ?? 'Driver Mitra TPST' }}</strong>
                <span class="text-[10px] text-slate-500">Transporter TPST</span>
            </div>
            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                <span class="text-slate-700 block mb-12 font-bold">Mengetahui &amp; Validasi,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950">Manajer Operasional Pabrik</strong>
                <span class="text-[10px] text-slate-600 font-semibold">PT Pinastika Bhakti Semesta</span>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Tanda Terima Resmi PT Pinastika Bhakti Semesta • SimpleAkunting 3-6
        </div>
    </div>
</body>
</html>
