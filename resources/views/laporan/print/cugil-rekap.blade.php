<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Stok CUGIL - PT Pinastika Bhakti Semesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 10pt; }
            @page { size: A4 landscape; margin: 8mm 10mm; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-6xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('laporan.cugil-rekap') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.cugil-rekap.export') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2">
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
    <div class="max-w-6xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-950">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 shrink-0 flex items-center justify-center">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PT Pinastika Bhakti Semesta" class="h-full w-full object-contain">
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                    <p class="text-[11px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} | Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</p>
                </div>
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
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">REKAPITULASI MUTASI STOK &amp; OLAHAN CUGIL</h2>
            <p class="text-xs text-slate-600 mt-0.5">Pergerakan Bahan Mentah, Hasil Cuci Giling &amp; Valuasi Posisi Persediaan</p>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-4 gap-3 py-4 text-xs">
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Stok Awal</span>
                <strong class="text-sm text-slate-900 font-mono">{{ number_format($totalStokAwal, 1, ',', '.') }} Kg</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Barang Masuk (In)</span>
                <strong class="text-sm text-slate-900 font-mono">+{{ number_format($totalMasuk, 1, ',', '.') }} Kg</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2.5 text-center bg-slate-50">
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Total Barang Keluar (Out)</span>
                <strong class="text-sm text-slate-900 font-mono">-{{ number_format($totalKeluar, 1, ',', '.') }} Kg</strong>
            </div>
            <div class="border border-slate-900 rounded-lg p-2.5 text-center bg-slate-100">
                <span class="text-[10px] text-slate-600 block uppercase font-bold">Total Stok Akhir (Valuasi)</span>
                <strong class="text-sm text-slate-950 font-mono">{{ number_format($totalStokAkhir, 1, ',', '.') }} Kg (Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }})</strong>
            </div>
        </div>

        <!-- Table Data -->
        <div class="py-2">
            <table class="w-full text-xs text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100 text-slate-900 uppercase font-bold text-[10px] border-b-2 border-slate-900">
                        <th class="border border-slate-400 py-2 px-2 text-center w-8">#</th>
                        <th class="border border-slate-400 py-2 px-2">Kode</th>
                        <th class="border border-slate-400 py-2 px-3">Nama Barang / SKU</th>
                        <th class="border border-slate-400 py-2 px-2">Kategori</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Stok Awal</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Masuk (In)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Keluar (Out)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right font-bold">Stok Akhir</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Hrg Beli (Rp)</th>
                        <th class="border border-slate-400 py-2 px-2 text-right">Hrg Jual (Rp)</th>
                        <th class="border border-slate-400 py-2 px-3 text-right">Nilai Persediaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangs as $index => $b)
                        @php
                            $nilaiAset = max(0, $b->stok_akhir) * $b->harga_beli;
                        @endphp
                        <tr class="{{ $index % 2 == 1 ? 'bg-slate-50' : 'bg-white' }}">
                            <td class="border border-slate-300 py-1.5 px-2 text-center text-slate-600">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 font-mono font-bold text-slate-800">{{ $b->kode_barang }}</td>
                            <td class="border border-slate-300 py-1.5 px-3 font-semibold text-slate-900">{{ $b->nama_barang }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-slate-600">{{ $b->kategori }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono">{{ number_format($b->stok_awal, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono text-slate-800">+{{ number_format($b->barang_masuk, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono text-slate-800">-{{ number_format($b->barang_keluar, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono font-bold text-slate-950">{{ number_format($b->stok_akhir, 1, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono">{{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-2 text-right font-mono">{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 py-1.5 px-3 text-right font-mono font-bold text-slate-950">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="4" class="border border-slate-400 py-2 px-3 text-right uppercase">Total Agregat:</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">{{ number_format($totalStokAwal, 1, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">+{{ number_format($totalMasuk, 1, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">-{{ number_format($totalKeluar, 1, ',', '.') }}</td>
                        <td class="border border-slate-400 py-2 px-2 text-right font-mono">{{ number_format($totalStokAkhir, 1, ',', '.') }}</td>
                        <td colspan="2" class="border border-slate-400 py-2 px-2 text-right">Total Valuasi:</td>
                        <td class="border border-slate-400 py-2 px-3 text-right font-mono text-slate-950">Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-8 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Dibuat Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Kepala Gudang &amp; QC</strong>
                <span class="text-[10px] text-slate-500">Tim Logistik CUGIL</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Manajer Operasional Pabrik</strong>
                <span class="text-[10px] text-slate-500">Divisi Cuci Giling PBS</span>
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
