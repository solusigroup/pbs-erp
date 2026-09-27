<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Pinastika Bhakti Semesta - Rekayasa Sirkular Ekonomi, RDF & Tata Kelola Korporasi</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- Tailwind CSS CDN & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            green: '#1B4D3E',
                            'green-light': '#246854',
                            'green-dark': '#13392e',
                            amber: '#F59E0B',
                            orange: '#FF8C00',
                            dark: '#0B0F19'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FFFFFF;
            color: #0B0F19;
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="min-h-screen text-[#0B0F19] selection:bg-[#1B4D3E] selection:text-white bg-white">

    <!-- ─── HEADER & NAVIGATION (Putih Bersih #FFFFFF, Teks Gelap #0B0F19, Navigasi Hijau Tua #1B4D3E) ─── -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-200">
        <div class="mx-auto max-w-[1240px] px-6 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="h-12 w-12 rounded-2xl bg-white p-1.5 flex items-center justify-center shadow-md shadow-slate-200 border border-slate-200 overflow-hidden shrink-0 group-hover:scale-105 transition duration-200">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PT Pinastika Bhakti Semesta" class="h-full w-full object-contain">
                </div>
                <div>
                    <div class="text-xl font-black tracking-tight text-[#0B0F19] flex items-center gap-1.5">
                        <span>PBS</span>
                        <span class="text-[#1B4D3E]">-ERP</span>
                    </div>
                    <p class="text-[10px] text-[#1B4D3E] font-bold uppercase tracking-wider">PT Pinastika Bhakti Semesta</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-700">
                <a href="#" class="text-[#1B4D3E] font-bold transition hover:text-[#246854] relative after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-[#1B4D3E]">Beranda</a>
                <a href="#about" class="hover:text-[#1B4D3E] transition">Tentang PBS</a>
                <a href="#engineering" class="hover:text-[#1B4D3E] transition flex items-center gap-1.5">
                    <span>Rekayasa Mesin &amp; RDF</span>
                    <span class="text-[9px] bg-[#1B4D3E]/10 text-[#1B4D3E] px-2 py-0.5 rounded-full font-bold">KBLI</span>
                </a>
                <a href="#profil" class="hover:text-[#1B4D3E] transition">Struktur Organisasi</a>
                <a href="#modules" class="hover:text-[#1B4D3E] transition">Modul ERP &amp; Pajak</a>
                <a href="#contact" class="hover:text-[#1B4D3E] transition">Kontak</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-[#F59E0B] hover:bg-[#d97706] text-white px-5 py-2.5 font-bold shadow-md shadow-amber-500/25 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-chart-pie"></i>
                        <span>Panel Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-[#1B4D3E] hover:bg-[#13392e] text-white px-5 py-2.5 font-bold shadow-md shadow-emerald-900/20 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-user-shield"></i>
                        <span>Login Admin Internal</span>
                    </a>
                @endauth
            </nav>

            <!-- Mobile Menu Toggle Button -->
            <button id="mobileMenuBtn" class="md:hidden text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div id="mobileMenu" class="hidden md:hidden px-6 pb-6 pt-2 bg-white border-b border-slate-200 space-y-3 shadow-lg">
            <a href="#" class="block text-[#1B4D3E] font-bold py-1.5 border-b border-slate-100">Beranda</a>
            <a href="#about" class="block text-slate-700 hover:text-[#1B4D3E] font-medium py-1.5 border-b border-slate-100">Tentang PBS</a>
            <a href="#engineering" class="block text-slate-700 hover:text-[#1B4D3E] font-medium py-1.5 border-b border-slate-100">Rekayasa Mesin &amp; RDF</a>
            <a href="#profil" class="block text-slate-700 hover:text-[#1B4D3E] font-medium py-1.5 border-b border-slate-100">Struktur Organisasi &amp; BOD</a>
            <a href="#modules" class="block text-slate-700 hover:text-[#1B4D3E] font-medium py-1.5 border-b border-slate-100">Modul ERP Intern</a>
            <a href="#contact" class="block text-slate-700 hover:text-[#1B4D3E] font-medium py-1.5 border-b border-slate-100">Kontak</a>
            <div class="pt-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-[#F59E0B] text-white font-bold shadow-md">
                        <i class="fas fa-chart-pie"></i>
                        <span>Panel Dashboard ERP</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-[#1B4D3E] text-white font-bold shadow-md">
                        <i class="fas fa-user-shield"></i>
                        <span>Login Admin Internal</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ─── HERO SECTION: Latar Belakang Gambar Platform + Overlay Hijau Botol Tipis & Putih Bersih ─── -->
    <section class="relative overflow-hidden bg-white border-b border-slate-200/80 py-20 lg:py-28">
        <!-- Background Image Platform Pengolahan Sampah & Konveyor 3D dengan Transparansi & Overlay Hijau Botol -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
            <img 
                src="{{ asset('images/facility-rdf-platform.jpg') }}" 
                alt="Platform Pengolahan Sampah Berteknologi PT Pinastika Bhakti Semesta" 
                class="w-full h-full object-cover object-center opacity-25 filter contrast-110 saturate-110"
            >
            <!-- Overlay Transparan Gradasi Hijau Botol (#1B4D3E) & Putih Bersih (#FFFFFF) -->
            <div class="absolute inset-0 bg-gradient-to-b from-white/95 via-white/85 to-white"></div>
            <div class="absolute inset-0 bg-[#1B4D3E]/10 mix-blend-multiply"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-[1180px] px-6 text-center">
            <!-- Legalitas Pill Badge -->
            <div class="inline-flex flex-wrap items-center justify-center gap-2 rounded-full border border-[#1B4D3E]/20 bg-white/90 px-4 py-1.5 text-xs font-semibold tracking-wider text-[#1B4D3E] uppercase mb-8 shadow-sm backdrop-blur-md">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#1B4D3E]"></span>
                </span>
                <span>NIB: 1911210009629 &bull; NPWP: 43.688.232.8-602.000 &bull; PMDN &bull; OSS-RBA BKPM RI</span>
            </div>

            <!-- Headline Utama -->
            <h1 class="mx-auto max-w-4xl text-4xl font-black tracking-tight text-[#0B0F19] sm:text-5xl lg:text-6xl leading-[1.18]">
                Pionir Sirkular Ekonomi &amp; Energi Hijau <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1B4D3E] via-[#246854] to-[#1B4D3E]">
                    PT Pinastika Bhakti Semesta
                </span>
            </h1>

            <!-- Subtitle Penjelasan -->
            <p class="mx-auto mt-6 max-w-3xl text-base sm:text-lg leading-relaxed text-slate-700">
                Penyedia pasokan bahan bakar alternatif terbarukan <strong class="text-[#1B4D3E] font-bold">Refuse Derived Fuel (RDF)</strong> untuk <strong class="text-[#0B0F19] font-bold">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>, pengolahan daur ulang plastik &amp; kompos organik, rekayasa permesinan mekanikal mandiri, serta tata kelola ERP enterprise berstandar SAK &amp; DJP.
            </p>

            <!-- Tombol Call-to-Action (CTA Kontras Tinggi: Amber Gold & Hijau Botol) -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2.5 rounded-full bg-[#F59E0B] hover:bg-[#d97706] text-white px-8 py-4 text-base font-bold shadow-xl shadow-amber-500/25 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-gauge-high"></i>
                        <span>Buka Dashboard BOD</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 rounded-full bg-[#F59E0B] hover:bg-[#d97706] text-white px-8 py-4 text-base font-bold shadow-xl shadow-amber-500/25 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-lock"></i>
                        <span>Masuk ke Portal Internal</span>
                    </a>
                @endauth

                <a href="#engineering" class="inline-flex items-center gap-2.5 rounded-full border-2 border-[#1B4D3E] bg-[#1B4D3E] text-white hover:bg-[#13392e] hover:border-[#13392e] px-8 py-4 text-base font-bold shadow-lg shadow-emerald-900/15 hover:-translate-y-0.5 transition duration-200">
                    <i class="fas fa-gears"></i>
                    <span>Portofolio Solusi Rekayasa &amp; RDF</span>
                </a>

                <a href="#contact" class="inline-flex items-center gap-2 rounded-full border-2 border-slate-300 bg-white text-slate-700 hover:text-[#1B4D3E] hover:border-[#1B4D3E] px-7 py-3.5 text-base font-semibold shadow-sm hover:-translate-y-0.5 transition duration-200">
                    <i class="fas fa-phone-volume text-[#1B4D3E]"></i>
                    <span>Hubungi Kami</span>
                </a>
            </div>

            <!-- 3 Key Highlights Badges -->
            <div class="mt-14 grid grid-cols-1 sm:grid-cols-3 gap-5 max-w-4xl mx-auto pt-8 border-t border-slate-200/80">
                <div class="flex items-center justify-center sm:justify-start gap-3.5 p-3 rounded-2xl bg-white/80 border border-slate-200 shadow-sm backdrop-blur-sm">
                    <div class="h-10 w-10 rounded-xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="fas fa-industry"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-black text-[#0B0F19]">Mitra Resmi SIG Tuban</div>
                        <div class="text-[11px] text-slate-500">Pasokan Bahan Bakar RDF</div>
                    </div>
                </div>

                <div class="flex items-center justify-center sm:justify-start gap-3.5 p-3 rounded-2xl bg-white/80 border border-slate-200 shadow-sm backdrop-blur-sm">
                    <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-black text-[#0B0F19]">Rekayasa Mesin Presisi</div>
                        <div class="text-[11px] text-slate-500">KBLI 28199 &amp; 28299 Mandiri</div>
                    </div>
                </div>

                <div class="flex items-center justify-center sm:justify-start gap-3.5 p-3 rounded-2xl bg-white/80 border border-slate-200 shadow-sm backdrop-blur-sm">
                    <div class="h-10 w-10 rounded-xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-black text-[#0B0F19]">Kepatuhan SAK &amp; DJP</div>
                        <div class="text-[11px] text-slate-500">Faktur WAPU BUMN 030</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── ABOUT SECTION: Legalitas NIB, KBLI & Visi Misi (Latar Netral Bersih #F8FAFC) ─── -->
    <section id="about" class="py-20 bg-slate-50 border-b border-slate-200/80 scroll-mt-12">
        <div class="mx-auto max-w-[1240px] px-6">
            <div class="text-center mb-14">
                <span class="text-xs font-bold tracking-widest text-[#1B4D3E] uppercase bg-[#1B4D3E]/10 border border-[#1B4D3E]/20 px-3.5 py-1 rounded-full">
                    PROFIL LEGALITAS &amp; PORTOFOLIO BISNIS
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#0B0F19] mt-3">Tentang PT Pinastika Bhakti Semesta</h2>
                <p class="mx-auto mt-2 max-w-2xl text-xs sm:text-sm text-slate-600">Legalitas resmi Badan Koordinasi Penanaman Modal (BKPM) &bull; Perizinan Berusaha Berbasis Risiko OSS Republik Indonesia</p>
                <div class="mx-auto mt-4 h-1 w-20 rounded bg-[#1B4D3E]"></div>
            </div>

            <!-- Corporate Identity Box from NIB (Kartu Putih #FFFFFF, Border #E2E8F0) -->
            <div class="mb-12 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-xs">
                    <div class="space-y-1 border-b md:border-b-0 md:border-r border-slate-200 pb-4 md:pb-0 pr-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Badan Hukum &amp; Nama Usaha:</span>
                        <h4 class="text-sm font-black text-[#0B0F19]">PT PINASTIKA BHAKTI SEMESTA</h4>
                        <span class="text-[11px] text-[#1B4D3E] font-semibold block">Status Modal: PMDN (Penanaman Modal Dalam Negeri)</span>
                    </div>

                    <div class="space-y-1 border-b md:border-b-0 lg:border-r border-slate-200 pb-4 md:pb-0 pr-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nomor Induk Berusaha (NIB):</span>
                        <div class="text-sm font-black font-mono text-[#1B4D3E] tracking-wider">1911210009629</div>
                        <span class="text-[11px] text-slate-500 block">Diterbitkan: 19 November 2021 (BKPM RI)</span>
                    </div>

                    <div class="space-y-1 border-b md:border-b-0 md:border-r border-slate-200 pb-4 md:pb-0 pr-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nomor Pokok Wajib Pajak (NPWP):</span>
                        <div class="text-sm font-black font-mono text-emerald-700 tracking-wider">43.688.232.8-602.000</div>
                        <span class="text-[11px] text-slate-500 block">KPP Pratama Mojokerto &bull; WAPU BUMN 030</span>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Alamat Kantor &amp; Workshop:</span>
                        <div class="text-xs font-bold text-[#0B0F19] leading-snug">JL. BYPASS NO 8 KEDUNGSARI, GUNUNGGEDANGAN, MAGERSARI</div>
                        <span class="text-[11px] text-slate-600 block">Kota Mojokerto, Jawa Timur (61315)</span>
                    </div>
                </div>
            </div>

            <!-- 2-Column: About Description & Visi Misi -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">
                <!-- Left: Corporate Summary (Kartu Putih #FFFFFF) -->
                <div class="space-y-5 lg:col-span-5">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-[#0B0F19] flex items-center gap-2">
                            <i class="fas fa-building-circle-check text-[#1B4D3E]"></i>
                            <span>Penggerak Rantai Pasok Hijau Nasional</span>
                        </h3>
                        <p class="text-xs sm:text-sm leading-relaxed text-slate-600 text-justify">
                            <strong class="text-[#0B0F19] font-bold">PT Pinastika Bhakti Semesta (PBS)</strong> adalah perusahaan sirkular ekonomi berbadan hukum resmi yang beroperasi di Mojokerto, Jawa Timur. PBS memadukan teknologi pengolahan sampah anorganik &amp; organik, pemilahan dan pencucian plastik (*Cuci Giling / CUGIL*), rekayasa permesinan mekanikal mandiri, serta produksi bahan bakar alternatif ramah lingkungan <strong class="text-[#1B4D3E] font-bold">Refuse Derived Fuel (RDF)</strong>.
                        </p>
                        <p class="text-xs leading-relaxed text-slate-600 text-justify">
                            Dalam rangka mendukung program Dekarbonisasi Nasional dan transisi energi bersih, PT PBS menjadi mitra penyedia pasokan RDF ke <strong class="text-[#0B0F19] font-bold">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>, sekaligus menyerap dan mengolah sampah dari berbagai TPST mitra daerah se-Jawa Timur.
                        </p>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                                <div class="text-xl font-black text-[#1B4D3E]">4 KBLI</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Bidang Usaha OSS Resmi</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                                <div class="text-xl font-black text-emerald-700">SIG Tuban</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Supply RDF Partner</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visi & Misi Perusahaan (Kartu Putih Bergaris Aksen Hijau Botol) -->
                <div class="space-y-6 rounded-3xl border-2 border-[#1B4D3E]/30 bg-white p-6 sm:p-8 shadow-sm lg:col-span-7">
                    <!-- VISI -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-[#1B4D3E] font-black text-sm uppercase tracking-wider">
                            <div class="h-8 w-8 rounded-xl bg-[#1B4D3E]/10 border border-[#1B4D3E]/20 flex items-center justify-center text-[#1B4D3E]">
                                <i class="fas fa-eye text-xs"></i>
                            </div>
                            <span>Visi Korporasi</span>
                        </div>
                        <blockquote class="border-l-4 border-[#1B4D3E] pl-4 py-2 text-sm sm:text-base font-semibold text-[#0B0F19] italic leading-relaxed bg-[#1B4D3E]/5 rounded-r-xl">
                            "Menjadi pelopor terdepan industri sirkular ekonomi dan energi hijau terbarukan (Waste-to-Energy) di Indonesia, yang mengintegrasikan pengolahan sampah presisi, rekayasa permesinan mandiri, dan tata kelola korporasi akuntabel demi keberlanjutan lingkungan dan kemandirian industri nasional."
                        </blockquote>
                    </div>

                    <!-- MISI (5 PILAR STRATEGIS) -->
                    <div class="border-t border-slate-200 pt-5 space-y-3">
                        <div class="flex items-center gap-2 text-[#1B4D3E] font-black text-sm uppercase tracking-wider mb-2">
                            <div class="h-8 w-8 rounded-xl bg-[#1B4D3E]/10 border border-[#1B4D3E]/20 flex items-center justify-center text-[#1B4D3E]">
                                <i class="fas fa-bullseye text-xs"></i>
                            </div>
                            <span>Misi Strategis Korporasi</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
                                <span class="h-6 w-6 rounded-lg bg-[#1B4D3E] text-white font-bold flex items-center justify-center shrink-0 text-[11px]">1</span>
                                <div class="text-slate-700">
                                    <strong class="text-[#0B0F19] block font-bold mb-0.5">Akselerasi Transisi Energi Bersih (Waste-to-Energy):</strong>
                                    Memproduksi pasokan bahan bakar alternatif <em>Refuse Derived Fuel (RDF)</em> berkualitas tinggi berkalori standar industri semen (PT Semen Indonesia Group) sebagai substitusi batubara ramah lingkungan.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
                                <span class="h-6 w-6 rounded-lg bg-[#1B4D3E] text-white font-bold flex items-center justify-center shrink-0 text-[11px]">2</span>
                                <div class="text-slate-700">
                                    <strong class="text-[#0B0F19] block font-bold mb-0.5">Ekosistem Sirkular Ekonomi Berkelanjutan:</strong>
                                    Membangun rantai pasok terintegrasi dari hulu ke hilir bersama TPST mitra daerah se-Jawa Timur guna meminimalisir residu ke TPA dan memberdayakan ekonomi sirkular masyarakat.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
                                <span class="h-6 w-6 rounded-lg bg-[#1B4D3E] text-white font-bold flex items-center justify-center shrink-0 text-[11px]">3</span>
                                <div class="text-slate-700">
                                    <strong class="text-[#0B0F19] block font-bold mb-0.5">Inovasi Rekayasa Permesinan &amp; Manufaktur Mandiri:</strong>
                                    Mengembangkan riset desain, manufaktur mesin pencacah (<em>shredder/crusher</em>), pemilah, dan instalasi mekanikal berteknologi tepat guna secara mandiri.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
                                <span class="h-6 w-6 rounded-lg bg-[#1B4D3E] text-white font-bold flex items-center justify-center shrink-0 text-[11px]">4</span>
                                <div class="text-slate-700">
                                    <strong class="text-[#0B0F19] block font-bold mb-0.5">Integritas Tata Kelola Keuangan &amp; Kepatuhan Pajak (SAK &amp; DJP):</strong>
                                    Menerapkan standar akuntansi berintegritas tinggi, pengawasan anggaran ketat, serta kepatuhan pemungutan PPN WAPU BUMN 030 dan PPh secara tertib.
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
                                <span class="h-6 w-6 rounded-lg bg-[#1B4D3E] text-white font-bold flex items-center justify-center shrink-0 text-[11px]">5</span>
                                <div class="text-slate-700">
                                    <strong class="text-[#0B0F19] block font-bold mb-0.5">Kemitraan Industri Terpercaya &amp; Budaya K3:</strong>
                                    Menjaga komitmen mutu spesifikasi pasokan, ketepatan jadwal pengiriman tonase harian, keselamatan kerja (K3), serta kepatuhan lingkungan hidup.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── REKAYASA MESIN, KBLI & PLATFORM TEKNOLOGI (SLATE DARK #1E293B / #0F172A) ─── -->
    <!-- Kontras tegas: Membuktikan kemampuan rekayasa mekanikal presisi & integrasi teknologi tinggi -->
    <section id="engineering" class="py-24 bg-gradient-to-b from-[#0F172A] via-[#1E293B] to-[#0F172A] text-slate-200 relative overflow-hidden border-y border-slate-800 scroll-mt-12">
        <!-- Background Asset Watermark Platform RDF -->
        <div class="absolute inset-0 pointer-events-none opacity-10 mix-blend-screen">
            <img src="{{ asset('images/facility-rdf-platform.jpg') }}" alt="Mesin Mekanikal" class="w-full h-full object-cover">
        </div>

        <div class="relative z-10 mx-auto max-w-[1240px] px-6">
            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest text-[#F59E0B] uppercase bg-amber-500/10 border border-amber-500/30 px-3.5 py-1 rounded-full">
                    DIVISI REKAYASA MEKANIKAL &amp; TEKNOLOGI PROSES
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white mt-3">
                    Manufaktur Mesin Presisi &amp; Pabrikasi RDF
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-xs sm:text-sm text-slate-400">
                    Membuktikan PT Pinastika Bhakti Semesta memiliki keunggulan rekayasa permesinan independen, bukan sekadar penampung sampah konvensional.
                </p>
                <div class="mx-auto mt-4 h-1 w-20 rounded bg-[#F59E0B]"></div>
            </div>

            <!-- Platform Preview Card with 3D Image -->
            <div class="mb-14 rounded-3xl border border-slate-700 bg-slate-900/80 overflow-hidden shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 items-center">
                    <div class="lg:col-span-7 relative h-72 lg:h-[420px] overflow-hidden bg-slate-950">
                        <img 
                            src="{{ asset('images/facility-rdf-platform.jpg') }}" 
                            alt="Platform Pengolahan Sampah Berteknologi" 
                            class="w-full h-full object-cover object-center transform hover:scale-105 transition duration-500"
                        >
                        <div class="absolute bottom-4 left-4 bg-slate-950/80 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-slate-700 text-xs text-amber-400 font-mono flex items-center gap-2">
                            <i class="fas fa-layer-group text-emerald-400"></i>
                            <span>Desain Platform Rantai Pemilah &amp; Pencacah RDF Mandiri</span>
                        </div>
                    </div>
                    <div class="lg:col-span-5 p-8 lg:p-10 space-y-5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                            High-Tech Precision Engineering
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black text-white leading-snug">
                            Platform Terpadu Pengolahan Sampah Menjadi Bahan Bakar RDF
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed text-justify">
                            Didukung perancangan mekanikal mandiri untuk menghadirkan alur terintegrasi: mulai dari <em>Trommel Screen</em> pemilah fraksi, konveyor bertingkat, mesin pencacah (<em>Dual-Shaft Shredder</em>), hingga sistem pengering dan pengemasan ke kontainer pasokan pabrik semen.
                        </p>
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 text-center">
                                <span class="text-lg font-black text-amber-400 block">&gt; 3.000</span>
                                <span class="text-[10px] text-slate-400">Kkal/Kg Standar RDF</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 text-center">
                                <span class="text-lg font-black text-emerald-400 block">Zero Waste</span>
                                <span class="text-[10px] text-slate-400">TPA Residual Reduction</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 KBLI Cards (High-Contrast Slate Dark Design) -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-certificate text-amber-400"></i>
                        <span>Portofolio 4 Klasifikasi Baku Lapangan Usaha (KBLI OSS BKPM)</span>
                    </h3>
                    <span class="text-xs text-amber-400 font-mono">NIB: 1911210009629</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- KBLI 28199 -->
                    <div class="rounded-2xl border border-slate-700 bg-slate-900/90 p-5 space-y-3 hover:border-amber-400 hover:-translate-y-1 transition duration-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-400 text-xs font-black font-mono border border-amber-500/30">
                                KBLI 28199
                            </span>
                            <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                Engineering
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white leading-snug">Industri Mesin Untuk Keperluan Umum Lainnya Ytdl</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Perakitan konveyor, elevator bahan, saringan putar (<em>trommel screen</em>), blower, dan mesin penunjang umum pabrik.
                        </p>
                    </div>

                    <!-- KBLI 28299 -->
                    <div class="rounded-2xl border border-slate-700 bg-slate-900/90 p-5 space-y-3 hover:border-amber-400 hover:-translate-y-1 transition duration-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-400 text-xs font-black font-mono border border-amber-500/30">
                                KBLI 28299
                            </span>
                            <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                Mesin Khusus RDF
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white leading-snug">Industri Mesin Keperluan Khusus Lainnya</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Manufaktur mesin shredder RDF, crusher plastik, mesin press hidrolik, dan mesin pencuci sentrifugal sampah daur ulang.
                        </p>
                    </div>

                    <!-- KBLI 38212 -->
                    <div class="rounded-2xl border border-slate-700 bg-slate-900/90 p-5 space-y-3 hover:border-emerald-400 hover:-translate-y-1 transition duration-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 text-xs font-black font-mono border border-emerald-500/30">
                                KBLI 38212
                            </span>
                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">
                                Organik &amp; Kompos
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white leading-snug">Produksi Kompos Sampah Organik</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Pengolahan sampah organik dan biomassa dari pasar dan TPST mitra menjadi pupuk kompos berkualitas dan media tanam.
                        </p>
                    </div>

                    <!-- KBLI 38302 -->
                    <div class="rounded-2xl border-2 border-emerald-500/60 bg-slate-900/90 p-5 space-y-3 hover:border-emerald-400 hover:-translate-y-1 transition duration-200">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500 text-slate-950 text-xs font-black font-mono">
                                KBLI 38302
                            </span>
                            <span class="text-[9px] font-bold text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-500/30">
                                Sektor Utama RDF
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-white leading-snug">Pemulihan Material Barang Bukan Logam</h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Pemilahan, pencacahan, pencucian, dan pengolahan sampah anorganik/plastik menjadi bahan baku daur ulang dan <strong>Refuse Derived Fuel (RDF)</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── STRUKTUR ORGANISASI & DEWAN DIREKSI (Latar Putih & Soft Neutral #F8FAFC) ─── -->
    <section id="profil" class="py-20 bg-slate-50 border-b border-slate-200/80 scroll-mt-12">
        <span id="profile" class="sr-only"></span>

        <div class="mx-auto max-w-[1240px] px-6">
            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest text-[#1B4D3E] uppercase bg-[#1B4D3E]/10 border border-[#1B4D3E]/20 px-3.5 py-1 rounded-full">
                    TATA KELOLA KORPORASI &amp; STRUKTUR ORGANISASI
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#0B0F19] mt-3">
                    Struktur Organisasi &amp; Dewan Direksi PT PBS
                </h2>
                <p class="mx-auto mt-3 max-w-3xl text-sm sm:text-base text-slate-600 leading-relaxed">
                    Sinergi strategis pengolahan sampah daur ulang menjadi <strong class="text-[#1B4D3E]">Refuse Derived Fuel (RDF)</strong>, tata kelola keuangan presisi, jejaring TPST mitra se-Jawa Timur, dan kemitraan pasokan ke <strong class="text-[#0B0F19]">PT Semen Indonesia (Persero) Tbk Pabrik Tuban</strong>.
                </p>
                <div class="mx-auto mt-4 h-1 w-20 rounded bg-[#1B4D3E]"></div>
            </div>

            <!-- BAGAN HIERARKI KORPORASI (Putih Bersih, Border #E2E8F0) -->
            <div id="struktur" class="mb-16 rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 pb-5 mb-8">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#1B4D3E]">Hierarki Pengurus &amp; Rantai Pasok Industri</span>
                        <h3 class="text-lg font-black text-[#0B0F19] mt-0.5 flex items-center gap-2">
                            <i class="fas fa-sitemap text-[#1B4D3E]"></i>
                            <span>Bagan Struktur Organisasi &amp; Komando Tata Kelola</span>
                        </h3>
                    </div>
                    <span class="hidden sm:inline-flex text-[11px] bg-emerald-50 text-[#1B4D3E] border border-[#1B4D3E]/30 px-3 py-1 rounded-full font-bold items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> Sesuai Akta Korporasi &amp; KBLI
                    </span>
                </div>

                <div class="space-y-8">
                    <!-- LEVEL 1: RUPS & DEWAN KOMISARIS -->
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="w-full max-w-md rounded-2xl border-2 border-[#1B4D3E]/40 bg-slate-50 p-4 text-center shadow-sm">
                                <span class="text-[9px] font-black uppercase tracking-widest text-[#1B4D3E] bg-[#1B4D3E]/10 px-2 py-0.5 rounded border border-[#1B4D3E]/20">
                                    Otoritas Kebijakan Tertinggi
                                </span>
                                <h4 class="text-sm font-black text-[#0B0F19] mt-1.5 uppercase">Rapat Umum Pemegang Saham (RUPS) &amp; Dewan Komisaris</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pengawasan Strategis, Pengesahan Rencana Bisnis &amp; Tata Kelola Korporasi</p>
                            </div>
                        </div>

                        <!-- 2 KOMISARIS CARDS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                            <!-- KOMISARIS UTAMA -->
                            <div class="rounded-2xl border-2 border-[#1B4D3E]/40 bg-white p-4 text-center shadow-sm hover:border-[#1B4D3E] transition">
                                <div class="h-10 w-10 rounded-xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center mx-auto mb-2.5 font-bold text-base">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#1B4D3E]">Komisaris Utama</span>
                                <h4 class="text-sm font-black text-[#0B0F19] mt-0.5">Safira Putri Imanina</h4>
                                <p class="text-xs text-slate-500 font-medium">Komisaris Utama</p>
                            </div>

                            <!-- KOMISARIS -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm hover:border-[#1B4D3E] transition">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center mx-auto mb-2.5 font-bold text-base">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Komisaris</span>
                                <h4 class="text-sm font-black text-[#0B0F19] mt-0.5">Handoko</h4>
                                <p class="text-xs text-slate-500 font-medium">Komisaris</p>
                            </div>
                        </div>
                    </div>

                    <!-- CONNECTOR LINE 1 -->
                    <div class="flex justify-center -my-2">
                        <div class="w-0.5 h-8 bg-slate-300"></div>
                    </div>

                    <!-- LEVEL 2: DEWAN DIREKSI (BOD) -->
                    <div class="space-y-4">
                        <div class="text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#0B0F19] bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                                Dewan Direksi (Board of Directors - BOD)
                            </span>
                        </div>

                        <!-- 4 BOD CARDS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- DIREKTUR UTAMA -->
                            <div class="rounded-2xl border-2 border-[#1B4D3E]/60 bg-white p-5 text-center shadow-sm hover:shadow-md hover:border-[#1B4D3E] transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-[#1B4D3E] text-white flex items-center justify-center mx-auto mb-3 font-bold text-lg shadow-sm">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-[#1B4D3E]">President Director</span>
                                    <h4 class="text-sm font-black text-[#0B0F19] mt-1">Sendy Hartono</h4>
                                    <p class="text-xs text-[#1B4D3E] font-semibold">Direktur Utama</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 leading-snug">
                                    Penanggung jawab kebijakan umum, kepatuhan legal, dan kepemimpinan korporasi PT PBS.
                                </div>
                            </div>

                            <!-- DIREKTUR AKUNTANSI, PAJAK & UMUM -->
                            <div class="rounded-2xl border-2 border-amber-500/60 bg-white p-5 text-center shadow-sm hover:shadow-md hover:border-amber-500 transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-amber-500 text-white flex items-center justify-center mx-auto mb-3 font-black text-lg shadow-sm">
                                        <i class="fas fa-scale-balanced"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600">Director</span>
                                    <h4 class="text-sm font-black text-[#0B0F19] mt-1 leading-snug">Kurniawan, S.E., Ak., CA., M. Ak.</h4>
                                    <p class="text-xs text-amber-600 font-semibold">Direktur - Akuntansi, Pajak &amp; Umum</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-600 leading-snug">
                                    Pengelolaan akuntansi, keuangan, perpajakan DJP, audit SAK, dan administrasi umum korporasi.
                                </div>
                            </div>

                            <!-- DIREKTUR TEKNIK & OPERASIONAL -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md hover:border-[#1B4D3E] transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center mx-auto mb-3 font-bold text-lg">
                                        <i class="fas fa-gears"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Director</span>
                                    <h4 class="text-sm font-black text-[#0B0F19] mt-1">Yudo Ariyanto</h4>
                                    <p class="text-xs text-slate-600 font-medium">Direktur - Teknik &amp; Operasional</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 leading-snug">
                                    Rekayasa teknis permesinan daur ulang, pemeliharaan fasilitas pabrik, dan keandalan operasional.
                                </div>
                            </div>

                            <!-- DIREKTUR OPERASIONAL -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm hover:shadow-md hover:border-[#1B4D3E] transition flex flex-col justify-between">
                                <div>
                                    <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center mx-auto mb-3 font-bold text-lg">
                                        <i class="fas fa-industry"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Director</span>
                                    <h4 class="text-sm font-black text-[#0B0F19] mt-1">Winardi Sugianto, SE</h4>
                                    <p class="text-xs text-slate-600 font-medium">Direktur - Operasional</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 leading-snug">
                                    Manajemen rantai pasok pengolahan sampah RDF, logistik pengiriman, dan operasional lapangan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONNECTOR LINE 2 -->
                    <div class="flex justify-center -my-3">
                        <div class="w-0.5 h-8 bg-slate-300"></div>
                    </div>

                    <!-- LEVEL 3: 4 DIVISI PELAKSANA RANTAI PASOK -->
                    <div class="space-y-4">
                        <div class="text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                                Divisi &amp; Unit Pelaksana Rantai Pasok (Supply Chain Ecosystem)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Divisi 1 -->
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:border-[#1B4D3E] transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-[#1B4D3E]">
                                    <i class="fas fa-truck-ramp-box text-base"></i>
                                    <h5 class="text-xs font-bold text-[#0B0F19] uppercase">Divisi Sourcing &amp; Mitra TPST</h5>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Pengambilan sampah terpilah dari TPST Megilan Lamongan, TPST Tambakrejo Sidoarjo, Sedati, &amp; Mojokerto.
                                </p>
                            </div>

                            <!-- Divisi 2 -->
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:border-[#1B4D3E] transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-[#1B4D3E]">
                                    <i class="fas fa-weight-scale text-base"></i>
                                    <h5 class="text-xs font-bold text-[#0B0F19] uppercase">QC &amp; Jembatan Timbang</h5>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Inspeksi kadar air, kalori RDF (Kkal/Kg), toleransi susut, dan verifikasi timbangan asal vs tujuan.
                                </p>
                            </div>

                            <!-- Divisi 3 -->
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:border-[#1B4D3E] transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-[#1B4D3E]">
                                    <i class="fas fa-truck-front text-base"></i>
                                    <h5 class="text-xs font-bold text-[#0B0F19] uppercase">Logistik &amp; Pasokan Tuban</h5>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Pengawalan armada ekspedisi truk/dump truck, penerbitan Surat Jalan (DO), &amp; BAST Pabrik Tuban.
                                </p>
                            </div>

                            <!-- Divisi 4 -->
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:border-[#1B4D3E] transition">
                                <div class="flex items-center gap-2.5 mb-2.5 text-[#1B4D3E]">
                                    <i class="fas fa-file-invoice-dollar text-base"></i>
                                    <h5 class="text-xs font-bold text-[#0B0F19] uppercase">Akuntansi &amp; Pajak (DJP)</h5>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Penerbitan Invoice komersial, Faktur Pajak WAPU BUMN (030), &amp; pembukuan SAK di Bank Mandiri.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL PROFIL EKSEKUTIF: BOD FINANCE & TAX (KURNIAWAN) -->
            <div class="rounded-3xl border-2 border-amber-500/30 bg-white p-8 sm:p-12 shadow-md">
                <div class="flex flex-col items-center gap-12 lg:flex-row lg:items-center">
                    <!-- Profile Picture -->
                    <div class="relative w-full max-w-[300px] shrink-0">
                        <div class="relative z-10 overflow-hidden rounded-3xl border border-slate-200 shadow-xl bg-slate-100">
                            <img 
                                src="{{ asset('images/ayahrompi.png') }}" 
                                alt="Kurniawan - Board of Director (Finance & Tax)"
                                class="h-[360px] w-full object-cover object-top transition duration-300 hover:scale-105"
                                onerror="this.style.display='none'"
                            >
                        </div>
                        <div class="absolute -top-4 -left-4 h-32 w-32 rounded-full bg-amber-500/15 blur-2xl -z-0"></div>
                        <div class="absolute -bottom-4 -right-4 h-36 w-36 rounded-full bg-emerald-600/15 blur-2xl -z-0"></div>
                    </div>

                    <!-- Profile Details -->
                    <div class="flex-1 space-y-5 text-center lg:text-left">
                        <span class="text-xs font-bold tracking-widest text-amber-700 uppercase bg-amber-50 border border-amber-200 px-3.5 py-1 rounded-full inline-block">
                            Penanggung Jawab Keuangan &amp; Kepatuhan Pajak (DJP)
                        </span>
                        <h3 class="text-2xl font-black text-[#0B0F19] sm:text-3xl leading-snug">
                            Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.
                        </h3>
                        <p class="text-xs text-[#1B4D3E] font-bold uppercase tracking-wider">
                            Direktur - Akuntansi, Pajak &amp; Umum &bull; PT Pinastika Bhakti Semesta
                        </p>

                        <!-- Badges Sertifikasi & Profesi -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2">
                            @foreach(['Chartered Accountant (CA)', 'Magister Akuntansi (M.Ak)', 'CMA', 'CIBA', 'CIAP', 'Ak.'] as $badge)
                                <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-bold text-[#0B0F19]">
                                    {{ $badge }}
                                </span>
                            @endforeach
                        </div>

                        <blockquote class="border-l-0 lg:border-l-4 border-amber-500 pl-0 lg:pl-4 italic text-slate-700 text-sm sm:text-base leading-relaxed py-1 bg-amber-50/50 rounded-r-xl">
                            "Sebagai Direktur yang membidangi Akuntansi, Pajak &amp; Umum pada PT Pinastika Bhakti Semesta, kami menjamin seluruh tata kelola transaksi pasokan RDF dan komoditas industri terkelola secara akuntabel, mematuhi standar SAK, memenuhi mekanisme pemungutan PPN WAPU BUMN 030, serta siap diaudit secara terbuka."
                        </blockquote>

                        <!-- Qualifications Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-left">
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                                <i class="fas fa-award text-amber-600 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-[#0B0F19]">Chartered Accountant (CA)</h4>
                                    <p class="text-[11px] text-slate-500">Ikatan Akuntan Indonesia</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                                <i class="fas fa-graduation-cap text-amber-600 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-[#0B0F19]">Magister Akuntansi (M.Ak)</h4>
                                    <p class="text-[11px] text-slate-500">Univ. Trunojoyo Madura</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                                <i class="fas fa-chart-pie text-amber-600 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-[#0B0F19]">Certified Mgt. Accountant (CMA)</h4>
                                    <p class="text-[11px] text-slate-500">Universitas Airlangga</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                                <i class="fas fa-globe text-amber-600 text-xl"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-[#0B0F19]">Intl. Business Analysis (CIBA)</h4>
                                    <p class="text-[11px] text-slate-500">Univ. Kristen Petra</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Track Record Grid -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600">
                            <i class="fas fa-landmark text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-[#0B0F19] mb-1.5">Sektor Publik &amp; BUMDesa Jatim</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tenaga Ahli Klinik BUMDesa Provinsi Jawa Timur &amp; Tim Penilai BUMDesa Berprestasi Tingkat Provinsi (2015-2024).
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600">
                            <i class="fas fa-briefcase text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-[#0B0F19] mb-1.5">Sektor Korporasi Multinasional</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Manajer Keuangan, Pengendalian Anggaran, Audit Internal &amp; CSR (Danone, USAID, Medco).
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                        <div class="mx-auto mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        <h4 class="text-sm font-bold text-[#0B0F19] mb-1.5">Akademisi &amp; Mentor Bisnis</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dosen Tetap Akuntansi, Mentor Inkubasi Bisnis, dan Narasumber Ahli Keuangan &amp; Pajak.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CAKUPAN MODUL ERP, KEPATUHAN PAJAK & AUDIT SAK (Latar Kartu Putih #FFFFFF, Border #E2E8F0) ─── -->
    <section id="modules" class="py-20 bg-white border-b border-slate-200/80 scroll-mt-12">
        <div class="mx-auto max-w-[1240px] px-6">
            <div class="text-center mb-14">
                <span class="text-xs font-bold tracking-widest text-[#1B4D3E] uppercase bg-[#1B4D3E]/10 border border-[#1B4D3E]/20 px-3.5 py-1 rounded-full">
                    ARSITEKTUR MODUL TERTUTUP &amp; KEPATUHAN PAJAK
                </span>
                <h2 class="text-3xl font-black text-[#0B0F19] mt-3">Integrasi Modul PBS-ERP &amp; Kepatuhan SAK</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-xl mx-auto">Tampilan tabel &amp; pelaporan akuntabel berstandar korporat BUMN dan audit DJP</p>
                <div class="mx-auto mt-4 h-1 w-16 rounded bg-[#1B4D3E]"></div>
            </div>

            <!-- Kartu Putih #FFFFFF, Garis Batas #E2E8F0, Ikon Hijau Tua & Slate -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Modul 1 -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-[#1B4D3E] transition duration-200">
                    <div class="h-12 w-12 rounded-2xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-book-journal-whills"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#0B0F19] mb-2">Akuntansi &amp; Jurnal SAK</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Chart of Accounts lengkap, Jurnal Umum double-entry, Buku Besar, Neraca Saldo, dan Laporan Keuangan otomatis sesuai SAK.
                    </p>
                </div>

                <!-- Modul 2 -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-[#1B4D3E] transition duration-200">
                    <div class="h-12 w-12 rounded-2xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#0B0F19] mb-2">Manajemen Pajak DJP</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Monitoring PPN Keluaran/Masukan, Faktur Pajak WAPU BUMN 030, Pemotongan PPh 21, 23, 4(2), tracking NTPN dan SPT Masa.
                    </p>
                </div>

                <!-- Modul 3 -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-amber-500 transition duration-200">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#0B0F19] mb-2">Approval Anggaran BOD</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pengajuan dana divisi, verifikasi pagu anggaran, serta approval berjenjang langsung oleh BOD Finance (Kurniawan).
                    </p>
                </div>

                <!-- Modul 4 -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-slate-400 transition duration-200">
                    <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center mb-4 text-xl">
                        <i class="fas fa-diagram-project"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#0B0F19] mb-2">Proyek &amp; Billing Klien</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Manajemen kontrak pasokan tonase RDF, surat jalan (DO), berita acara serah terima (BAST), dan invoice termin komersial.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── KONTAK & KANTOR PUSAT OPERASIONAL (Latar Netral Bersih #F8FAFC) ─── -->
    <section id="contact" class="py-20 bg-slate-50 border-b border-slate-200/80 scroll-mt-12">
        <div class="mx-auto max-w-[1240px] px-6">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-black text-[#0B0F19]">Kontak &amp; Kantor Pusat</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">PT Pinastika Bhakti Semesta &bull; Kota Mojokerto, Jawa Timur</p>
                <div class="mx-auto mt-3 h-1 w-16 rounded bg-[#1B4D3E]"></div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-12">
                    <div class="p-8 sm:p-12 lg:col-span-6 flex flex-col justify-center space-y-7">
                        <div>
                            <h3 class="text-xl font-bold text-[#0B0F19]">Kantor Pusat Operasional</h3>
                            <p class="text-xs text-slate-500 mt-1">Hubungi kantor kami untuk urusan administratif, perpajakan, dan kemitraan pasokan industri.</p>
                        </div>

                        <div class="space-y-5 text-sm">
                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-location-dot"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#0B0F19] text-xs">Alamat Kantor Pusat</h4>
                                    <p class="text-xs text-slate-700 mt-0.5 font-medium leading-relaxed">
                                        {{ $perusahaan->alamat ?? 'Jln Raya ByPass no 08 Kedungsari Magersari Kota Mojokerto' }}
                                    </p>
                                    <span class="text-[10px] text-[#1B4D3E] font-mono block mt-0.5">Jawa Timur, Indonesia</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#0B0F19] text-xs">Telepon / WhatsApp Resmi</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        <a href="https://wa.me/6282131763686" target="_blank" class="text-emerald-700 hover:text-emerald-800 font-mono font-bold hover:underline flex items-center gap-1.5">
                                            <span>+62 821 3176 3686 (082131763686)</span>
                                            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-[#1B4D3E]/10 text-[#1B4D3E] flex items-center justify-center shrink-0">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#0B0F19] text-xs">Email Korespondensi</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        <a href="mailto:pt.pinastikabhaktisemesta@gmail.com" class="text-[#1B4D3E] hover:text-[#246854] font-mono font-semibold hover:underline">
                                            pt.pinastikabhaktisemesta@gmail.com
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Embed with Specific Coordinates -->
                    <div class="min-h-[360px] lg:col-span-6 relative border-t lg:border-t-0 lg:border-l border-slate-200 bg-slate-100 flex flex-col">
                        <iframe 
                            title="Peta Kantor PT Pinastika Bhakti Semesta - Mojokerto"
                            src="https://maps.google.com/maps?q=-7.4655082105554476,112.45988780996359&hl=id&z=17&output=embed" 
                            class="h-full w-full border-0 min-h-[300px]"
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                        <div class="p-3 bg-white border-t border-slate-200 text-center flex items-center justify-between px-4 text-[11px]">
                            <span class="text-slate-600 font-mono text-[10px]">
                                <i class="fas fa-location-crosshairs text-[#1B4D3E] mr-1"></i> -7.465508, 112.459888
                            </span>
                            <a href="https://maps.google.com/?q=-7.4655082105554476,112.45988780996359" target="_blank" class="text-[#1B4D3E] hover:underline font-bold flex items-center gap-1">
                                <span>Buka di Google Maps</span>
                                <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FOOTER (Dark Slate #0B0F19 dengan Badge "Powered by SimpleAkunting.id") ─── -->
    <footer class="py-14 bg-[#0B0F19] text-slate-400 text-center text-xs space-y-4 border-t border-slate-800">
        <!-- Eye-catching Powered by SimpleAkunting.id Badge -->
        <div class="flex items-center justify-center">
            <a 
                href="https://simpleakunting.id/" 
                target="_blank" 
                rel="noopener noreferrer"
                class="group relative inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-slate-900 border border-amber-500/40 hover:border-amber-400 shadow-lg shadow-orange-500/10 hover:shadow-orange-500/25 transition-all duration-300 transform hover:-translate-y-0.5"
            >
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <span class="text-xs text-slate-300 group-hover:text-white transition-colors">
                    Powered by <strong class="font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-orange-400 to-amber-300 group-hover:from-amber-300 group-hover:to-orange-200 text-sm tracking-wide">SimpleAkunting.id</strong>
                </span>
                <i class="fas fa-arrow-up-right-from-square text-[10px] text-amber-400/80 group-hover:text-amber-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all"></i>
            </a>
        </div>

        <p class="text-slate-400">
            &copy; {{ date('Y') }} <strong class="text-white">PT Pinastika Bhakti Semesta (PBS)</strong>. Hak Cipta Dilindungi Undang-Undang.
        </p>
        <p class="text-[11px] text-slate-500 max-w-xl mx-auto">
            Sistem tertutup (closed intranet). Dilindungi otentikasi berjenjang. Percobaan akses tidak sah akan dicatat ke dalam audit trail keamanan sistem.
        </p>
        <div class="pt-2">
            @auth
                <a href="{{ route('dashboard') }}" class="text-[#F59E0B] hover:underline font-bold">Buka Dashboard ERP</a>
            @else
                <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition font-medium">Login Administrator Sistem</a>
            @endauth
        </div>
    </footer>

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