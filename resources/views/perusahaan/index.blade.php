@extends('layouts.admin')

@section('title', 'Profil Perusahaan, Dewan Direksi & Otorisasi Dokumen')

@section('content')
@php
    $currentTab = request('tab', 'perusahaan');
    $currentDoc = request('doc', 'sales_order');
@endphp

<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white flex items-center gap-2">
                <i class="fas fa-building-circle-check text-amber-400"></i>
                <span>Profil Korporasi, Dewan Direksi & Otorisasi Dokumen</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola identitas legal PT PBS, susunan pengurus Direksi (BOD), dan kustomisasi penandatangan Sales Order, Invoice & Faktur Pajak</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-3 py-1 rounded-full font-bold flex items-center gap-1.5">
                <i class="fas fa-shield-halved"></i> Tata Kelola B2B PT PBS - SIG Tuban
            </span>
        </div>
    </div>

    <!-- Alert Success / Errors -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-400 text-base"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-rose-400"></i>
                <span>Mohon periksa kesalahan input berikut:</span>
            </div>
            <ul class="list-disc list-inside pl-4 text-rose-200">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 overflow-x-auto">
        <a href="?tab=perusahaan" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $currentTab == 'perusahaan' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20' : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800' }}">
            <i class="fas fa-building"></i>
            <span>Identitas Legal Perusahaan</span>
        </a>
        <a href="?tab=bod" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $currentTab == 'bod' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20' : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800' }}">
            <i class="fas fa-users-tie"></i>
            <span>Daftar Board of Director (BOD)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentTab == 'bod' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300' }}">{{ $directors->count() }}</span>
        </a>
        <a href="?tab=signatures" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $currentTab == 'signatures' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20' : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800' }}">
            <i class="fas fa-signature"></i>
            <span>Otorisasi Signature Dokumen (SO, Inv, Faktur Pajak)</span>
        </a>
    </div>

    <!-- TAB 1: IDENTITAS LEGAL PERUSAHAAN -->
    @if($currentTab == 'perusahaan')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Form Update Data Perusahaan -->
        <div class="lg:col-span-7 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-6">
            <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fas fa-landmark text-amber-400"></i>
                <span>Data Korporasi & Rekening Perbankan PT PBS</span>
            </h3>

            <form method="POST" action="{{ route('perusahaan.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Resmi Perusahaan <span class="text-rose-400">*</span></label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan ?? 'PT Pinastika Bhakti Semesta') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Singkatan / Inisial <span class="text-rose-400">*</span></label>
                        <input type="text" name="singkatan" value="{{ old('singkatan', $perusahaan->singkatan ?? 'PBS') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Pokok Wajib Pajak (NPWP)</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $perusahaan->npwp ?? '01.234.567.8-602.000') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-amber-300 font-mono text-xs focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kuasa Direksi (Finance & Tax) <span class="text-rose-400">*</span></label>
                        <input type="text" name="bod_finance_tax" value="{{ old('bod_finance_tax', $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white font-bold text-xs focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Kantor Pusat</label>
                    <textarea name="alamat" rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-2 focus:ring-amber-500">{{ old('alamat', $perusahaan->alamat ?? 'Jln Raya ByPass no 08 Kedungsari Magersari Kota Mojokerto') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kota / Kabupaten</label>
                        <input type="text" name="kota" value="{{ old('kota', $perusahaan->kota ?? 'Kota Mojokerto') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Provinsi</label>
                        <input type="text" name="provinsi" value="{{ old('provinsi', $perusahaan->provinsi ?? 'Jawa Timur') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Telepon Resmi / WhatsApp</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $perusahaan->telepon ?? '082131763686') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Resmi</label>
                        <input type="email" name="email" value="{{ old('email', $perusahaan->email ?? 'pt.pinastikabhaktisemesta@gmail.com') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Website Resmi</label>
                        <input type="text" name="website" value="{{ old('website', $perusahaan->website ?? 'www.pinastika.co.id') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                </div>

                <!-- Rekening Bank Resmi PT PBS -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider">
                        <i class="fas fa-money-check-dollar"></i>
                        <span>Rekening Bank Resmi Penampung Pembayaran</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Nama Bank</label>
                            <input type="text" name="bank_nama" value="{{ old('bank_nama', $perusahaan->bank_nama ?? 'Bank Mandiri') }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Nomor Rekening</label>
                            <input type="text" name="bank_rekening" value="{{ old('bank_rekening', $perusahaan->bank_rekening ?? '142-00-1234567-8') }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-amber-300 font-mono text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Atas Nama Rekening</label>
                            <input type="text" name="bank_atas_nama" value="{{ old('bank_atas_nama', $perusahaan->bank_atas_nama ?? 'PT Pinastika Bhakti Semesta') }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3">
                    @if(auth()->user()->canMutate())
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan Identitas Korporasi</span>
                    </button>
                    @else
                    <span class="px-4 py-2 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
                    </span>
                    @endif
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Summary Preview Kartu Identitas -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Official Corporate Card -->
            <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-br from-slate-900 via-slate-900 to-amber-950/30 p-6 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-amber-400 pointer-events-none">
                    <i class="fas fa-building"></i>
                </div>
                <div class="flex items-center gap-3 border-b border-slate-800/80 pb-4 mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500 text-slate-950 font-black text-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        {{ $perusahaan->singkatan ?? 'PBS' }}
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white leading-tight">{{ $perusahaan->nama_perusahaan ?? 'PT Pinastika Bhakti Semesta' }}</h4>
                        <span class="text-[10px] text-amber-400 font-bold tracking-widest uppercase">Waste Management & RDF Supplier</span>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">NPWP Legal:</span>
                        <span class="text-amber-300 font-mono font-bold">{{ $perusahaan->npwp ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Rekening Resmi:</span>
                        <span class="text-white font-mono">{{ $perusahaan->bank_nama ?? 'Bank Mandiri' }} - {{ $perusahaan->bank_rekening ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">A.N Rekening:</span>
                        <span class="text-slate-200 font-medium">{{ $perusahaan->bank_atas_nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email Korespondensi:</span>
                        <span class="text-amber-400 font-mono">{{ $perusahaan->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Hotline / Telepon:</span>
                        <span class="text-white font-mono">{{ $perusahaan->telepon ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Dewan Direksi Mini -->
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i class="fas fa-user-tie text-amber-400"></i>
                        <span>Dewan Direksi (BOD) Terdaftar</span>
                    </h4>
                    <a href="?tab=bod" class="text-[10px] text-amber-400 hover:underline font-bold">Kelola Direksi &rarr;</a>
                </div>

                <div class="divide-y divide-slate-800 text-xs">
                    @forelse($directors as $d)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-amber-400 text-xs shrink-0">
                                    {{ substr($d->nama, 0, 1) }}
                                </div>
                                <div>
                                    <strong class="text-white block text-xs">{{ $d->nama }}</strong>
                                    <span class="text-[10px] text-amber-400">{{ $d->jabatan }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $d->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                {{ $d->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-500">Belum ada anggota Direksi yang didaftarkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 2: DAFTAR BOARD OF DIRECTOR (BOD) -->
    @if($currentTab == 'bod')
    <div class="space-y-6">
        <!-- Top Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-900/60 p-4 rounded-3xl border border-slate-800">
            <div>
                <h3 class="text-sm font-black text-white flex items-center gap-2">
                    <i class="fas fa-users-gear text-amber-400"></i>
                    <span>Struktur & Otoritas Dewan Direksi (Board of Directors)</span>
                </h3>
                <p class="text-xs text-slate-400">Daftar pengurus korporasi yang berwenang memberikan otorisasi komersial, perpajakan, dan operasional</p>
            </div>
            @if(auth()->user()->canMutate())
            <button onclick="openModal('modalAddDirector')" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition flex items-center gap-2 shrink-0">
                <i class="fas fa-user-plus"></i>
                <span>+ Tambah Anggota Direksi</span>
            </button>
            @else
            <span class="px-4 py-2 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5 shrink-0">
                <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
            </span>
            @endif
        </div>

        <!-- Directors Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($directors as $d)
                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl hover:border-amber-500/40 transition flex flex-col justify-between space-y-4 relative group">
                    <div class="space-y-4">
                        <!-- Top Card Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="h-14 w-14 rounded-2xl border-2 border-amber-500/40 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-xl text-amber-400 shadow-md shrink-0">
                                    @if($d->foto && file_exists(public_path($d->foto)))
                                        <img src="{{ asset($d->foto) }}" alt="{{ $d->nama }}" class="h-full w-full object-cover object-top">
                                    @else
                                        {{ substr($d->nama, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Urutan #{{ $d->urutan }}
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">{{ $d->nama }}</h4>
                                    <p class="text-xs text-amber-300/90 font-medium">{{ $d->jabatan }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details -->
                        <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                            @if($d->nik)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">NIK (KTP):</span>
                                    <span class="font-mono text-slate-200">{{ $d->nik }}</span>
                                </div>
                            @endif
                            @if($d->npwp)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">NPWP Pribadi:</span>
                                    <span class="font-mono text-amber-400">{{ $d->npwp }}</span>
                                </div>
                            @endif
                            @if($d->email)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Email:</span>
                                    <span class="font-mono text-slate-300 truncate max-w-[180px]">{{ $d->email }}</span>
                                </div>
                            @endif
                            @if($d->telepon)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">WhatsApp / Telp:</span>
                                    <span class="font-mono text-slate-300">{{ $d->telepon }}</span>
                                </div>
                            @endif
                            @if($d->keterangan)
                                <div class="pt-1 text-[10px] text-slate-400 italic border-t border-slate-800/60">
                                    "{{ $d->keterangan }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $d->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                            <i class="fas fa-circle text-[7px] mr-1 {{ $d->is_active ? 'text-emerald-400' : 'text-slate-500' }}"></i>
                            {{ $d->is_active ? 'Otorisasi Aktif' : 'Non-Aktif' }}
                        </span>

                        @if(auth()->user()->canMutate())
                        <div class="flex items-center gap-1.5">
                            <button onclick="editDirector({{ json_encode($d) }})" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-semibold border border-slate-700 transition flex items-center gap-1">
                                <i class="fas fa-pen-to-square"></i>
                                <span>Edit</span>
                            </button>
                            <form method="POST" action="{{ route('perusahaan.directors.destroy', $d->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Direksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white text-xs font-semibold border border-rose-500/20 transition">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-12 text-center rounded-3xl border border-dashed border-slate-800 bg-slate-900/30">
                    <i class="fas fa-users-slash text-4xl text-slate-600 mb-3"></i>
                    <p class="text-sm font-bold text-slate-400">Belum ada anggota Direksi yang didaftarkan</p>
                    <p class="text-xs text-slate-500 mt-1">Klik tombol "+ Tambah Anggota Direksi" di atas untuk menambahkan data pengurus korporasi.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- TAB 3: CUSTOMISASI OTORISASI SIGNATURE DOKUMEN -->
    @if($currentTab == 'signatures')
    <div class="space-y-6">
        <!-- Sub-Document Selector -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900/80 p-4 rounded-3xl border border-slate-800">
            <div class="flex flex-wrap items-center gap-2">
                <a href="?tab=signatures&doc=sales_order" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentDoc == 'sales_order' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                    <i class="fas fa-file-contract"></i>
                    <span>Sales Order (SO)</span>
                </a>
                <a href="?tab=signatures&doc=invoice" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentDoc == 'invoice' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Commercial Invoice</span>
                </a>
                <a href="?tab=signatures&doc=faktur_pajak" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentDoc == 'faktur_pajak' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                    <i class="fas fa-file-shield"></i>
                    <span>Faktur Pajak Standar / WAPU</span>
                </a>
                <a href="?tab=signatures&doc=surat_jalan" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentDoc == 'surat_jalan' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                    <i class="fas fa-truck-ramp-box"></i>
                    <span>Surat Jalan (DO)</span>
                </a>
                <a href="?tab=signatures&doc=purchase_order" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentDoc == 'purchase_order' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                    <i class="fas fa-cart-flatbed"></i>
                    <span>Purchase Order (PO)</span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('perusahaan.signatures.reset') }}" onsubmit="return confirm('Kembalikan susunan penandatangan ke standar resmi PT PBS?')">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5">
                        <i class="fas fa-rotate-left"></i>
                        <span>Reset Default</span>
                    </button>
                </form>
            </div>
        </div>

        @php
            $activeSignatures = $signatures->get($currentDoc, collect());
            $docTitles = [
                'sales_order' => 'Sales Order (SO) - Pasokan RDF & Daur Ulang',
                'invoice' => 'Commercial Invoice Tagihan Resmi PT PBS',
                'faktur_pajak' => 'Faktur Pajak Standar / Penyerahan BKP Pemungut BUMN (030)',
                'surat_jalan' => 'Surat Jalan / Delivery Order (Pengawalan Armada)',
                'purchase_order' => 'Purchase Order (PO) ke TPST Mitra',
            ];
        @endphp

        <!-- Form Editor Otorisasi Signature Dokumen Terpilih -->
        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-800 pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                        Pengaturan Penandatangan Dokumen
                    </span>
                    <h3 class="text-base font-black text-white mt-1">{{ $docTitles[$currentDoc] ?? 'Dokumen Komersial' }}</h3>
                </div>
                <div class="text-xs text-slate-400">
                    Total: <strong class="text-amber-400">{{ $activeSignatures->count() }} Blok Tanda Tangan</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('perusahaan.signatures.update') }}" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_doc" value="{{ $currentDoc }}">

                <div class="grid grid-cols-1 md:grid-cols-{{ min(4, max(1, $activeSignatures->count())) }} gap-6">
                    @foreach($activeSignatures as $idx => $sig)
                        <div class="rounded-2xl border-2 border-slate-800 bg-slate-950/80 p-4 space-y-3 relative group">
                            <input type="hidden" name="signatures[{{ $idx }}][id]" value="{{ $sig->id }}">
                            
                            <!-- Header Blok -->
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">
                                    Posisi #{{ $idx + 1 }} ({{ $sig->posisi_kode }})
                                </span>
                                
                                <!-- Pilih Cepat dari BOD Dropdown -->
                                <select onchange="applyDirectorToBlock(this, {{ $idx }})" class="px-2 py-1 rounded-lg bg-slate-900 border border-slate-700 text-amber-300 text-[10px] font-medium focus:ring-1 focus:ring-amber-500">
                                    <option value="">⚡ Pilih Cepat dari BOD</option>
                                    @foreach($directors as $d)
                                        <option value="{{ $d->id }}" data-nama="{{ $d->nama }}" data-jabatan="{{ $d->jabatan }}" {{ $sig->director_id == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama }} ({{ $d->jabatan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="signatures[{{ $idx }}][director_id]" id="dir_id_{{ $idx }}" value="{{ $sig->director_id }}">

                            <!-- Form Inputs -->
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Judul Otorisasi (Label Atas)</label>
                                <input type="text" name="signatures[{{ $idx }}][label_judul]" value="{{ old('signatures.'.$idx.'.label_judul', $sig->label_judul) }}" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-semibold">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Nama Pejabat Penandatangan</label>
                                <input type="text" name="signatures[{{ $idx }}][nama_penandatangan]" id="nama_{{ $idx }}" value="{{ old('signatures.'.$idx.'.nama_penandatangan', $sig->nama_penandatangan) }}" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-amber-400 text-xs font-bold">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Jabatan Penandatangan</label>
                                <input type="text" name="signatures[{{ $idx }}][jabatan_penandatangan]" id="jabatan_{{ $idx }}" value="{{ old('signatures.'.$idx.'.jabatan_penandatangan', $sig->jabatan_penandatangan) }}" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Nama Organisasi / Entitas</label>
                                <input type="text" name="signatures[{{ $idx }}][organisasi]" value="{{ old('signatures.'.$idx.'.organisasi', $sig->organisasi) }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Catatan Khusus / Meterai / Kota</label>
                                <input type="text" name="signatures[{{ $idx }}][catatan]" value="{{ old('signatures.'.$idx.'.catatan', $sig->catatan) }}" placeholder="e.g. METERAI ELEKTRONIK / Mojokerto" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs font-mono">
                            </div>

                            <!-- Preview Tampilan Cetak Mini -->
                            <div class="mt-3 p-3 rounded-xl bg-slate-900 border border-dashed border-slate-700 text-center text-xs space-y-4">
                                <span class="text-[10px] text-slate-400 block">{{ $sig->label_judul }}</span>
                                @if($sig->catatan)
                                    <span class="inline-block border border-dashed border-slate-600 px-2 py-0.5 text-[8px] text-slate-400 font-mono rounded">
                                        {{ $sig->catatan }}
                                    </span>
                                @else
                                    <div class="h-6"></div>
                                @endif
                                <div class="border-t border-slate-600 pt-1">
                                    <strong class="text-white block text-[11px]">{{ $sig->nama_penandatangan }}</strong>
                                    <span class="text-[9px] text-amber-400 block">{{ $sig->jabatan_penandatangan }}</span>
                                    @if($sig->organisasi)
                                        <span class="text-[8px] text-slate-400 block">{{ $sig->organisasi }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-800">
                    @if(auth()->user()->canMutate())
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                        <i class="fas fa-check-double"></i>
                        <span>Simpan Otorisasi {{ $docTitles[$currentDoc] ?? 'Dokumen' }}</span>
                    </button>
                    @else
                    <span class="px-4 py-2 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
                    </span>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH DIREKSI -->
<!-- ========================================================================= -->
<div id="modalAddDirector" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-user-plus text-amber-400"></i>
                <span>Tambah Anggota Dewan Direksi (BOD)</span>
            </h3>
            <button onclick="closeModal('modalAddDirector')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" action="{{ route('perusahaan.directors.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap & Gelar <span class="text-rose-400">*</span></label>
                    <input type="text" name="nama" placeholder="e.g. Kurniawan, S.E., Ak., CA., M.Ak." required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan Resmi <span class="text-rose-400">*</span></label>
                    <input type="text" name="jabatan" placeholder="e.g. Board of Director (Finance & Tax)" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Urutan Hierarki</label>
                    <input type="number" name="urutan" value="{{ ($directors->max('urutan') ?? 0) + 1 }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor KTP (NIK)</label>
                    <input type="text" name="nik" placeholder="3516..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">NPWP Pribadi</label>
                    <input type="text" name="npwp" placeholder="08.123..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Resmi</label>
                    <input type="email" name="email" placeholder="nama@pinastika.co.id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="telepon" placeholder="+62 8..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Tugas Pokok & Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Tanggung jawab otorisasi transaksi, perbankan, dan perpajakan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="add_active" value="1" checked class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                <label for="add_active" class="text-xs text-slate-300 font-medium">Status Otorisasi Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('modalAddDirector')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold">Simpan Direksi</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT DIREKSI -->
<!-- ========================================================================= -->
<div id="modalEditDirector" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-user-pen text-amber-400"></i>
                <span>Edit Data Anggota Dewan Direksi (BOD)</span>
            </h3>
            <button onclick="closeModal('modalEditDirector')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <form id="formEditDirector" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap & Gelar <span class="text-rose-400">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan Resmi <span class="text-rose-400">*</span></label>
                    <input type="text" name="jabatan" id="edit_jabatan" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Urutan Hierarki</label>
                    <input type="number" name="urutan" id="edit_urutan" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor KTP (NIK)</label>
                    <input type="text" name="nik" id="edit_nik" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">NPWP Pribadi</label>
                    <input type="text" name="npwp" id="edit_npwp" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Resmi</label>
                    <input type="email" name="email" id="edit_email" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="telepon" id="edit_telepon" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Tugas Pokok & Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="edit_active" value="1" class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                <label for="edit_active" class="text-xs text-slate-300 font-medium">Status Otorisasi Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('modalEditDirector')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function editDirector(director) {
    const form = document.getElementById('formEditDirector');
    form.action = `/perusahaan/directors/${director.id}`;

    document.getElementById('edit_nama').value = director.nama || '';
    document.getElementById('edit_jabatan').value = director.jabatan || '';
    document.getElementById('edit_urutan').value = director.urutan || 1;
    document.getElementById('edit_nik').value = director.nik || '';
    document.getElementById('edit_npwp').value = director.npwp || '';
    document.getElementById('edit_email').value = director.email || '';
    document.getElementById('edit_telepon').value = director.telepon || '';
    document.getElementById('edit_keterangan').value = director.keterangan || '';
    document.getElementById('edit_active').checked = Boolean(director.is_active);

    openModal('modalEditDirector');
}

function applyDirectorToBlock(selectElem, blockIdx) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    if (!selectedOption.value) return;

    const dirId = selectedOption.value;
    const nama = selectedOption.getAttribute('data-nama');
    const jabatan = selectedOption.getAttribute('data-jabatan');

    document.getElementById('dir_id_' + blockIdx).value = dirId;
    document.getElementById('nama_' + blockIdx).value = nama;
    document.getElementById('jabatan_' + blockIdx).value = jabatan;
}
</script>
@endsection
