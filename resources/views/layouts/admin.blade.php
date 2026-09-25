<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - PBS-ERP PT Pinastika Bhakti Semesta</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Tailwind CSS CDN & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff8c00',
                        'primary-hover': '#e07b00',
                        navy: {
                            800: '#101d33',
                            900: '#0a1628',
                            950: '#070f1e'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #070f1e;
            color: #e2e8f0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 140, 0, 0.2);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 140, 0, 0.5);
        }
        .sidebar-transition {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Collapsed Sidebar Rules */
        .sidebar-collapsed {
            width: 5rem !important; /* 80px */
        }
        .sidebar-collapsed .sidebar-text,
        .sidebar-collapsed .sidebar-chevron,
        .sidebar-collapsed .sidebar-category-title,
        .sidebar-collapsed .user-info-text {
            display: none !important;
        }
        .sidebar-collapsed .sidebar-header-branding {
            justify-content: center !important;
        }
        .sidebar-collapsed .sidebar-link-item {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .sidebar-collapsed .sidebar-user-card {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        .sidebar-collapsed .submenu-container {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[#070f1e] text-slate-200 antialiased flex flex-col md:flex-row relative overflow-x-hidden">

    <!-- Mobile Overlay Backdrop -->
    <div id="mobileBackdrop" onclick="toggleSidebarMobile()" class="fixed inset-0 bg-black/70 z-40 hidden md:hidden transition-opacity"></div>

    <!-- Sidebar Navigation -->
    <aside id="mainSidebar" class="w-64 bg-[#0a1628] border-r border-slate-800 flex flex-col shrink-0 md:min-h-screen fixed md:sticky top-0 h-screen z-50 sidebar-transition -translate-x-full md:translate-x-0">
        
        <!-- Logo & Branding Header -->
        <div class="h-16 px-4 border-b border-slate-800/80 flex items-center justify-between sidebar-header-branding">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="h-10 w-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-lg shadow-orange-500/10 shrink-0 border border-slate-700/60 overflow-hidden">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PBS" class="h-full w-full object-contain">
                </div>
                <div class="sidebar-text truncate">
                    <h1 class="text-base font-extrabold tracking-tight text-white leading-tight">PBS-ERP</h1>
                    <p class="text-[9px] text-amber-500 font-semibold tracking-wider uppercase truncate">PT Pinastika Bhakti Semesta</p>
                </div>
            </a>
            <button onclick="toggleSidebarDesktop()" class="text-slate-400 hover:text-amber-400 p-1.5 rounded-lg hover:bg-slate-800/80 hidden md:flex items-center justify-center sidebar-text transition" title="Ciutkan Sidebar">
                <i class="fas fa-angles-left text-sm"></i>
            </button>
            <button onclick="toggleSidebarMobile()" class="text-slate-400 hover:text-white p-1.5 md:hidden">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links (Accordion & Expandable Submenus) -->
        <nav class="p-3 space-y-1.5 flex-1 custom-scrollbar overflow-y-auto overflow-x-hidden">
            
            <!-- Dashboard (Single Item) -->
            <a href="{{ route('dashboard') }}" class="sidebar-link-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-[#ff8c00] text-white shadow-lg shadow-orange-500/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}" title="Executive Dashboard">
                <i class="fas fa-chart-pie w-5 text-center text-base"></i>
                <span class="sidebar-text">Executive Dashboard</span>
            </a>

            <!-- SECTION 1: Operasional Cuci Giling (CUGIL) -->
            @php $isCugilActive = request()->routeIs('cugil.*'); @endphp
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('cugilSubmenu', 'cugilChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isCugilActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-recycle w-5 text-center text-sm text-cyan-400 shrink-0"></i>
                        <span class="sidebar-text truncate">Cuci Giling (CUGIL)</span>
                    </div>
                    <i id="cugilChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isCugilActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="cugilSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isCugilActive ? '' : 'hidden' }}">
                    <a href="{{ route('cugil.po.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.po.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Purchase Order (PO)">
                        <i class="fas fa-file-invoice text-blue-400 w-4 text-center"></i>
                        <span class="sidebar-text">Purchase Order (PO)</span>
                    </a>
                    <a href="{{ route('cugil.raw.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.raw.*') ? 'bg-amber-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Terima Bahan Baku">
                        <i class="fas fa-boxes-stacked text-amber-400 w-4 text-center"></i>
                        <span class="sidebar-text">Terima Bahan Baku</span>
                    </a>
                    <a href="{{ route('cugil.sales.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.sales.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Penjualan CUGIL">
                        <i class="fas fa-hand-holding-dollar text-emerald-400 w-4 text-center"></i>
                        <span class="sidebar-text">Penjualan CUGIL</span>
                    </a>
                    <a href="{{ route('cugil.barang.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.barang.*') ? 'bg-purple-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Daftar Barang">
                        <i class="fas fa-boxes text-purple-400 w-4 text-center"></i>
                        <span class="sidebar-text">Daftar Barang &amp; Stok</span>
                    </a>
                    <a href="{{ route('cugil.supplier.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.supplier.*') ? 'bg-cyan-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Daftar Supplier">
                        <i class="fas fa-truck text-cyan-400 w-4 text-center"></i>
                        <span class="sidebar-text">Daftar Supplier</span>
                    </a>
                    <a href="{{ route('cugil.customer.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('cugil.customer.*') ? 'bg-green-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Daftar Kastamer">
                        <i class="fas fa-users text-green-400 w-4 text-center"></i>
                        <span class="sidebar-text">Daftar Kastamer</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 2: Keuangan & Akuntansi -->
            @php $isAkuntansiActive = request()->routeIs('akuntansi.*'); @endphp
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('akuntansiSubmenu', 'akuntansiChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isAkuntansiActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-sack-dollar w-5 text-center text-sm text-amber-400 shrink-0"></i>
                        <span class="sidebar-text truncate">Keuangan &amp; Akuntansi</span>
                    </div>
                    <i id="akuntansiChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isAkuntansiActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="akuntansiSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isAkuntansiActive ? '' : 'hidden' }}">
                    <a href="{{ route('akuntansi.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.index') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Chart of Accounts">
                        <i class="fas fa-book-bookmark w-4 text-center"></i>
                        <span class="sidebar-text">Chart of Accounts (COA)</span>
                    </a>
                    <a href="{{ route('akuntansi.jurnal-kas') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.jurnal-kas') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Jurnal Kas & Bank (BKM / BKK / Transfer)">
                        <i class="fas fa-money-bill-transfer w-4 text-center text-amber-400"></i>
                        <span class="sidebar-text">Jurnal Kas &amp; Bank</span>
                    </a>
                    <a href="{{ route('akuntansi.buku-kas') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.buku-kas') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Buku Kas & Bank / Mutasi Rekening">
                        <i class="fas fa-wallet w-4 text-center text-emerald-400"></i>
                        <span class="sidebar-text">Buku Kas &amp; Bank</span>
                    </a>
                    <a href="{{ route('akuntansi.buku-besar') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.buku-besar') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Buku Besar (General Ledger)">
                        <i class="fas fa-book-journal-whills w-4 text-center text-sky-400"></i>
                        <span class="sidebar-text">Buku Besar (GL)</span>
                    </a>
                    <a href="{{ route('akuntansi.jurnal') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.jurnal') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Jurnal Umum Memorial">
                        <i class="fas fa-receipt w-4 text-center"></i>
                        <span class="sidebar-text">Jurnal Memorial</span>
                    </a>
                    <a href="{{ route('akuntansi.laporan') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.laporan') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Laba Rugi & Neraca">
                        <i class="fas fa-file-invoice-dollar w-4 text-center"></i>
                        <span class="sidebar-text">Laba Rugi &amp; Neraca</span>
                    </a>
                    <a href="{{ route('akuntansi.arus-kas') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('akuntansi.arus-kas') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Laporan Arus Kas SAK">
                        <i class="fas fa-money-bill-wave w-4 text-center text-teal-400"></i>
                        <span class="sidebar-text">Laporan Arus Kas</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 3: Pusat Laporan & Rekapitulasi -->
            @php $isLaporanActive = request()->routeIs('laporan.*'); @endphp
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('laporanSubmenu', 'laporanChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isLaporanActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-file-contract w-5 text-center text-sm text-cyan-400 shrink-0"></i>
                        <span class="sidebar-text truncate">Laporan &amp; Rekap</span>
                    </div>
                    <i id="laporanChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isLaporanActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="laporanSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isLaporanActive ? '' : 'hidden' }}">
                    <a href="{{ route('laporan.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.index') ? 'bg-cyan-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Reporting Hub">
                        <i class="fas fa-folder-tree text-cyan-400 w-4 text-center"></i>
                        <span class="sidebar-text">Reporting Hub</span>
                    </a>
                    <a href="{{ route('laporan.cugil-rekap') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.cugil-rekap') ? 'bg-amber-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Rekap Stok & Olahan">
                        <i class="fas fa-recycle text-amber-400 w-4 text-center"></i>
                        <span class="sidebar-text">Rekap Stok &amp; CUGIL</span>
                    </a>
                    <a href="{{ route('laporan.pembelian') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.pembelian') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Rekap Pembelian Bahan">
                        <i class="fas fa-truck-ramp-box text-blue-400 w-4 text-center"></i>
                        <span class="sidebar-text">Rekap Pembelian</span>
                    </a>
                    <a href="{{ route('laporan.penjualan') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.penjualan') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Rekap Penjualan Gilingan">
                        <i class="fas fa-hand-holding-dollar text-emerald-400 w-4 text-center"></i>
                        <span class="sidebar-text">Rekap Penjualan</span>
                    </a>
                    <a href="{{ route('laporan.keuangan') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.keuangan') ? 'bg-yellow-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Laba Rugi & Neraca">
                        <i class="fas fa-scale-balanced text-yellow-400 w-4 text-center"></i>
                        <span class="sidebar-text">Laba Rugi &amp; Neraca</span>
                    </a>
                    <a href="{{ route('laporan.pajak') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('laporan.pajak') ? 'bg-purple-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Rekapitulasi Pajak">
                        <i class="fas fa-shield-halved text-purple-400 w-4 text-center"></i>
                        <span class="sidebar-text">Rekapitulasi Pajak</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 4: Analisis & Business Intelligence -->
            <a href="{{ route('analisis.index') }}" class="sidebar-link-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('analisis.*') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-orange-500/25 font-bold' : 'text-amber-400/90 hover:bg-slate-800/60 hover:text-amber-300' }}" title="Analisis & Business Intelligence">
                <i class="fas fa-chart-pie w-5 text-center text-base text-amber-400"></i>
                <span class="sidebar-text font-bold">Executive BI &amp; Analisis</span>
            </a>

            <!-- SECTION 3: Direksi & Pengawasan -->
            @php $isDireksiActive = request()->routeIs('pajak.*') || request()->routeIs('anggaran.*') || request()->routeIs('proyek.*'); @endphp
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('direksiSubmenu', 'direksiChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isDireksiActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-crown w-5 text-center text-sm text-yellow-400 shrink-0"></i>
                        <span class="sidebar-text truncate">Direksi &amp; Tax BOD</span>
                    </div>
                    <i id="direksiChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isDireksiActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="direksiSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isDireksiActive ? '' : 'hidden' }}">
                    <a href="{{ route('pajak.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('pajak.*') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Manajemen Pajak">
                        <i class="fas fa-shield-halved w-4 text-center text-amber-400"></i>
                        <span class="sidebar-text">Manajemen Pajak (BOD)</span>
                    </a>
                    <a href="{{ route('anggaran.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('anggaran.*') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Approval Anggaran">
                        <i class="fas fa-hand-holding-dollar w-4 text-center text-emerald-400"></i>
                        <span class="sidebar-text">Approval Anggaran</span>
                    </a>
                    <a href="{{ route('proyek.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('proyek.*') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Proyek & Billing">
                        <i class="fas fa-diagram-project w-4 text-center text-sky-400"></i>
                        <span class="sidebar-text">Proyek &amp; Billing</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 4: Manajemen User & Otoritas (RBAC) -->
            @php $isUsersActive = request()->routeIs('users.*') || request()->routeIs('roles.*'); @endphp
            @if(auth()->check() && (auth()->user()->isBOD() || auth()->user()->isAdmin()))
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('usersSubmenu', 'usersChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isUsersActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-users-gear w-5 text-center text-sm text-purple-400 shrink-0"></i>
                        <span class="sidebar-text truncate">User &amp; Hak Akses</span>
                    </div>
                    <i id="usersChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isUsersActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="usersSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isUsersActive ? '' : 'hidden' }}">
                    <a href="{{ route('users.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('users.*') ? 'bg-purple-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Manajemen Pengguna">
                        <i class="fas fa-users text-purple-400 w-4 text-center"></i>
                        <span class="sidebar-text">Daftar Pengguna</span>
                    </a>
                    <a href="{{ route('roles.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('roles.*') ? 'bg-purple-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Role & Hak Akses">
                        <i class="fas fa-shield-halved text-purple-400 w-4 text-center"></i>
                        <span class="sidebar-text">Role &amp; Hak Akses</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- SECTION 5: Konfigurasi -->
            @php $isConfigActive = request()->routeIs('perusahaan.*'); @endphp
            <div class="accordion-group pt-1">
                <button type="button" onclick="toggleAccordion('configSubmenu', 'configChevron')" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $isConfigActive ? 'text-amber-400 bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i class="fas fa-gear w-5 text-center text-sm text-slate-400 shrink-0"></i>
                        <span class="sidebar-text truncate">Pengaturan &amp; Info</span>
                    </div>
                    <i id="configChevron" class="fas fa-chevron-down text-[10px] sidebar-chevron transition-transform duration-200 {{ $isConfigActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="configSubmenu" class="submenu-container pl-3 pr-1 pt-1 space-y-1 {{ $isConfigActive ? '' : 'hidden' }}">
                    <a href="{{ route('perusahaan.index') }}" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('perusahaan.*') ? 'bg-[#ff8c00] text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" title="Profil PT PBS">
                        <i class="fas fa-building w-4 text-center text-orange-400"></i>
                        <span class="sidebar-text">Profil PT PBS</span>
                    </a>
                    <a href="{{ url('/') }}" target="_blank" class="sidebar-link-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition" title="Portal Landing Page">
                        <i class="fas fa-arrow-up-right-from-square w-4 text-center"></i>
                        <span class="sidebar-text">Portal Landing Page</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- Current User Profile / BOD Card -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-900/60 sidebar-user-card">
            <div class="flex items-center gap-3 mb-2.5">
                <div class="h-10 w-10 rounded-xl border border-amber-500/40 bg-amber-500/10 flex items-center justify-center text-amber-400 font-bold overflow-hidden shrink-0">
                    <img src="{{ asset('images/ayahrompi.png') }}" alt="User" class="h-full w-full object-cover" onerror="this.style.display='none'">
                    <span>K</span>
                </div>
                <div class="overflow-hidden user-info-text">
                    <h4 class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Kurniawan, S.E.' }}</h4>
                    <p class="text-[10px] text-amber-400 truncate">{{ auth()->user()->position ?? 'BOD (Finance & Tax)' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-2.5 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 transition" title="Keluar Sistem">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span class="sidebar-text">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-[#0a1628]/90 backdrop-blur-md border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            
            <div class="flex items-center gap-3">
                <!-- Hamburger Button (Desktop toggle & Mobile Drawer) -->
                <button type="button" onclick="handleHamburgerClick()" class="h-10 w-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-amber-400 flex items-center justify-center border border-slate-700/80 shadow-sm transition" title="Toggle Sidebar">
                    <i class="fas fa-bars-staggered text-base"></i>
                </button>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden sm:inline">Internal Secure Node &bull;</span> PT PBS
                </span>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right text-xs hidden sm:block">
                    <span class="text-slate-400 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="h-6 w-px bg-slate-800 hidden sm:block"></div>
                <div class="text-xs font-semibold text-amber-400 flex items-center gap-1.5 bg-amber-500/10 px-3 py-1.5 rounded-lg border border-amber-500/20">
                    <i class="fas fa-shield-halved text-[11px]"></i>
                    <span class="hidden sm:inline">Eksklusif Intern</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between mb-4 shadow-md">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-circle-check text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-lg">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center justify-between mb-4 shadow-md">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-circle-exclamation text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white text-lg">&times;</button>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm flex items-center justify-between mb-4 shadow-md">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-triangle-exclamation text-lg"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-white text-lg">&times;</button>
                </div>
            @endif
        </div>

        <!-- Page Body Content -->
        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="p-6 border-t border-slate-800/60 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} PT Pinastika Bhakti Semesta (PBS). Sistem ERP &amp; Akuntansi Internal Terpadu.
        </footer>
    </div>

    <!-- JavaScript for Hamburger Toggle, Mini-Sidebar & Accordion Submenus -->
    <script>
        // 1. Accordion Sub-menu Toggle
        function toggleAccordion(submenuId, chevronId) {
            const sidebar = document.getElementById('mainSidebar');
            
            // If sidebar is collapsed to icon-only, auto-expand first so submenus become accessible
            if (sidebar.classList.contains('sidebar-collapsed')) {
                toggleSidebarDesktop();
            }

            const submenu = document.getElementById(submenuId);
            const chevron = document.getElementById(chevronId);

            if (submenu) {
                const isHidden = submenu.classList.contains('hidden');
                if (isHidden) {
                    submenu.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                } else {
                    submenu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            }
        }

        // 2. Hamburger Click Router (Desktop vs Mobile)
        function handleHamburgerClick() {
            if (window.innerWidth < 768) {
                toggleSidebarMobile();
            } else {
                toggleSidebarDesktop();
            }
        }

        // 3. Desktop Collapse / Mini-Sidebar Toggle
        function toggleSidebarDesktop() {
            const sidebar = document.getElementById('mainSidebar');
            const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('pbs_sidebar_collapsed', isCollapsed ? '1' : '0');
        }

        // 4. Mobile Drawer Toggle
        function toggleSidebarMobile() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileBackdrop');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // 5. Restore User Preference on Page Load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth >= 768) {
                const savedState = localStorage.getItem('pbs_sidebar_collapsed');
                if (savedState === '1') {
                    document.getElementById('mainSidebar').classList.add('sidebar-collapsed');
                }
            }
        });
    </script>
</body>
</html>
