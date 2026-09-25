<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Pinastika Bhakti Semesta - PBS-ERP & Corporate Governance</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #070f1e 0%, #0a1628 50%, #0d1b2a 100%);
            color: #e0e0e0;
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="min-h-screen text-slate-200 selection:bg-amber-500/30 selection:text-white">

    <!-- Ambient glowing backgrounds -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[500px] w-[500px] rounded-full bg-[#ff8c00]/10 blur-[140px]"></div>
        <div class="absolute top-1/3 -right-40 h-[600px] w-[600px] rounded-full bg-blue-600/10 blur-[150px]"></div>
        <div class="absolute -bottom-40 left-1/3 h-[500px] w-[500px] rounded-full bg-amber-500/10 blur-[140px]"></div>
    </div>

    <div class="relative mx-auto max-w-[1180px] px-6 py-4">
        <!-- Header & Nav -->
        <header class="flex items-center justify-between py-6">
            <a href="#" class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-white font-black text-xl shadow-xl shadow-orange-500/25 border border-amber-400/30">
                    PBS
                </div>
                <div>
                    <div class="text-xl font-black tracking-tight text-white flex items-center gap-1.5">
                        <span>PBS</span>
                        <span class="text-[#ff8c00]">-ERP</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">PT Pinastika Bhakti Semesta</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-300">
                <a href="#" class="hover:text-[#ff8c00] transition">Beranda</a>
                <a href="#about" class="hover:text-[#ff8c00] transition">Tentang PBS</a>
                <a href="#profil" class="hover:text-[#ff8c00] transition">Struktur Organisasi & BOD</a>
                <a href="#modules" class="hover:text-[#ff8c00] transition">Modul ERP Intern</a>
                <a href="#contact" class="hover:text-[#ff8c00] transition">Kontak</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-5 py-2.5 font-bold text-white shadow-lg shadow-orange-500/25 hover:bg-[#e07b00] hover:-translate-y-0.5 transition">
                        <i class="fas fa-chart-pie"></i>
                        <span>Panel Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-5 py-2.5 font-bold text-white shadow-lg shadow-orange-500/25 hover:bg-[#e07b00] hover:-translate-y-0.5 transition">
                        <i class="fas fa-user-shield"></i>
                        <span>Login Admin Internal</span>
                    </a>
                @endauth
            </nav>

            <!-- Mobile Menu Toggle Button -->
            <button id="mobileMenuBtn" class="md:hidden text-white p-2 rounded-lg hover:bg-slate-800">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </header>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden mb-6 p-6 rounded-2xl border border-slate-800 bg-slate-900/95 backdrop-blur-xl space-y-3">
            <a href="#" class="block text-slate-300 hover:text-amber-400 py-1">Beranda</a>
            <a href="#about" class="block text-slate-300 hover:text-amber-400 py-1">Tentang PBS</a>
            <a href="#profil" class="block text-slate-300 hover:text-amber-400 py-1">Struktur Organisasi & BOD</a>
            <a href="#modules" class="block text-slate-300 hover:text-amber-400 py-1">Modul ERP Intern</a>
            <a href="#contact" class="block text-slate-300 hover:text-amber-400 py-1">Kontak</a>
            <div class="pt-3 border-t border-slate-800">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-[#ff8c00] text-white font-bold">
                        <i class="fas fa-chart-pie"></i>
                        <span>Panel Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-[#ff8c00] text-white font-bold">
                        <i class="fas fa-user-shield"></i>
                        <span>Login Admin Internal</span>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Hero Section -->
        <section class="py-16 text-center lg:py-24">
            <div class="inline-flex flex-wrap items-center justify-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold tracking-wider text-amber-400 uppercase mb-6 backdrop-blur-md">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-400"></span>
                </span>
                <span>NIB: 1911210009629 &bull; PMDN &bull; Perizinan Berusaha Berbasis Risiko (OSS-RBA)</span>
            </div>

            <h1 class="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-[1.15]">
                Pionir Sirkular Ekonomi &amp; Energi Hijau <br>
                <span class="text-[#ff8c00] underline decoration-[#ff8c00]/40 decoration-wavy">PT Pinastika Bhakti Semesta</span>
            </h1>

            <p class="mx-auto mt-6 max-w-3xl text-base sm:text-lg leading-relaxed text-slate-300">
                Penyedia pasokan bahan bakar alternatif terbarukan <strong class="text-amber-400 font-bold">Refuse Derived Fuel (RDF)</strong> untuk <strong class="text-white">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>, pengolahan daur ulang plastik &amp; kompos, rekayasa permesinan mekanikal, serta tata kelola ERP enterprise berstandar SAK &amp; DJP.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-7 py-3.5 text-base font-bold text-white shadow-xl shadow-orange-500/25 hover:bg-[#e07b00] hover:-translate-y-0.5 transition">
                        <i class="fas fa-gauge-high"></i>
                        <span>Buka Dashboard BOD</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-7 py-3.5 text-base font-bold text-white shadow-xl shadow-orange-500/25 hover:bg-[#e07b00] hover:-translate-y-0.5 transition">
                        <i class="fas fa-lock"></i>
                        <span>Masuk ke Portal Internal</span>
                    </a>
                @endauth

                <a href="#about" class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900/60 px-7 py-3.5 text-base font-semibold text-white backdrop-blur-md hover:bg-slate-800 hover:border-slate-600 transition">
                    <i class="fas fa-file-contract text-amber-400"></i>
                    <span>Legalitas NIB &amp; KBLI Resmi</span>
                </a>
                <a href="#profil" class="inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-7 py-3.5 text-base font-semibold text-amber-300 backdrop-blur-md hover:bg-amber-500/20 transition">
                    <i class="fas fa-users-gear text-amber-400"></i>
                    <span>Struktur Organisasi &amp; BOD</span>
                </a>
            </div>
        </section>

        <!-- About Section: Legalitas NIB, KBLI & Visi Misi -->
        <section id="about" class="py-20 border-t border-slate-800/80 scroll-mt-6">
            <div class="text-center mb-14">
                <span class="text-xs font-bold tracking-widest text-[#ff8c00] uppercase bg-amber-500/10 border border-amber-500/20 px-3.5 py-1 rounded-full">
                    PROFIL LEGALITAS &amp; PORTOFOLIO BISNIS
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white mt-3">Tentang PT Pinastika Bhakti Semesta</h2>
                <p class="mx-auto mt-2 max-w-2xl text-xs sm:text-sm text-slate-400">Legalitas resmi Badan Koordinasi Penanaman Modal (BKPM) &bull; Perizinan Berusaha Berbasis Risiko OSS Republik Indonesia</p>
                <div class="mx-auto mt-4 h-1 w-20 rounded bg-[#ff8c00]"></div>
            </div>

            <!-- Corporate Identity Box from NIB -->
            <div class="mb-12 rounded-3xl border border-amber-500/30 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-xs">
                    <div class="space-y-1 border-b md:border-b-0 md:border-r border-slate-800/80 pb-4 md:pb-0 pr-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Badan Hukum &amp; Nama Usaha:</span>
                        <h4 class="text-sm font-black text-white">PT PINASTIKA BHAKTI SEMESTA</h4>
                        <span class="text-[10px] text-amber-400 font-semibold block">Status Modal: PMDN (Penanaman Modal Dalam Negeri)</span>
                    </div>

                    <div class="space-y-1 border-b md:border-b-0 lg:border-r border-slate-800/80 pb-4 md:pb-0 pr-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Induk Berusaha (NIB):</span>
                        <div class="text-sm font-black font-mono text-amber-400 tracking-wider">1911210009629</div>
                        <span class="text-[10px] text-slate-400 block">Diterbitkan: 19 November 2021 (BKPM RI)</span>
                    </div>

                    <div class="space-y-1 border-b md:border-b-0 md:border-r border-slate-800/80 pb-4 md:pb-0 pr-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Pokok Wajib Pajak (NPWP):</span>
                        <div class="text-sm font-black font-mono text-emerald-400 tracking-wider">04.368.823.2-602.000</div>
                        <span class="text-[10px] text-slate-400 block">KPP Pratama Mojokerto &bull; WAPU BUMN 030</span>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alamat Kantor &amp; Workshop:</span>
                        <div class="text-xs font-bold text-white leading-snug">JL. BYPASS NO 8 KEDUNGSARI, GUNUNGGEDANGAN, MAGERSARI</div>
                        <span class="text-[10px] text-amber-400 block">Kota Mojokerto, Jawa Timur (61315)</span>
                    </div>
                </div>
            </div>

            <!-- 2-Column: About Description & Visi Misi -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start mb-16">
                <!-- Left: Corporate Summary -->
                <div class="space-y-5 lg:col-span-5">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 sm:p-7 shadow-xl space-y-4">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-building-circle-check text-amber-400"></i>
                            <span>Penggerak Rantai Pasok Hijau Nasional</span>
                        </h3>
                        <p class="text-xs sm:text-sm leading-relaxed text-slate-300 text-justify">
                            <strong class="text-white font-bold">PT Pinastika Bhakti Semesta (PBS)</strong> adalah perusahaan sirkular ekonomi berbadan hukum resmi yang beroperasi di Mojokerto, Jawa Timur. PBS memadukan teknologi pengolahan sampah anorganik &amp; organik, pemilahan dan pencucian plastik (*Cuci Giling / CUGIL*), rekayasa permesinan mekanikal, serta produksi bahan bakar alternatif ramah lingkungan <strong class="text-amber-400">Refuse Derived Fuel (RDF)</strong>.
                        </p>
                        <p class="text-xs leading-relaxed text-slate-400 text-justify">
                            Dalam rangka mendukung program Dekarbonisasi Nasional dan transisi energi bersih, PT PBS menjadi mitra penyedia pasokan RDF ke <strong class="text-slate-200">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>, sekaligus menyerap dan mengolah sampah dari berbagai TPST mitra daerah se-Jawa Timur.
                        </p>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-3 text-center">
                                <div class="text-xl font-black text-amber-400">4 KBLI</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Bidang Usaha OSS Resmi</div>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-3 text-center">
                                <div class="text-xl font-black text-emerald-400">SIG Tuban</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Supply RDF Partner</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visi & Misi Perusahaan (KEREN & STRATEGIS) -->
                <div class="space-y-6 rounded-3xl border-2 border-amber-500/40 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 p-6 sm:p-8 shadow-2xl backdrop-blur-md lg:col-span-7">
                    <!-- VISI -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-amber-400 font-black text-sm uppercase tracking-wider">
                            <div class="h-8 w-8 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400">
                                <i class="fas fa-eye text-xs"></i>
                            </div>
                            <span>Visi Korporasi</span>
                        </div>
                        <blockquote class="border-l-4 border-amber-500 pl-4 py-1 text-sm sm:text-base font-semibold text-white italic leading-relaxed bg-amber-500/5 rounded-r-xl">
                            "Menjadi pelopor terdepan industri sirkular ekonomi dan energi hijau terbarukan (Waste-to-Energy) di Indonesia, yang mengintegrasikan pengolahan sampah presisi, rekayasa permesinan mandiri, dan tata kelola korporasi akuntabel demi keberlanjutan lingkungan dan kemandirian industri nasional."
                        </blockquote>
                    </div>

                    <!-- MISI (5 PILAR STRATEGIS) -->
                    <div class="border-t border-slate-800/80 pt-5 space-y-3">
                        <div class="flex items-center gap-2 text-amber-400 font-black text-sm uppercase tracking-wider mb-2">
                            <div class="h-8 w-8 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400">
                                <i class="fas fa-bullseye text-xs"></i>
                            </div>
                            <span>Misi Strategis Korporasi</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-950/60 p-3 border border-slate-800/80">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center shrink-0 text-[11px]">1</span>
                                <div class="text-slate-300">
                                    <strong class="text-white block font-bold mb-0.5">Akselerasi Transisi Energi Bersih (Waste-to-Energy):</strong>
                                    Memproduksi pasokan bahan bakar alternatif <em>Refuse Derived Fuel (RDF)</em> berkualitas tinggi berkalori standar industri semen (PT Semen Indonesia Group) sebagai substitusi batubara ramah lingkungan.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-950/60 p-3 border border-slate-800/80">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center shrink-0 text-[11px]">2</span>
                                <div class="text-slate-300">
                                    <strong class="text-white block font-bold mb-0.5">Ekosistem Sirkular Ekonomi Berkelanjutan:</strong>
                                    Membangun rantai pasok terintegrasi dari hulu ke hilir bersama TPST mitra daerah se-Jawa Timur guna meminimalisir residu ke TPA dan memberdayakan ekonomi sirkular masyarakat.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-950/60 p-3 border border-slate-800/80">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center shrink-0 text-[11px]">3</span>
                                <div class="text-slate-300">
                                    <strong class="text-white block font-bold mb-0.5">Inovasi Rekayasa Permesinan &amp; Manufaktur Mandiri:</strong>
                                    Mengembangkan riset desain, manufaktur mesin pencacah (<em>shredder/crusher</em>), pemilah, dan instalasi mekanikal berteknologi tepat guna secara mandiri.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-950/60 p-3 border border-slate-800/80">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center shrink-0 text-[11px]">4</span>
                                <div class="text-slate-300">
                                    <strong class="text-white block font-bold mb-0.5">Integritas Tata Kelola Keuangan &amp; Kepatuhan Pajak (SAK &amp; DJP):</strong>
                                    Menerapkan standar akuntansi berintegritas tinggi, pengawasan anggaran ketat, serta kepatuhan pemungutan PPN WAPU BUMN 030 dan PPh secara tertib.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-950/60 p-3 border border-slate-800/80">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center shrink-0 text-[11px]">5</span>
                                <div class="text-slate-300">
                                    <strong class="text-white block font-bold mb-0.5">Kemitraan Industri Terpercaya &amp; Budaya K3:</strong>
                                    Menjaga komitmen mutu spesifikasi pasokan, ketepatan jadwal pengiriman tonase harian, keselamatan kerja (K3), serta kepatuhan lingkungan hidup.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── PORTOFOLIO 4 KODE KBLI RESMI (LAMPIRAN NIB OSS BKPM) ─────────── -->
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 sm:p-10 shadow-2xl backdrop-blur-md space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)</span>
                        <h3 class="text-lg font-black text-white mt-0.5 flex items-center gap-2">
                            <i class="fas fa-layer-group text-amber-400"></i>
                            <span>4 Bidang Usaha Berizin Resmi PT Pinastika Bhakti Semesta</span>
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 font-mono">
                        Lampiran NIB No. <strong class="text-amber-400">1911210009629</strong>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- KBLI 28199 -->
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4 space-y-2 hover:border-amber-500/40 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-black font-mono border border-amber-500/20">
                                KBLI 28199
                            </span>
                            <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                Engineering
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white">Industri Mesin Untuk Keperluan Umum Lainnya Ytdl</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Perakitan konveyor, elevator bahan, saringan putar (<em>trommel screen</em>), blower, dan mesin penunjang umum pabrik.
                        </p>
                    </div>

                    <!-- KBLI 28299 -->
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4 space-y-2 hover:border-amber-500/40 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-black font-mono border border-amber-500/20">
                                KBLI 28299
                            </span>
                            <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                Mesin Khusus RDF
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white">Industri Mesin Keperluan Khusus Lainnya</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Manufaktur mesin shredder RDF, crusher plastik, mesin press hidrolik, dan mesin pencuci sentrifugal sampah daur ulang.
                        </p>
                    </div>

                    <!-- KBLI 38212 -->
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4 space-y-2 hover:border-amber-500/40 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-xs font-black font-mono border border-emerald-500/30">
                                KBLI 38212
                            </span>
                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">
                                Organik &amp; Kompos
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white">Produksi Kompos Sampah Organik</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Pengolahan sampah organik dan biomassa dari pasar dan TPST mitra menjadi pupuk kompos berkualitas dan media tanam.
                        </p>
                    </div>

                    <!-- KBLI 38302 -->
                    <div class="rounded-2xl border-2 border-amber-500/50 bg-slate-950/80 p-4 space-y-2 hover:border-amber-400 transition relative group">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-500 text-slate-950 text-xs font-black font-mono">
                                KBLI 38302
                            </span>
                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                Sektor Utama RDF
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white">Pemulihan Material Barang Bukan Logam</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Pemilahan, pencacahan, pencucian, dan pengolahan sampah anorganik/plastik menjadi bahan baku daur ulang dan <strong>Refuse Derived Fuel (RDF)</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- Profile & Organizational Structure Section -->
        <!-- ========================================================================= -->
        <section id="profil" class="py-20 border-t border-slate-800/80 scroll-mt-6">
            <span id="profile" class="sr-only"></span>

            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest text-[#ff8c00] uppercase bg-amber-500/10 border border-amber-500/20 px-3.5 py-1 rounded-full">
                    TATA KELOLA KORPORASI &amp; STRUKTUR ORGANISASI
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white mt-3">
                    Struktur Organisasi &amp; Dewan Direksi PT PBS
                </h2>
                <p class="mx-auto mt-3 max-w-3xl text-sm sm:text-base text-slate-400 leading-relaxed">
                    Sinergi strategis pengolahan sampah daur ulang menjadi <strong class="text-amber-400">Refuse Derived Fuel (RDF)</strong>, tata kelola keuangan presisi, jejaring TPST mitra se-Jawa Timur, dan kemitraan pasokan bahan bakar alternatif ke <strong class="text-white">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>.
                </p>
                <div class="mx-auto mt-4 h-1 w-20 rounded bg-[#ff8c00]"></div>
            </div>

            <!-- ─── BAGAN STRUKTUR ORGANISASI KORPORASI (ORGANIZATIONAL TREE) ─────── -->
            <div id="struktur" class="mb-20 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 sm:p-10 shadow-2xl backdrop-blur-md relative overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-800 pb-5 mb-8">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Hierarki Pengurus &amp; Rantai Pasok Industri</span>
                        <h3 class="text-lg font-black text-white mt-0.5 flex items-center gap-2">
                            <i class="fas fa-sitemap text-amber-400"></i>
                            <span>Bagan Struktur Organisasi &amp; Komando Tata Kelola</span>
                        </h3>
                    </div>
                    <span class="hidden sm:inline-flex text-[11px] bg-amber-500/10 text-amber-400 border border-amber-500/30 px-3 py-1 rounded-full font-bold items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> Sesuai Akta Korporasi &amp; KBLI
                    </span>
                </div>

                <!-- TREE VISUALIZATION -->
                <div class="space-y-8">
                    <!-- LEVEL 1: RUPS & DEWAN KOMISARIS -->
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="w-full max-w-md rounded-2xl border-2 border-amber-500/50 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 text-center shadow-xl relative group hover:border-amber-400 transition">
                                <span class="text-[9px] font-black uppercase tracking-widest text-amber-400 bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/30">
                                    Otoritas Kebijakan Tertinggi
                                </span>
                                <h4 class="text-sm font-black text-white mt-1.5 uppercase">Rapat Umum Pemegang Saham (RUPS) &amp; Dewan Komisaris</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Pengawasan Strategis, Pengesahan Rencana Bisnis &amp; Tata Kelola Korporasi</p>
                            </div>
                        </div>

                        <!-- 2 KOMISARIS CARDS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                            <!-- KOMISARIS UTAMA -->
                            <div class="rounded-2xl border-2 border-amber-500/60 bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 p-4 text-center shadow-lg hover:border-amber-400 transition">
                                <div class="h-10 w-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto mb-2.5 font-bold text-base">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400">Komisaris Utama</span>
                                <h4 class="text-sm font-black text-white mt-0.5">Safira Putri Imanina</h4>
                                <p class="text-xs text-amber-300/80 font-medium">Komisaris Utama</p>
                            </div>

                            <!-- KOMISARIS -->
                            <div class="rounded-2xl border border-slate-700 bg-slate-950/90 p-4 text-center shadow-lg hover:border-amber-500/50 transition">
                                <div class="h-10 w-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto mb-2.5 font-bold text-base">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Komisaris</span>
                                <h4 class="text-sm font-black text-white mt-0.5">Handoko</h4>
                                <p class="text-xs text-amber-300/80 font-medium">Komisaris</p>
                            </div>
                        </div>
                    </div>

                    <!-- CONNECTOR LINE 1 -->
                    <div class="flex justify-center -my-2">
                        <div class="w-0.5 h-8 bg-gradient-to-b from-amber-500/80 to-amber-500/30"></div>
                    </div>

                    <!-- LEVEL 2: DEWAN DIREKSI (BOARD OF DIRECTORS) -->
                    <div class="space-y-4">
                        <div class="text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-800/80 px-3 py-1 rounded-full border border-slate-700">
                                Dewan Direksi (Board of Directors - BOD)
                            </span>
                        </div>

                        <!-- 4 BOD CARDS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- DIREKTUR UTAMA -->
                            <div class="rounded-2xl border-2 border-amber-500/70 bg-gradient-to-b from-slate-900 to-slate-950 p-5 text-center shadow-xl hover:border-amber-400 transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center mx-auto mb-3 font-bold text-lg shadow-md shadow-amber-500/30">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400">President Director</span>
                                    <h4 class="text-sm font-black text-white mt-1">Sendy Hartono</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur Utama</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-800 text-[11px] text-slate-400 leading-snug">
                                    Penanggung jawab kebijakan umum, kepatuhan legal, dan kepemimpinan korporasi PT PBS.
                                </div>
                            </div>

                            <!-- DIREKTUR AKUNTANSI & UMUM -->
                            <div class="rounded-2xl border-2 border-amber-500/70 bg-gradient-to-b from-slate-900 to-slate-950 p-5 text-center shadow-xl hover:border-amber-400 transition relative flex flex-col justify-between">
                                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                    <span class="bg-amber-500 text-slate-950 text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow">
                                        Akuntansi &amp; Umum
                                    </span>
                                </div>
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center mx-auto mb-3 font-black text-lg shadow-md shadow-amber-500/30">
                                        <i class="fas fa-scale-balanced"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400">Director</span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Kurniawan</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur - Akuntansi &amp; Umum</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-800 text-[11px] text-slate-300 leading-snug">
                                    Pengelolaan akuntansi, keuangan, perpajakan DJP, audit SAK, dan administrasi umum korporasi.
                                </div>
                            </div>

                            <!-- DIREKTUR TEKNIK & OPERASIONAL -->
                            <div class="rounded-2xl border border-slate-700 bg-slate-950/90 p-5 text-center shadow-lg hover:border-amber-500/60 transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto mb-3 font-bold text-lg">
                                        <i class="fas fa-gears"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400">Director</span>
                                    <h4 class="text-sm font-black text-white mt-1">Yudo Ariyanto</h4>
                                    <p class="text-xs text-amber-300/80 font-medium">Direktur - Teknik &amp; Operasional</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-800 text-[11px] text-slate-400 leading-snug">
                                    Rekayasa teknis permesinan daur ulang, pemeliharaan fasilitas pabrik, dan keandalan operasional.
                                </div>
                            </div>

                            <!-- DIREKTUR OPERASIONAL -->
                            <div class="rounded-2xl border border-slate-700 bg-slate-950/90 p-5 text-center shadow-lg hover:border-amber-500/60 transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto mb-3 font-bold text-lg">
                                        <i class="fas fa-industry"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400">Director</span>
                                    <h4 class="text-sm font-black text-white mt-1">Winardi Sugianto, SE</h4>
                                    <p class="text-xs text-amber-300/80 font-medium">Direktur - Operasional</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-800 text-[11px] text-slate-400 leading-snug">
                                    Manajemen rantai pasok pengolahan sampah RDF, logistik pengiriman, dan operasional lapangan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONNECTOR LINE 2 -->
                    <div class="flex justify-center -my-4">
                        <div class="w-0.5 h-8 bg-gradient-to-b from-amber-500/30 to-slate-700"></div>
                    </div>

                    <!-- LEVEL 3: DIVISI PELAKSANA OPERASIONAL LAPANGAN (4 DIVISI) -->
                    <div class="space-y-4">
                        <div class="text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-800/80 px-3 py-1 rounded-full border border-slate-700">
                                Divisi &amp; Unit Pelaksana Rantai Pasok (Supply Chain Ecosystem)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Divisi 1 -->
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 hover:border-amber-500/40 transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-amber-400">
                                    <i class="fas fa-truck-ramp-box text-base"></i>
                                    <h5 class="text-xs font-bold text-white uppercase">Divisi Sourcing &amp; Mitra TPST</h5>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Pengambilan sampah terpilah dari TPST Megilan Lamongan, TPST Tambakrejo Sidoarjo, Sedati, &amp; Mojokerto.
                                </p>
                            </div>

                            <!-- Divisi 2 -->
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 hover:border-amber-500/40 transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-amber-400">
                                    <i class="fas fa-weight-scale text-base"></i>
                                    <h5 class="text-xs font-bold text-white uppercase">QC &amp; Jembatan Timbang</h5>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Inspeksi kadar air, kalori RDF (Kkal/Kg), toleransi susut, dan verifikasi timbangan asal vs tujuan.
                                </p>
                            </div>

                            <!-- Divisi 3 -->
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 hover:border-amber-500/40 transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-amber-400">
                                    <i class="fas fa-truck-front text-base"></i>
                                    <h5 class="text-xs font-bold text-white uppercase">Logistik &amp; Pasokan Tuban</h5>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Pengawalan armada ekspedisi truk/dump truck, penerbitan Surat Jalan (DO), &amp; BAST Pabrik Tuban.
                                </p>
                            </div>

                            <!-- Divisi 4 -->
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 hover:border-amber-500/40 transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-amber-400">
                                    <i class="fas fa-file-invoice-dollar text-base"></i>
                                    <h5 class="text-xs font-bold text-white uppercase">Akuntansi &amp; Pajak (DJP)</h5>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Penerbitan Invoice komersial, Faktur Pajak WAPU BUMN (030), &amp; pembukuan SAK di Bank Mandiri.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── DAFTAR KARTU PROFIL PENGURUS KORPORASI & BOD (DEWAN KOMISARIS & DEWAN DIREKSI) ─── -->
            <div class="mb-16">
                <div class="text-center mb-10">
                    <span class="text-xs font-bold tracking-widest text-[#ff8c00] uppercase">EXECUTIVE LEADERSHIP &amp; GOVERNANCE TEAM</span>
                    <h3 class="text-2xl font-black text-white mt-1">Pengurus Korporasi &amp; Dewan Direksi (BOD)</h3>
                    <p class="text-xs text-slate-400 mt-2 max-w-xl mx-auto">Kepemimpinan strategis, kepatuhan tata kelola, dan eksekusi operasional PT Pinastika Bhakti Semesta</p>
                    <div class="mx-auto mt-2.5 h-1 w-12 rounded bg-[#ff8c00]"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- 1. SAFIRA PUTRI IMANINA -->
                    <div class="rounded-3xl border-2 border-amber-500/60 bg-gradient-to-b from-slate-900 via-slate-900 to-amber-950/20 shadow-2xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-400 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border-2 border-amber-500/40 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl text-amber-400 shadow-md shrink-0">
                                    <i class="fas fa-shield-halved text-2xl text-amber-400"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Dewan Komisaris
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Safira Putri Imanina</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Komisaris-Utama</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Pengawasan tertinggi tata kelola korporasi, pengesahan arah kebijakan strategis, dan kepatuhan anggaran dasar perusahaan.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <i class="fas fa-certificate mr-1"></i> Pengawasan &amp; Tata Kelola Korporasi
                            </span>
                        </div>
                    </div>

                    <!-- 2. HANDOKO -->
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-500/60 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl text-slate-300 shadow-md shrink-0">
                                    <i class="fas fa-user-check text-2xl text-amber-400"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700">
                                        Dewan Komisaris
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Handoko</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Komisaris</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Fungsi pengawasan independen terhadap pelaksanaan kebijakan operasional dan kelangsungan usaha perseroan.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                <i class="fas fa-shield-check mr-1"></i> Pengawasan Independen
                            </span>
                        </div>
                    </div>

                    <!-- 3. SENDY HARTONO -->
                    <div class="rounded-3xl border-2 border-amber-500/70 bg-gradient-to-b from-slate-900 to-slate-950 shadow-2xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-400 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border-2 border-amber-500/50 bg-amber-500 text-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl shadow-md shrink-0">
                                    <i class="fas fa-crown text-2xl text-slate-950"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Dewan Direksi (BOD)
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Sendy Hartono</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur Utama</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Pemimpin eksekutif korporasi, penetapan kebijakan operasional menyeluruh, dan hubungan kelembagaan mitra industri.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <i class="fas fa-check-circle mr-1"></i> Pimpinan Eksekutif Perseroan
                            </span>
                        </div>
                    </div>

                    <!-- 4. KURNIAWAN -->
                    <div class="rounded-3xl border-2 border-amber-500/60 bg-gradient-to-b from-slate-900 via-slate-900 to-amber-950/20 shadow-2xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-400 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border-2 border-amber-500/40 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl text-amber-400 shadow-md shrink-0">
                                    @if(file_exists(public_path('images/ayahrompi.png')))
                                        <img src="{{ asset('images/ayahrompi.png') }}" alt="Kurniawan" class="h-full w-full object-cover object-top">
                                    @else
                                        K
                                    @endif
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Dewan Direksi (BOD)
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Kurniawan</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur - Akuntansi &amp; Umum</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Otorisasi anggaran, kepatuhan perpajakan DJP, audit pembukuan SAK, dan administrasi umum perseroan.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-shield-check mr-1"></i> Otoritas Keuangan &amp; Akuntansi
                            </span>
                        </div>
                    </div>

                    <!-- 5. YUDO ARIYANTO -->
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-500/60 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl text-amber-400 shadow-md shrink-0">
                                    <i class="fas fa-gears text-2xl text-amber-400"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Dewan Direksi (BOD)
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Yudo Ariyanto</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur &ndash; Teknik &amp; Operasional</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Rekayasa teknis permesinan daur ulang, kesiapan lini produksi RDF, dan standarisasi operasional mesin.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                <i class="fas fa-wrench mr-1"></i> Pengendalian Teknis &amp; Operasional
                            </span>
                        </div>
                    </div>

                    <!-- 6. WINARDI SUGIANTO, SE -->
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl p-6 flex flex-col justify-between space-y-4 hover:border-amber-500/60 transition">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden flex items-center justify-center font-black text-2xl text-amber-400 shadow-md shrink-0">
                                    <i class="fas fa-industry text-2xl text-amber-400"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        Dewan Direksi (BOD)
                                    </span>
                                    <h4 class="text-sm font-black text-white mt-1 leading-snug">Winardi Sugianto, SE</h4>
                                    <p class="text-xs text-amber-300 font-semibold">Direktur - Operasional</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-300 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
                                <div class="text-[11px] text-slate-300 leading-relaxed">
                                    Manajemen pasokan cacahan sampah, logistik pengiriman RDF, dan koordinasi penerimaan SIG Pabrik Tuban.
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="w-full text-center block px-3 py-1 rounded-xl text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-truck-fast mr-1"></i> Manajemen Operasional &amp; Rantai Pasok
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── DETAIL PROFIL EKSEKUTIF: BOD FINANCE & TAX (KURNIAWAN) ───────── -->
            <div class="rounded-3xl border border-amber-500/30 bg-slate-900/80 p-8 sm:p-12 shadow-2xl">
                <div class="flex flex-col items-center gap-12 lg:flex-row lg:items-center">
                    <!-- Profile Picture with Glow Effect -->
                    <div class="relative w-full max-w-[320px] shrink-0">
                        <div class="relative z-10 overflow-hidden rounded-3xl border border-slate-700 shadow-2xl bg-slate-900">
                            <img 
                                src="{{ asset('images/ayahrompi.png') }}" 
                                alt="Kurniawan - Board of Director (Finance & Tax)"
                                class="h-[380px] w-full object-cover object-top transition duration-300 hover:scale-105"
                                onerror="this.style.display='none'"
                            >
                        </div>
                        <!-- Ambient Glow -->
                        <div class="absolute -top-4 -left-4 h-32 w-32 rounded-full bg-amber-500/30 blur-2xl -z-0"></div>
                        <div class="absolute -bottom-4 -right-4 h-36 w-36 rounded-full bg-blue-600/30 blur-2xl -z-0"></div>
                    </div>

                    <!-- Profile Details -->
                    <div class="flex-1 space-y-5 text-center lg:text-left">
                        <span class="text-xs font-bold tracking-widest text-[#ff8c00] uppercase bg-amber-500/10 border border-amber-500/30 px-3 py-1 rounded-full">
                            Penanggung Jawab Keuangan &amp; Kepatuhan Pajak (DJP)
                        </span>
                        <h3 class="text-2xl font-black text-white sm:text-3xl leading-snug">
                            Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.
                        </h3>
                        <p class="text-xs text-amber-300 font-semibold uppercase tracking-wider">
                            Direktur - Akuntansi &amp; Umum &bull; PT Pinastika Bhakti Semesta
                        </p>

                        <!-- Badges -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2">
                            @foreach(['Chartered Accountant (CA)', 'Magister Akuntansi (M.Ak)', 'CMA', 'CIBA', 'CIAP', 'Ak.'] as $badge)
                                <span class="rounded-full border border-amber-500/40 bg-amber-500/10 px-3 py-1 text-xs font-bold text-amber-400">
                                    {{ $badge }}
                                </span>
                            @endforeach
                        </div>

                        <blockquote class="border-l-0 lg:border-l-4 border-amber-500 pl-0 lg:pl-4 italic text-slate-300 text-sm sm:text-base leading-relaxed py-1">
                            "Sebagai Direktur yang membidangi Akuntansi &amp; Umum pada PT Pinastika Bhakti Semesta, kami menjamin seluruh tata kelola transaksi pasokan RDF dan komoditas industri terkelola secara akuntabel, mematuhi standar SAK, memenuhi mekanisme pemungutan PPN WAPU BUMN, serta siap diaudit secara terbuka."
                        </blockquote>

                        <!-- Qualifications Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-left">
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-800 bg-slate-950/60 p-3.5 hover:border-amber-500/40 transition">
                                <i class="fas fa-award text-amber-400 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-white">Chartered Accountant (CA)</h4>
                                    <p class="text-[11px] text-slate-400">Ikatan Akuntan Indonesia</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-800 bg-slate-950/60 p-3.5 hover:border-amber-500/40 transition">
                                <i class="fas fa-graduation-cap text-amber-400 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-white">Magister Akuntansi (M.Ak)</h4>
                                    <p class="text-[11px] text-slate-400">Univ. Trunojoyo Madura</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-800 bg-slate-950/60 p-3.5 hover:border-amber-500/40 transition">
                                <i class="fas fa-chart-pie text-amber-400 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-white">Certified Mgt. Accountant (CMA)</h4>
                                    <p class="text-[11px] text-slate-400">Universitas Airlangga</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-800 bg-slate-950/60 p-3.5 hover:border-amber-500/40 transition">
                                <i class="fas fa-globe text-amber-400 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-white">Intl. Business Analysis (CIBA)</h4>
                                    <p class="text-[11px] text-slate-400">Univ. Kristen Petra</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Track Record Grid -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/50 p-6 text-center hover:border-amber-500/40 transition">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400">
                            <i class="fas fa-landmark text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-white mb-1.5">Sektor Publik &amp; BUMDesa Jatim</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Tenaga Ahli Klinik BUMDesa Provinsi Jawa Timur &amp; Tim Penilai BUMDesa Berprestasi Tingkat Provinsi (2015-2024).
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-950/50 p-6 text-center hover:border-amber-500/40 transition">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400">
                            <i class="fas fa-briefcase text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-white mb-1.5">Sektor Korporasi Multinasional</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Manajer Keuangan, Pengendalian Anggaran, Audit Internal &amp; CSR (Danone, USAID, Medco).
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-950/50 p-6 text-center hover:border-amber-500/40 transition">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-white mb-1.5">Akademisi &amp; Mentor Bisnis</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Dosen Tetap Akuntansi, Mentor Inkubasi Bisnis, dan Narasumber Ahli Keuangan &amp; Pajak.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modules Section -->
        <section id="modules" class="py-20 border-t border-slate-800/80">
            <div class="text-center mb-14">
                <span class="text-xs font-bold tracking-widest text-[#ff8c00] uppercase">ARSITEKTUR MODUL TERTUTUP</span>
                <h2 class="text-3xl font-extrabold text-white mt-1">Cakupan Modul PBS-ERP</h2>
                <div class="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 hover:border-amber-500/40 transition">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-book-journal-whills"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Akuntansi & Jurnal SAK</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Chart of Accounts lengkap, Jurnal Umum double-entry, Buku Besar, Neraca Saldo, dan Laporan Keuangan otomatis.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 hover:border-amber-500/40 transition">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Manajemen Pajak</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Monitoring PPN Keluaran/Masukan, Pemotongan PPh 21, 23, 4(2), tracking bukti setor (NTPN) dan SPT Masa.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 hover:border-amber-500/40 transition">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Approval Anggaran BOD</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pengajuan dana divisi, verifikasi pagu anggaran, approval berjenjang langsung oleh BOD Finance (Kurniawan).
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 hover:border-amber-500/40 transition">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-diagram-project"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Proyek & Billing Klien</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Manajemen kontrak proyek, pemantauan progress pekerjaan, penerbitan invoice termin, dan tracking piutang.
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 border-t border-slate-800/80">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-extrabold text-white">Kontak & Kantor Pusat</h2>
                <p class="text-xs text-slate-400 mt-1">PT Pinastika Bhakti Semesta</p>
                <div class="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]"></div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/60 shadow-2xl backdrop-blur-md">
                <div class="grid grid-cols-1 lg:grid-cols-12">
                    <div class="p-8 sm:p-12 lg:col-span-6 flex flex-col justify-center space-y-7">
                        <div>
                            <h3 class="text-xl font-bold text-white">Kantor Pusat Operasional</h3>
                            <p class="text-xs text-slate-400 mt-1">Hubungi kantor kami untuk urusan administratif, perpajakan, dan kemitraan.</p>
                        </div>

                        <div class="space-y-5 text-sm">
                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-location-dot"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-xs">Alamat Kantor Pusat</h4>
                                    <p class="text-xs text-slate-300 mt-0.5 font-medium leading-relaxed">
                                        {{ $perusahaan->alamat ?? 'Jln Raya ByPass no 08 Kedungsari Magersari Kota Mojokerto' }}
                                    </p>
                                    <span class="text-[10px] text-amber-400/80 font-mono block mt-0.5">Jawa Timur, Indonesia</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                                    <i class="fab fa-whatsapp text-lg text-emerald-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-xs">Telepon / WhatsApp Resmi</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <a href="https://wa.me/6282131763686" target="_blank" class="text-emerald-400 hover:text-emerald-300 font-mono font-bold hover:underline flex items-center gap-1.5">
                                            <span>+62 821 3176 3686 (082131763686)</span>
                                            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-xs">Email Korespondensi</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <a href="mailto:pt.pinastikabhaktisemesta@gmail.com" class="text-amber-400 hover:text-amber-300 font-mono hover:underline">
                                            pt.pinastikabhaktisemesta@gmail.com
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Embed with Specific Coordinates -->
                    <div class="min-h-[360px] lg:col-span-6 relative border-t lg:border-t-0 lg:border-l border-slate-800 bg-slate-950 flex flex-col">
                        <iframe 
                            title="Peta Kantor PT Pinastika Bhakti Semesta - Mojokerto"
                            src="https://maps.google.com/maps?q=-7.4655082105554476,112.45988780996359&hl=id&z=17&output=embed" 
                            class="h-full w-full border-0 filter grayscale invert contrast-90 opacity-85 min-h-[300px]"
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                        <div class="p-3 bg-slate-950/90 border-t border-slate-800 text-center flex items-center justify-between px-4 text-[11px]">
                            <span class="text-slate-400 font-mono text-[10px]">
                                <i class="fas fa-location-crosshairs text-amber-400 mr-1"></i> -7.465508, 112.459888
                            </span>
                            <a href="https://maps.google.com/?q=-7.4655082105554476,112.45988780996359" target="_blank" class="text-amber-400 hover:underline font-bold flex items-center gap-1">
                                <span>Buka di Google Maps</span>
                                <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-slate-800/80 text-center text-xs text-slate-500 space-y-3">
            <p>
                &copy; {{ date('Y') }} <strong class="text-slate-300">PT Pinastika Bhakti Semesta (PBS)</strong>. Hak Cipta Dilindungi Undang-Undang.
            </p>
            <p class="text-[11px] text-slate-600">
                Sistem tertutup (closed intranet). Percobaan akses tidak sah akan dicatat ke dalam audit trail keamanan.
            </p>
            <div class="pt-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-amber-400 hover:underline font-semibold">Buka Dashboard ERP</a>
                @else
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-amber-400 transition font-medium">Login Administrator Sistem</a>
                @endauth
            </div>
        </footer>
    </div>

    <script>
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('mobileMenu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>