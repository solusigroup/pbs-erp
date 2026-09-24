<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher {{ $jurnal->no_transaksi }} - PBS-ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-border { border-color: #333 !important; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="javascript:history.back()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak Voucher / PDF</span>
            </button>
        </div>
    </div>

    <!-- Voucher Paper Canvas -->
    <div class="max-w-4xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-900">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                <p class="text-xs text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                <p class="text-[11px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '01.234.567.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-slate-950 text-white font-black text-xs uppercase tracking-wider">
                    @if($jurnal->tipe_jurnal === 'Kas Masuk')
                        BUKTI KAS MASUK (BKM)
                    @elseif($jurnal->tipe_jurnal === 'Kas Keluar')
                        BUKTI KAS KELUAR (BKK)
                    @elseif($jurnal->tipe_jurnal === 'Transfer')
                        BUKTI TRANSFER KAS-BANK
                    @else
                        BUKTI MEMORIAL JURNAL
                    @endif
                </span>
                <div class="mt-2 text-xs font-mono font-bold text-slate-800">
                    No: {{ $jurnal->no_transaksi }}
                </div>
            </div>
        </div>

        <!-- Voucher Details Info -->
        <div class="grid grid-cols-2 gap-4 py-4 text-xs border-b border-slate-200">
            <div>
                <table class="w-full">
                    <tr class="py-1">
                        <td class="w-28 text-slate-500 font-medium">Tanggal</td>
                        <td class="font-bold text-slate-900">: {{ $jurnal->tanggal->format('d F Y') }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-slate-500 font-medium">Tipe Jurnal</td>
                        <td class="font-bold text-slate-900">: {{ $jurnal->tipe_jurnal }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-slate-500 font-medium">Referensi</td>
                        <td class="font-bold text-slate-900">: {{ $jurnal->sumber_referensi ?: '-' }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full">
                    <tr class="py-1">
                        <td class="w-28 text-slate-500 font-medium">Keterangan</td>
                        <td class="font-bold text-slate-900">: {{ $jurnal->deskripsi }}</td>
                    </tr>
                    <tr class="py-1">
                        <td class="text-slate-500 font-medium">Dibuat Oleh</td>
                        <td class="font-bold text-slate-900">: {{ $jurnal->created_by ?? 'System' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Details Table -->
        <div class="py-4">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="border-y-2 border-slate-900 bg-slate-100 font-bold uppercase text-[10px] text-slate-700">
                        <th class="py-2.5 px-3">Kode Akun</th>
                        <th class="py-2.5 px-3">Nama Akun</th>
                        <th class="py-2.5 px-3">Keterangan Baris</th>
                        <th class="py-2.5 px-3 text-right">Debit (Rp)</th>
                        <th class="py-2.5 px-3 text-right">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($jurnal->details as $d)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">{{ $d->kode_akun }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $d->akun->nama_akun ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $d->keterangan_baris ?: '-' }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                {{ $d->debit > 0 ? number_format($d->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                {{ $d->kredit > 0 ? number_format($d->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-900 font-bold bg-slate-50 text-xs">
                        <td colspan="3" class="py-2.5 px-3 text-right uppercase">Total Transaksi:</td>
                        <td class="py-2.5 px-3 text-right font-mono text-sm text-slate-950">
                            Rp {{ number_format($jurnal->total_debit, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-sm text-slate-950">
                            Rp {{ number_format($jurnal->total_kredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terbilang Box -->
        <div class="p-3 bg-slate-100 rounded-xl border border-slate-300 text-xs mb-6">
            <span class="font-bold text-slate-700">Terbilang:</span>
            <span class="italic font-semibold text-slate-900 ml-1"># {{ $terbilang }} #</span>
        </div>

        <!-- 3 Official Signature Blocks -->
        <div class="grid grid-cols-3 gap-4 pt-4 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Dibuat Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">{{ $jurnal->created_by ?? 'Staff Keuangan' }}</strong>
                <span class="text-[10px] text-slate-500">Staff Administrasi / Kasir</span>
            </div>

            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Finance &amp; Accounting Manager</strong>
                <span class="text-[10px] text-slate-500">Internal Reviewer</span>
            </div>

            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                <span class="text-slate-700 block mb-14 font-bold">Disetujui Oleh,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950">Kurniawan, S.E., Ak., CA., M.Ak.</strong>
                <span class="text-[10px] text-slate-600 font-semibold">BOD (Finance &amp; Tax)</span>
            </div>
        </div>

        <!-- Print Footer -->
        <div class="mt-6 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dicetak otomatis dari PBS-ERP Enterprise • {{ date('d/m/Y H:i:s') }} • PT Pinastika Bhakti Semesta
        </div>
    </div>

</body>
</html>
