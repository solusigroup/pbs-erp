<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan CUGIL - PT Pinastika Bhakti Semesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 9.5pt; }
            @page { size: A4 landscape; margin: 8mm 10mm; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-7xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('laporan.penjualan', request()->query()) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.penjualan.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel (.csv)</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak / Simpan ke PDF</span>
            </button>
        </div>
    </div>

    <!-- Paper Canvas -->
    <div class="max-w-7xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-950">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                <p class="text-xs text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                <p class="text-[11px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '01.234.567.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} | Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-slate-950 text-white font-black text-xs uppercase tracking-wider">
                    LAPORAN RESMI
                </span>
                <div class="mt-1 text-[11px] text-slate-500 font-mono">
                    Dicetak: {{ date('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-4 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">REKAPITULASI PENJUALAN PRODUK CUCI GILING &amp; PIUTANG DAGANG</h2>
            <p class="text-xs text-slate-600 mt-0.5">
                @if(request('dari') && request('sampai'))
                    Periode Transaksi: {{ \Carbon\Carbon::parse(request('dari'))->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('sampai'))->translatedFormat('d M Y') }}
                @else
                    Seluruh Periode Pembukuan Berjalan
                @endif
                @if(request('customer'))
                    | Filter Buyer: {{ request('customer') }}
                @endif
                @if(request('status'))
                    | Status: {{ request('status') }}
                @endif
            </p>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-6 gap-2.5 py-4 text-xs">
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Volume</span>
                <strong class="text-xs text-slate-900 font-mono">{{ number_format($totalQty, 1, ',', '.') }} Kg</strong>
                <span class="text-[9px] text-slate-500 block">({{ number_format($totalSak, 0, ',', '.') }} Sak)</span>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Bruto</span>
                <strong class="text-xs text-slate-900 font-mono">Rp {{ number_format($totalBruto, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Diskon Penjualan</span>
                <strong class="text-xs text-amber-700 font-mono">-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Fee Makelar</span>
                <strong class="text-xs text-indigo-700 font-mono">Rp {{ number_format($totalFeeMakelar, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Terbayar</span>
                <strong class="text-xs text-emerald-700 font-mono">Rp {{ number_format($totalPayment, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-900 rounded-lg p-2 text-center bg-slate-100">
                <span class="text-[10px] text-slate-600 block uppercase font-bold">Sisa Piutang</span>
                <strong class="text-xs text-rose-700 font-mono">Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Table Data -->
        <div class="py-2">
            <table class="w-full text-[11px] text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[9.5px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-1.5 px-1.5 text-center w-7">#</th>
                        <th class="border border-slate-400 py-1.5 px-2">Tanggal</th>
                        <th class="border border-slate-400 py-1.5 px-2">No Penjualan</th>
                        <th class="border border-slate-400 py-1.5 px-2">Buyer / Pembeli</th>
                        <th class="border border-slate-400 py-1.5 px-2">Armada / Broker</th>
                        <th class="border border-slate-400 py-1.5 px-2">Rincian Produk</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">Qty (Kg)</th>
                        <th class="border border-slate-400 py-1.5 px-1.5 text-center">Sak</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">Nilai Bruto</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">Diskon</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">Fee Mklr</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right font-bold">Tagihan Net</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">Dibayar</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right font-bold">Sisa Piutang</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $s)
                        <tr class="{{ $index % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                            <td class="border border-slate-300 py-1 px-1.5 text-center text-slate-600">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 py-1 px-2 font-mono whitespace-nowrap">{{ $s->tanggal->format('d/m/Y') }}</td>
                            <td class="border border-slate-300 py-1 px-2 font-mono font-bold text-slate-800">{{ $s->id_penjualan }}</td>
                            <td class="border border-slate-300 py-1 px-2 font-semibold text-slate-900">
                                {{ $s->nama_buyer ?? ($s->customer->nama_customer ?? $s->kode_customer) }}
                                <span class="text-[9px] text-slate-500 block font-normal">{{ $s->kode_customer }}</span>
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-[10px] text-slate-600">
                                <div><i class="fas fa-truck text-[9px] mr-1"></i>{{ $s->truk ?? '-' }}</div>
                                <div><i class="fas fa-handshake text-[9px] mr-1"></i>{{ $s->broker ?? '-' }}</div>
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-[10px]">
                                @if($s->items && $s->items->count() > 0)
                                    @foreach($s->items as $it)
                                        <div>• {{ $it->nama_barang }}: {{ number_format($it->qty_terjual, 1, ',', '.') }} Kg @ Rp{{ number_format($it->harga_satuan, 0, ',', '.') }}</div>
                                    @endforeach
                                @else
                                    <div>• {{ $s->nama_barang }}: {{ number_format($s->qty_terjual, 1, ',', '.') }} Kg @ Rp{{ number_format($s->harga_satuan, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono font-bold">{{ number_format($s->total_qty, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1 px-1.5 text-center font-mono">{{ $s->total_sak }}</td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono">Rp {{ number_format($s->total_bruto, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono text-amber-700">
                                {{ $s->diskon_rupiah > 0 ? 'Rp '.number_format($s->diskon_rupiah, 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono text-indigo-700">
                                {{ $s->fee_makelar > 0 ? 'Rp '.number_format($s->fee_makelar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono font-bold text-slate-950">
                                Rp {{ number_format($s->tagihan, 0, ',', '.') }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono text-emerald-700">
                                Rp {{ number_format($s->payment, 0, ',', '.') }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono font-bold {{ $s->sisa_piutang > 0 ? 'text-rose-700' : 'text-slate-500' }}">
                                Rp {{ number_format($s->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-center">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $s->status_pelunasan == 'LUNAS' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $s->status_pelunasan }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="py-6 text-center text-slate-500 italic">Tidak ada transaksi penjualan sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-[10.5px]">
                        <td colspan="6" class="border border-slate-400 py-2 px-2 text-right uppercase">TOTAL AGREGAT:</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">{{ number_format($totalQty, 1, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-1.5 text-center font-mono">{{ number_format($totalSak, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-amber-800">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-indigo-800">Rp {{ number_format($totalFeeMakelar, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-slate-950">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-emerald-800">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-rose-800">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-8 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Dibuat Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Admin Penjualan &amp; Billing</strong>
                <span class="text-[10px] text-slate-500">Divisi Commercial &amp; Sales</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Manajer Pemasaran &amp; Distribusi</strong>
                <span class="text-[10px] text-slate-500">Divisi Distribusi Cuci Giling</span>
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
