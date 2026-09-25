@extends('layouts.admin')

@section('title', 'Laporan Keuangan - SAK EP/EMKM (Neraca & Laba Rugi)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 border border-emerald-500/30 text-emerald-400 shadow-lg">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Laporan Keuangan Korporasi
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold font-mono">
                            SAK EP / EMKM
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Laba Rugi &amp; Neraca (Posisi Keuangan) Single &amp; Komparatif Periode</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->canMutate())
            <form action="{{ route('akuntansi.jurnal.adjustHppCugil') }}" method="POST" class="inline" onsubmit="return confirm('Jalankan Jurnal Penyesuaian HPP CUGIL per tanggal {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}?\n\nSaldo akumulasi Persediaan Bahan Baku (1-1610) per tanggal tersebut akan dialokasikan ke HPP (5-1100).')">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggalSampai }}">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-cyan-500/20" title="Penyesuaian HPP CUGIL per Periode Tanggal Filter (Zeroing Persediaan Bahan Baku)">
                    <i class="fas fa-wand-magic-sparkles text-amber-300"></i>
                    <span>⚡ Penyesuaian HPP CUGIL (Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }})</span>
                </button>
            </form>
            @endif
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-print text-amber-400"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-400 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-triangle-exclamation text-rose-400 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
        </div>
    @endif

    <!-- Filter Mode & Parameter Periode SAK EP/EMKM -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
        <form method="GET" action="{{ route('akuntansi.laporan') }}" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-[11px] text-slate-400 font-medium mb-1">Mode Penyajian</label>
                    <select name="mode" id="selectMode" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-semibold">
                        <option value="single" {{ $mode === 'single' ? 'selected' : '' }}>Single Periode (From Date - To Date)</option>
                        <option value="komparatif" {{ $mode === 'komparatif' ? 'selected' : '' }}>Komparatif Periode (SAK EP/EMKM)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 font-medium mb-1">Periode Utama (Dari)</label>
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 font-medium mb-1">Periode Utama (Sampai)</label>
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>

                @if($mode === 'komparatif')
                <div>
                    <label class="block text-[11px] text-amber-400 font-medium mb-1">Pembanding (Dari)</label>
                    <input type="date" name="tanggal_dari_komparatif" value="{{ $tanggalDariKomparatif }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-amber-500/40 text-amber-300">
                </div>

                <div>
                    <label class="block text-[11px] text-amber-400 font-medium mb-1">Pembanding (Sampai)</label>
                    <input type="date" name="tanggal_sampai_komparatif" value="{{ $tanggalSampaiKomparatif }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-amber-500/40 text-amber-300">
                </div>
                @endif

                <div class="{{ $mode === 'komparatif' ? 'sm:col-span-2 lg:col-span-5' : '' }} flex items-center justify-end gap-2 pt-2">
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold transition flex items-center gap-1.5">
                        <i class="fas fa-filter"></i>
                        <span>Tampilkan Laporan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tampilan Laporan Keuangan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. LAPORAN LABA RUGI -->
        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
            <div class="pb-3 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-amber-400"></i>
                        <span>Laporan Laba Rugi</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                        {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
                        @if($mode === 'komparatif')
                            <span class="text-amber-400 ml-1">vs {{ \Carbon\Carbon::parse($tanggalDariKomparatif)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampaiKomparatif)->format('d/m/Y') }}</span>
                        @endif
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $mode === 'komparatif' ? 'Komparatif' : 'Single' }} SAK
                </span>
            </div>

            <!-- Bagian Pendapatan Usaha -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block mb-2">1. Pendapatan Usaha</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($dataUtama['pendapatanData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['pendapatanData'][$kode]['nominal'] ?? 0;
                            $selisih = $nomUtama - $nomKomp;
                        @endphp
                        <div class="py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <span class="text-slate-300">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</span>
                            <div class="flex items-center gap-3 font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px]">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                <span class="text-[10px] {{ $selisih >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    ({{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }})
                                </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="py-2.5 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1 text-emerald-400">
                        <span>Total Pendapatan:</span>
                        <div class="font-mono text-sm text-right">
                            <span>Rp {{ number_format($dataUtama['totalPendapatan'], 0, ',', '.') }}</span>
                            @if($mode === 'komparatif')
                            <span class="text-xs text-amber-400 ml-2">vs Rp {{ number_format($dataKomparatif['totalPendapatan'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Beban Operasional & HPP -->
            <div class="pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400 block mb-2">2. Beban Pokok &amp; Operasional</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($dataUtama['bebanData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['bebanData'][$kode]['nominal'] ?? 0;
                            $selisih = $nomUtama - $nomKomp;
                        @endphp
                        <div class="py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <span class="text-slate-300">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</span>
                            <div class="flex items-center gap-3 font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px]">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                <span class="text-[10px] {{ $selisih <= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    ({{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }})
                                </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="py-2.5 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1 text-rose-400">
                        <span>Total Beban Usaha:</span>
                        <div class="font-mono text-sm text-right">
                            <span>Rp {{ number_format($dataUtama['totalBeban'], 0, ',', '.') }}</span>
                            @if($mode === 'komparatif')
                            <span class="text-xs text-amber-400 ml-2">vs Rp {{ number_format($dataKomparatif['totalBeban'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laba Bersih -->
            <div class="pt-4 border-t-2 border-slate-700/80">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-500/20 to-orange-500/10 border border-amber-500/30 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Laba Bersih Periode</span>
                        <p class="text-[10px] text-slate-400 mt-0.5">Pendapatan dikurangi Seluruh Beban Usaha</p>
                    </div>
                    <div class="text-right">
                        <strong class="text-xl font-black text-amber-400 font-mono">
                            Rp {{ number_format($dataUtama['labaBersih'], 0, ',', '.') }}
                        </strong>
                        @if($mode === 'komparatif')
                        <div class="text-xs font-mono text-slate-400">
                            Komparasi: Rp {{ number_format($dataKomparatif['labaBersih'], 0, ',', '.') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. LAPORAN POSISI KEUANGAN (BALANCE SHEET / NERACA) -->
        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
            <div class="pb-3 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-scale-balanced text-amber-400"></i>
                        <span>Neraca (Posisi Keuangan)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                        Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d F Y') }}
                        @if($mode === 'komparatif')
                        <span class="text-amber-400 ml-1">vs {{ \Carbon\Carbon::parse($tanggalSampaiKomparatif)->format('d F Y') }}</span>
                        @endif
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Balanced
                </span>
            </div>

            <!-- Aset (Aktiva) -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-400 block mb-2">Aset (Aktiva)</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($dataUtama['asetLancarData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['asetLancarData'][$kode]['nominal'] ?? 0;
                        @endphp
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $item['akun']->nama_akun }}</span>
                            <div class="font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px] ml-2">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @foreach($dataUtama['asetTetapData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['asetTetapData'][$kode]['nominal'] ?? 0;
                        @endphp
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $item['akun']->nama_akun }}</span>
                            <div class="font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px] ml-2">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="py-2 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1 text-sky-400">
                        <span>Total Aset:</span>
                        <div class="font-mono text-sm text-right">
                            <span>Rp {{ number_format($dataUtama['totalAset'], 0, ',', '.') }}</span>
                            @if($mode === 'komparatif')
                            <span class="text-xs text-amber-400 ml-2">vs Rp {{ number_format($dataKomparatif['totalAset'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kewajiban & Ekuitas (Pasiva) -->
            <div class="pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 block mb-2">Kewajiban &amp; Ekuitas (Pasiva)</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($dataUtama['kewajibanData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['kewajibanData'][$kode]['nominal'] ?? 0;
                        @endphp
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $item['akun']->nama_akun }}</span>
                            <div class="font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px] ml-2">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @foreach($dataUtama['ekuitasData'] as $kode => $item)
                        @php
                            $nomUtama = $item['nominal'];
                            $nomKomp = $dataKomparatif['ekuitasData'][$kode]['nominal'] ?? 0;
                        @endphp
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $item['akun']->nama_akun }}</span>
                            <div class="font-mono text-right">
                                <span class="text-white">Rp {{ number_format($nomUtama, 0, ',', '.') }}</span>
                                @if($mode === 'komparatif')
                                <span class="text-slate-400 text-[11px] ml-2">/ Rp {{ number_format($nomKomp, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="py-1.5 flex items-center justify-between text-slate-300 font-semibold text-amber-400">
                        <span>Laba Bersih Kumulatif</span>
                        <div class="font-mono text-right">
                            <span>Rp {{ number_format($dataUtama['labaBersihKumulatif'], 0, ',', '.') }}</span>
                            @if($mode === 'komparatif')
                            <span class="text-slate-400 text-[11px] ml-2">/ Rp {{ number_format($dataKomparatif['labaBersihKumulatif'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="py-2 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1 text-amber-400">
                        <span>Total Kewajiban &amp; Ekuitas:</span>
                        <div class="font-mono text-sm text-right">
                            <span>Rp {{ number_format($dataUtama['totalKewajibanEkuitas'], 0, ',', '.') }}</span>
                            @if($mode === 'komparatif')
                            <span class="text-xs text-amber-400 ml-2">vs Rp {{ number_format($dataKomparatif['totalKewajibanEkuitas'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Keseimbangan Neraca -->
            <div class="pt-2 text-center text-[11px] text-slate-400 border-t border-slate-800/80">
                <i class="fas fa-check-double text-emerald-400 mr-1"></i>
                Laporan Keuangan diverifikasi dan disupervisi langsung oleh Board of Director (Finance &amp; Tax).
            </div>
        </div>
    </div>
</div>
@endsection
