<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Perpajakan - PT Pinastika Bhakti Semesta</title>
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
        <a href="{{ route('laporan.pajak', request()->query()) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.pajak.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2">
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
                    TAX COMPLIANCE
                </span>
                <div class="mt-1 text-[11px] text-slate-500 font-mono">
                    Dicetak: {{ date('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-4 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">REKAPITULASI KEWAJIBAN PERPAJAKAN (PPN &amp; PPH)</h2>
            <p class="text-xs text-slate-600 mt-0.5">
                Monitoring Faktur Pajak, Pemotongan/Pemungutan, Status Setor NTPN &amp; Pelaporan SPT Masa
                @if(request('jenis_pajak'))
                    | Filter Jenis: {{ request('jenis_pajak') }}
                @endif
                @if(request('status_bayar'))
                    | Status Setor: {{ request('status_bayar') }}
                @endif
            </p>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-4 gap-3 py-4 text-xs">
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Kewajiban Pajak</span>
                <strong class="text-sm text-slate-900 font-mono">Rp {{ number_format($totalPajak, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Sudah Disetor (NTPN)</span>
                <strong class="text-sm text-emerald-700 font-mono">Rp {{ number_format($pajakSudahSetor, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Belum Disetor (Hutang Pajak)</span>
                <strong class="text-sm text-rose-700 font-mono">Rp {{ number_format($pajakBelumSetor, 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-900 rounded-lg p-2.5 text-center bg-slate-100">
                <span class="text-[10px] text-slate-600 block uppercase font-bold">Total Record Terdata</span>
                <strong class="text-sm text-slate-950 font-mono">{{ $transaksis->count() }} Dokumen</strong>
            </div>
        </div>

        <!-- Table Data -->
        <div class="py-2">
            <table class="w-full text-[11px] text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[9.5px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-1.5 px-1.5 text-center w-7">#</th>
                        <th class="border border-slate-400 py-1.5 px-2">Ref / Masa</th>
                        <th class="border border-slate-400 py-1.5 px-2">Jenis Pajak</th>
                        <th class="border border-slate-400 py-1.5 px-2">Tgl &amp; No Dokumen</th>
                        <th class="border border-slate-400 py-1.5 px-2">Lawan Transaksi &amp; NPWP</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right">DPP (Rp)</th>
                        <th class="border border-slate-400 py-1.5 px-1.5 text-center">Tarif</th>
                        <th class="border border-slate-400 py-1.5 px-2 text-right font-bold">Nominal Pajak</th>
                        <th class="border border-slate-400 py-1.5 px-2">Status Bayar &amp; NTPN</th>
                        <th class="border border-slate-400 py-1.5 px-2">Status Lapor &amp; BPE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $t)
                        <tr class="{{ $index % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                            <td class="border border-slate-300 py-1 px-1.5 text-center text-slate-600">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 py-1 px-2 font-mono">
                                <div class="font-bold text-slate-800">{{ $t->kode_referensi }}</div>
                                <div class="text-[9px] text-slate-500">{{ $t->masa_pajak }} / {{ $t->tahun_pajak }}</div>
                            </td>
                            <td class="border border-slate-300 py-1 px-2 font-bold text-slate-800">{{ $t->jenis_pajak }}</td>
                            <td class="border border-slate-300 py-1 px-2 text-[10px]">
                                <div class="font-mono">{{ $t->tanggal_faktur_potong ? $t->tanggal_faktur_potong->format('d/m/Y') : '-' }}</div>
                                <div class="text-slate-600">{{ $t->nomor_dokumen ?? '-' }}</div>
                            </td>
                            <td class="border border-slate-300 py-1 px-2">
                                <div class="font-semibold text-slate-900">{{ $t->lawan_transaksi }}</div>
                                <div class="text-[9px] font-mono text-slate-500">{{ $t->npwp_lawan_transaksi ?? '-' }}</div>
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono">Rp {{ number_format($t->dpp, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1 px-1.5 text-center font-mono">{{ $t->tarif_persen }}%</td>
                            <td class="border border-slate-300 py-1 px-2 text-right font-mono font-bold text-slate-950">
                                Rp {{ number_format($t->nominal_pajak, 0, ',', '.') }}
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-[10px]">
                                <div>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $t->status_bayar == 'Sudah Disetor' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $t->status_bayar }}
                                    </span>
                                </div>
                                @if($t->ntpn)
                                    <div class="font-mono text-[9px] text-slate-600 mt-0.5">NTPN: {{ $t->ntpn }}</div>
                                @endif
                            </td>
                            <td class="border border-slate-300 py-1 px-2 text-[10px]">
                                <div>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $t->status_lapor == 'Sudah Lapor' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $t->status_lapor }}
                                    </span>
                                </div>
                                @if($t->bpe_spt)
                                    <div class="font-mono text-[9px] text-slate-600 mt-0.5">BPE: {{ $t->bpe_spt }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-slate-500 italic">Tidak ada transaksi perpajakan sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-[10.5px]">
                        <td colspan="7" class="border border-slate-400 py-2 px-2 text-right uppercase">TOTAL NILAI PAJAK:</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono text-slate-950">Rp {{ number_format($totalPajak, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-[10px] text-emerald-800">Setor: Rp {{ number_format($pajakSudahSetor, 0, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-[10px] text-rose-800">Hutang: Rp {{ number_format($pajakBelumSetor, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-8 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Dibuat Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Tax Compliance Officer</strong>
                <span class="text-[10px] text-slate-500">Divisi Perpajakan &amp; Kepatuhan</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Tax &amp; Accounting Specialist</strong>
                <span class="text-[10px] text-slate-500">Divisi Keuangan &amp; Pajak</span>
            </div>
            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                <span class="text-slate-700 block mb-14 font-bold">Disetujui &amp; Disahkan Oleh,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950">Kurniawan, S.E., Ak., CA., M.Ak.</strong>
                <span class="text-[10px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
            </div>
        </div>

        <div class="mt-6 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Laporan Perpajakan Resmi PT Pinastika Bhakti Semesta • Sistem PBS-ERP Enterprise • DJP Online Compliance
        </div>
    </div>
</body>
</html>
