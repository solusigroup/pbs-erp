<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pembelian Bahan Baku - PBS-ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 9pt; }
            @page { size: A4 landscape; margin: 8mm 10mm; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-6xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('laporan.pembelian') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.pembelian.export', request()->all()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel (.csv)</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak / Simpan ke PDF</span>
            </button>
        </div>
    </div>

    <!-- Paper Canvas -->
    <div class="max-w-6xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-950">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                <p class="text-xs text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                <p class="text-[11px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '01.234.567.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} | Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-slate-950 text-white font-black text-xs uppercase tracking-wider">
                    LAPORAN PEMBELIAN
                </span>
                <div class="mt-1 text-[11px] text-slate-500 font-mono">
                    Dicetak: {{ date('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-4 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">REKAPITULASI PEMBELIAN &amp; PENERIMAAN BAHAN BAKU</h2>
            <p class="text-xs text-slate-600 mt-0.5">Penerimaan Limbah Mentah, Potongan Rafaksi, Ongkos Logistik &amp; Posisi Hutang Dagang</p>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-5 gap-2 py-3 text-xs">
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Tonase</span>
                <strong class="text-xs text-slate-900 font-mono">{{ number_format($totalQty, 1, ',', '.') }} Kg</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Rafaksi / Ongkir</span>
                <strong class="text-xs text-slate-900 font-mono">-Rp {{ number_format($totalRafaksi, 0, ',', '.') }} / +Rp {{ number_format($totalOngkir, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Tagihan Bersih</span>
                <strong class="text-xs text-blue-700 font-mono">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Pembayaran</span>
                <strong class="text-xs text-emerald-700 font-mono">Rp {{ number_format($totalPayment, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-900 rounded-lg p-2 text-center bg-slate-100">
                <span class="text-[10px] text-slate-600 block uppercase font-bold">Sisa Hutang Dagang</span>
                <strong class="text-xs text-rose-700 font-mono">Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Table Data -->
        <div class="py-2">
            <table class="w-full text-xs text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[10px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-2 px-2 text-center w-8">#</th>
                        <th class="border border-slate-400 py-2 px-2">Tanggal / PO</th>
                        <th class="border border-slate-400 py-2 px-3">Pemasok / Armada</th>
                        <th class="border border-slate-400 py-2 px-3">Rincian Item (SKU - Qty - Harga)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Total Qty (Kg)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Rafaksi / Ongkir</th>
                        <th class="border border-slate-400 py-2 px-2 text-right font-bold">Total Tagihan</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Dibayar</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Sisa Hutang</th>
                        <th class="border border-slate-400 py-2 px-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rawMaterials as $index => $raw)
                        <tr class="{{ $index % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                            <td class="border border-slate-300 py-1.5 px-2 text-center text-slate-600">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 py-1.5 px-2">
                                <span class="font-bold text-slate-900 block">{{ \Carbon\Carbon::parse($raw->tanggal)->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $raw->nomor_po ?? '-' }}</span>
                            </td>
                            <td class="border border-slate-300 py-1.5 px-3">
                                <span class="font-semibold text-slate-900 block">{{ $raw->nama_pemasok ?? $raw->kode_supplier }}</span>
                                <span class="text-[10px] text-slate-500">Truk: {{ $raw->truk ?? '-' }}</span>
                            </td>
                            <td class="border border-slate-300 py-1.5 px-3">
                                @if($raw->items && $raw->items->count() > 0)
                                    <div class="space-y-0.5 text-[10px]">
                                        @foreach($raw->items as $it)
                                            <div>{{ $it->nama_barang }} ({{ number_format($it->qty, 1, ',', '.') }} Kg @ Rp{{ number_format($it->harga_satuan, 0, ',', '.') }})</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-800 text-[11px]">{{ $raw->nama_barang }} ({{ number_format($raw->qty, 1, ',', '.') }} Kg)</span>
                                @endif
                            </td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono font-bold">{{ number_format($raw->total_qty, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right text-[10px] font-mono">
                                <div>-Rp {{ number_format($raw->diskon_rafaksi, 0, ',', '.') }}</div>
                                <div>+Rp {{ number_format($raw->ongkos_angkut, 0, ',', '.') }}</div>
                            </td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono font-bold text-slate-950">Rp {{ number_format($raw->tagihan, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono text-slate-800">Rp {{ number_format($raw->payment, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono font-bold text-slate-950">Rp {{ number_format($raw->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-center text-[10px] font-bold">
                                {{ $raw->status_lunas }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="4" class="border border-slate-400 py-2 px-3 text-right uppercase">Total:</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">{{ number_format($totalQty, 1, ',', '.') }} Kg</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">-Rp {{ number_format($totalRafaksi, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-slate-950">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-slate-950">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                        <td class="border border-slate-400"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-8 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diverifikasi Bagian Logistik,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Petugas Timbangan &amp; QC</strong>
                <span class="text-[10px] text-slate-500">Penerimaan Bahan Baku</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Finance &amp; Accounting Manager</strong>
                <span class="text-[10px] text-slate-500">Verifikasi Hutang Dagang</span>
            </div>
            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                <span class="text-slate-700 block mb-14 font-bold">Disetujui Oleh,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950">Kurniawan, S.E., Ak., CA., M.Ak.</strong>
                <span class="text-[10px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
            </div>
        </div>

        <div class="mt-6 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Laporan Resmi PT Pinastika Bhakti Semesta • Sistem PBS-ERP Enterprise • SimpleAkunting 3-6
        </div>
    </div>
</body>
</html>
