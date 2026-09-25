import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { 
    Building2, 
    ShieldCheck, 
    Layers, 
    Award, 
    GraduationCap, 
    PieChart, 
    Globe, 
    MapPin, 
    Phone, 
    Mail, 
    ArrowRight, 
    Menu, 
    X, 
    CheckCircle2, 
    Eye, 
    Target, 
    BarChart3, 
    Lock, 
    Boxes,
    LogIn,
    LayoutDashboard,
    ExternalLink,
    Landmark,
    Briefcase,
    UserCheck
} from 'lucide-react';
import { dashboard, login } from '@/routes';
const register = () => '/login';
import type { User } from '@/types';

export default function Welcome() {
    const { auth } = usePage<{ auth?: { user?: User | null } }>().props;
    const user = auth?.user;
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <>
            <Head>
                <title>SimpleAkunting - Platform Akuntansi & ERP Multi-Tenant</title>
                <meta 
                    name="description" 
                    content="SimpleAkunting - Platform dan layanan akuntansi profesional multi-tenant untuk UMKM, BUMDesa, Koperasi, dan Perusahaan di Jawa Timur dan Indonesia." 
                />
                <link rel="icon" href="/favicon.ico?v=2" sizes="any" />
                <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2" />
                <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2" />
                <link rel="icon" type="image/png" href="/images/favicon.png?v=2" />
                <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2" />
            </Head>

            <div className="min-h-screen bg-gradient-to-br from-[#0a1628] via-[#1a2a4a] to-[#0d1b2a] text-[#e0e0e0] font-sans selection:bg-[#ff8c00]/30 selection:text-white">
                {/* Background ambient lighting */}
                <div className="pointer-events-none fixed inset-0 overflow-hidden">
                    <div className="absolute -top-40 -left-40 h-[500px] w-[500px] rounded-full bg-[#ff8c00]/10 blur-[120px]" />
                    <div className="absolute top-1/3 -right-40 h-[600px] w-[600px] rounded-full bg-blue-600/10 blur-[140px]" />
                    <div className="absolute -bottom-40 left-1/3 h-[500px] w-[500px] rounded-full bg-amber-500/10 blur-[130px]" />
                </div>

                <div className="relative mx-auto max-w-[1140px] px-6 py-4">
                    {/* Header & Navigation */}
                    <header className="flex items-center justify-between py-6">
                        <a href="#" className="flex items-center gap-2 text-2xl font-bold tracking-tight text-[#ff8c00]">
                            <span>Simple</span>
                            <span className="text-white">Akunting</span>
                        </a>

                        {/* Desktop Navigation */}
                        <nav className="hidden items-center gap-7 text-[0.95rem] font-medium text-[#8fa8c8] md:flex">
                            <a href="#" className="transition hover:text-[#ff8c00]">Beranda</a>
                            <a href="#about" className="transition hover:text-[#ff8c00]">Tentang Kami</a>
                            <a href="#features" className="transition hover:text-[#ff8c00]">Fitur</a>
                            <a href="#profile" className="transition hover:text-[#ff8c00]">Profil Pimpinan</a>
                            <a 
                                href="/panduanhibahSA.html" 
                                target="_blank" 
                                rel="noreferrer" 
                                className="flex items-center gap-1 transition hover:text-[#ff8c00]"
                            >
                                <span>Hibah Software</span>
                                <ExternalLink className="h-3.5 w-3.5 opacity-70" />
                            </a>
                            <a href="#links-hub" className="transition hover:text-[#ff8c00]">Akses Sistem</a>
                            <a href="#contact" className="transition hover:text-[#ff8c00]">Kontak</a>

                            {user ? (
                                <Link
                                    href={dashboard()}
                                    className="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-5 py-2 font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[#e07b00] hover:-translate-y-0.5"
                                >
                                    <LayoutDashboard className="h-4 w-4" />
                                    <span>Dashboard</span>
                                </Link>
                            ) : (
                                <div className="flex items-center gap-3">
                                    <Link
                                        href={login()}
                                        className="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-5 py-2 font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[#e07b00] hover:-translate-y-0.5"
                                    >
                                        <LogIn className="h-4 w-4" />
                                        <span>Login Admin</span>
                                    </Link>
                                    <Link
                                        href={register()}
                                        className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 font-semibold text-white/90 backdrop-blur-sm transition hover:bg-white/10 hover:border-white/40"
                                    >
                                        <span>Daftar</span>
                                    </Link>
                                </div>
                            )}
                        </nav>

                        {/* Mobile Menu Button */}
                        <button
                            type="button"
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="inline-flex items-center justify-center rounded-lg p-2 text-white hover:bg-white/10 md:hidden"
                            aria-label="Toggle Menu"
                        >
                            {mobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                        </button>
                    </header>

                    {/* Mobile Dropdown Menu */}
                    {mobileMenuOpen && (
                        <div className="relative z-50 mb-6 flex flex-col gap-4 rounded-2xl border border-white/10 bg-[#0a1628]/95 p-6 backdrop-blur-xl shadow-2xl md:hidden">
                            <a 
                                href="#" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Beranda
                            </a>
                            <a 
                                href="#about" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Tentang Kami
                            </a>
                            <a 
                                href="#features" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Fitur
                            </a>
                            <a 
                                href="#profile" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Profil Pimpinan
                            </a>
                            <a 
                                href="/panduanhibahSA.html" 
                                target="_blank" 
                                rel="noreferrer"
                                onClick={() => setMobileMenuOpen(false)}
                                className="flex items-center justify-between text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                <span>Hibah Software</span>
                                <ExternalLink className="h-4 w-4" />
                            </a>
                            <a 
                                href="#links-hub" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Akses Sistem
                            </a>
                            <a 
                                href="#contact" 
                                onClick={() => setMobileMenuOpen(false)}
                                className="text-[#8fa8c8] hover:text-[#ff8c00]"
                            >
                                Kontak
                            </a>
                            <div className="pt-2">
                                {user ? (
                                    <Link
                                        href={dashboard()}
                                        className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#ff8c00] py-2.5 font-semibold text-white"
                                    >
                                        <LayoutDashboard className="h-4 w-4" />
                                        <span>Dashboard</span>
                                    </Link>
                                ) : (
                                    <div className="flex flex-col gap-2">
                                        <Link
                                            href={login()}
                                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#ff8c00] py-2.5 font-semibold text-white"
                                        >
                                            <LogIn className="h-4 w-4" />
                                            <span>Login Admin</span>
                                        </Link>
                                        <Link
                                            href={register()}
                                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 py-2.5 font-semibold text-white"
                                        >
                                            <span>Daftar Akun Baru</span>
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Hero Section */}
                    <section className="py-16 text-center lg:py-24">
                        <div className="inline-flex items-center gap-2 rounded-full border border-[#ff8c00]/30 bg-[#ff8c00]/10 px-4 py-1.5 text-xs font-semibold tracking-wider text-[#ff8c00] uppercase mb-6 backdrop-blur-md">
                            <span className="relative flex h-2 w-2">
                                <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#ff8c00] opacity-75"></span>
                                <span className="relative inline-flex h-2 w-2 rounded-full bg-[#ff8c00]"></span>
                            </span>
                            Platform Akuntansi & ERP Multi-Tenant
                        </div>
                        <h1 className="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-[1.15]">
                            Solusi Akuntansi Modern <br />
                            <span className="text-[#ff8c00] underline decoration-[#ff8c00]/40 decoration-wavy decoration-from-font">
                                Multi-Tenant
                            </span> & Berkelanjutan
                        </h1>
                        <p className="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-[#8fa8c8]">
                            Mengelola akuntansi banyak perusahaan dan unit usaha dari satu platform terpusat. Setiap entitas
                            mendapatkan database terpisah, keamanan tinggi, dan fitur lengkap sesuai Standar Akuntansi Keuangan (SAK).
                        </p>

                        <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
                            <a
                                href="#features"
                                className="inline-flex items-center gap-2 rounded-full bg-[#ff8c00] px-7 py-3 text-base font-semibold text-white shadow-xl shadow-orange-500/25 transition hover:bg-[#e07b00] hover:-translate-y-0.5"
                            >
                                <span>Eksplorasi Fitur</span>
                                <ArrowRight className="h-4 w-4" />
                            </a>
                            <a
                                href="https://wa.me/6282141643495"
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-7 py-3 text-base font-semibold text-white backdrop-blur-md transition hover:bg-white/10 hover:border-white/40"
                            >
                                <Phone className="h-4 w-4 text-[#ff8c00]" />
                                <span>Konsultasi Gratis</span>
                            </a>
                        </div>
                    </section>

                    {/* About Section */}
                    <section id="about" className="py-20 border-t border-white/10">
                        <div className="text-center mb-14">
                            <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Tentang simpleakunting.id</h2>
                            <div className="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]" />
                        </div>

                        <div className="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center">
                            <div className="space-y-6 lg:col-span-6">
                                <p className="text-lg leading-relaxed text-white/90">
                                    <span className="font-bold text-[#ff8c00]">simpleakunting.id</span> adalah platform dan
                                    layanan akuntansi profesional yang berkedudukan di <strong className="text-white">Mojokerto, Jawa Timur</strong>. Kami hadir
                                    sebagai mitra strategis bagi pelaku usaha (UMKM), BUMDesa, Koperasi, hingga perusahaan swasta.
                                </p>
                                <p className="text-base leading-relaxed text-[#8fa8c8]">
                                    Didirikan dan dipimpin langsung oleh praktisi berpengalaman dengan latar belakang akademis yang kuat,
                                    kami memadukan keahlian teknis bersertifikasi internasional dengan pemahaman mendalam mengenai
                                    regulasi perpajakan dan kondisi tata kelola lapangan di Indonesia.
                                </p>
                                <div className="grid grid-cols-2 gap-4 pt-2">
                                    <div className="rounded-xl border border-white/10 bg-white/[0.03] p-4 text-center">
                                        <div className="text-2xl font-bold text-[#ff8c00]">10+ Tahun</div>
                                        <div className="text-xs text-[#8fa8c8] mt-1">Pengalaman Praktis & Pendampingan</div>
                                    </div>
                                    <div className="rounded-xl border border-white/10 bg-white/[0.03] p-4 text-center">
                                        <div className="text-2xl font-bold text-[#ff8c00]">Ratusan</div>
                                        <div className="text-xs text-[#8fa8c8] mt-1">UMKM & BUMDesa Terbantu</div>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-6 rounded-2xl border border-white/10 border-l-4 border-l-[#ff8c00] bg-white/[0.03] p-8 shadow-2xl backdrop-blur-md lg:col-span-6">
                                <div>
                                    <h3 className="flex items-center gap-2.5 text-xl font-bold text-[#ff8c00]">
                                        <Eye className="h-5 w-5" />
                                        <span>Visi</span>
                                    </h3>
                                    <p className="mt-2.5 italic text-slate-200 leading-relaxed">
                                        "Menjadi mitra konsultan terpercaya yang mampu meningkatkan tata kelola keuangan dan manajemen bisnis yang akuntabel bagi UMKM dan BUMDesa di Jawa Timur."
                                    </p>
                                </div>

                                <div className="border-t border-white/10 pt-6">
                                    <h3 className="flex items-center gap-2.5 text-xl font-bold text-[#ff8c00]">
                                        <Target className="h-5 w-5" />
                                        <span>Misi</span>
                                    </h3>
                                    <ul className="mt-3.5 space-y-3">
                                        <li className="flex items-start gap-3 text-slate-200">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-[#ff8c00] mt-0.5" />
                                            <span>Layanan pendampingan akuntansi presisi dan terstandarisasi.</span>
                                        </li>
                                        <li className="flex items-start gap-3 text-slate-200">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-[#ff8c00] mt-0.5" />
                                            <span>Peningkatan kapasitas sumber daya manusia (SDM) melalui pelatihan & workshop.</span>
                                        </li>
                                        <li className="flex items-start gap-3 text-slate-200">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-[#ff8c00] mt-0.5" />
                                            <span>Solusi audit, evaluasi kelayakan usaha & mitigasi risiko bisnis komprehensif.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features Section */}
                    <section id="features" className="py-20 border-t border-white/10">
                        <div className="text-center mb-14">
                            <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Fitur Unggulan</h2>
                            <p className="mt-3 text-slate-400 max-w-xl mx-auto">Dirancang spesifik untuk kebutuhan pembukuan entitas bisnis lokal dengan standar kelas industri.</p>
                            <div className="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]" />
                        </div>

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div className="group rounded-2xl border border-white/10 bg-white/[0.04] p-8 transition duration-200 hover:-translate-y-1.5 hover:border-[#ff8c00]/50 hover:bg-white/[0.07]">
                                <div className="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00] group-hover:bg-[#ff8c00] group-hover:text-white transition">
                                    <BarChart3 className="h-6 w-6" />
                                </div>
                                <h3 className="text-xl font-bold text-white mb-2.5">Akuntansi Lengkap & SAK</h3>
                                <p className="text-sm leading-relaxed text-[#8fa8c8]">
                                    Point of Sales (POS), Jurnal Umum & Khusus, Buku Besar, Neraca Saldo, Laporan Laba Rugi, 
                                    Arus Kas, dan Perubahan Modal terintegrasi sesuai Standar Akuntansi Keuangan.
                                </p>
                            </div>

                            <div className="group rounded-2xl border border-white/10 bg-white/[0.04] p-8 transition duration-200 hover:-translate-y-1.5 hover:border-[#ff8c00]/50 hover:bg-white/[0.07]">
                                <div className="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00] group-hover:bg-[#ff8c00] group-hover:text-white transition">
                                    <Lock className="h-6 w-6" />
                                </div>
                                <h3 className="text-xl font-bold text-white mb-2.5">Isolasi Data Multi-Tenant</h3>
                                <p className="text-sm leading-relaxed text-[#8fa8c8]">
                                    Setiap unit usaha atau klien memiliki basis data terpisah (isolated database). 
                                    Privasi dan kerahasiaan keuangan Anda terjamin 100% aman tanpa tumpang tindih data.
                                </p>
                            </div>

                            <div className="group rounded-2xl border border-white/10 bg-white/[0.04] p-8 transition duration-200 hover:-translate-y-1.5 hover:border-[#ff8c00]/50 hover:bg-white/[0.07]">
                                <div className="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00] group-hover:bg-[#ff8c00] group-hover:text-white transition">
                                    <Boxes className="h-6 w-6" />
                                </div>
                                <h3 className="text-xl font-bold text-white mb-2.5">Multi-Modul Terintegrasi</h3>
                                <p className="text-sm leading-relaxed text-[#8fa8c8]">
                                    Mendukung Penjualan, Pembelian, Manajemen Stok & Persediaan, Simpan Pinjam Koperasi, 
                                    Manufaktur HPP, hingga modul Pertanian & Perkebunan (Agriculture).
                                </p>
                            </div>
                        </div>
                    </section>

                    {/* Profile Section */}
                    <section id="profile" className="py-20 border-t border-white/10">
                        <div className="text-center mb-14">
                            <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Profil Pimpinan</h2>
                            <p className="mt-3 text-slate-400">Kepemimpinan teruji dengan reputasi dan integritas profesional.</p>
                            <div className="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]" />
                        </div>

                        <div className="flex flex-col items-center gap-12 lg:flex-row lg:items-center">
                            {/* Profile Image with Glow Rings */}
                            <div className="relative w-full max-w-[340px] shrink-0">
                                <div className="relative z-10 overflow-hidden rounded-2xl border border-white/20 shadow-2xl bg-slate-900">
                                    <img
                                        src="/images/ayahrompi.png"
                                        alt="Kurniawan - Managing Director"
                                        className="h-[400px] w-full object-cover object-top transition duration-300 hover:scale-105"
                                        onError={(e) => {
                                            // Fallback if image fails
                                            (e.target as HTMLElement).style.display = 'none';
                                        }}
                                    />
                                </div>
                                {/* Glow elements */}
                                <div className="absolute -top-4 -left-4 h-32 w-32 rounded-full bg-[#ff8c00]/30 blur-2xl -z-0" />
                                <div className="absolute -bottom-4 -right-4 h-36 w-36 rounded-full bg-blue-600/30 blur-2xl -z-0" />
                            </div>

                            {/* Profile Details */}
                            <div className="flex-1 space-y-5 text-center lg:text-left">
                                <span className="text-xs font-bold tracking-widest text-[#ff8c00] uppercase">
                                    Managing Director
                                </span>
                                <h3 className="text-2xl font-extrabold text-white sm:text-3xl leading-snug">
                                    Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.
                                </h3>

                                <div className="flex flex-wrap justify-center lg:justify-start gap-2">
                                    {['CMA', 'CIBA', 'CIAP', 'CA', 'Ak.'].map((badge) => (
                                        <span
                                            key={badge}
                                            className="rounded-full border border-[#ff8c00]/40 bg-[#ff8c00]/10 px-3 py-1 text-xs font-bold text-[#ff8c00]"
                                        >
                                            {badge}
                                        </span>
                                    ))}
                                </div>

                                <blockquote className="border-l-0 lg:border-l-4 border-[#ff8c00] pl-0 lg:pl-4 italic text-[#8fa8c8] text-base leading-relaxed py-1">
                                    "simpleakunting.id dipimpin oleh seorang profesional yang memiliki kombinasi unik antara pengalaman praktis di industri, penugasan di sektor publik, dan latar belakang akademis yang solid."
                                </blockquote>

                                {/* Qualifications Grid */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-left">
                                    <div className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5 hover:bg-white/[0.06] transition">
                                        <Award className="h-6 w-6 text-[#ff8c00] shrink-0" />
                                        <div>
                                            <h4 className="text-sm font-bold text-white">Chartered Accountant (CA)</h4>
                                            <p className="text-xs text-[#8fa8c8]">Ikatan Akuntan Indonesia</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5 hover:bg-white/[0.06] transition">
                                        <GraduationCap className="h-6 w-6 text-[#ff8c00] shrink-0" />
                                        <div>
                                            <h4 className="text-sm font-bold text-white">Magister Akuntansi (M.Ak)</h4>
                                            <p className="text-xs text-[#8fa8c8]">Univ. Trunojoyo Madura</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5 hover:bg-white/[0.06] transition">
                                        <PieChart className="h-6 w-6 text-[#ff8c00] shrink-0" />
                                        <div>
                                            <h4 className="text-sm font-bold text-white">Certified Mgt. Accountant (CMA)</h4>
                                            <p className="text-xs text-[#8fa8c8]">Universitas Airlangga</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5 hover:bg-white/[0.06] transition">
                                        <Globe className="h-6 w-6 text-[#ff8c00] shrink-0" />
                                        <div>
                                            <h4 className="text-sm font-bold text-white">Intl. Business Analysis (CIBA)</h4>
                                            <p className="text-xs text-[#8fa8c8]">Univ. Kristen Petra</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Experience Grid */}
                        <div className="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-7 text-center transition hover:-translate-y-1.5 hover:border-[#ff8c00]/40">
                                <div className="absolute top-0 left-0 right-0 h-1 bg-[#ff8c00] scale-x-0 group-hover:scale-x-100 transition-transform duration-300" />
                                <div className="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00]">
                                    <Landmark className="h-6 w-6" />
                                </div>
                                <h3 className="text-lg font-bold text-white mb-2">Sektor Publik</h3>
                                <p className="text-sm text-[#8fa8c8] leading-relaxed">
                                    Tenaga Ahli Klinik BUMDesa Provinsi Jawa Timur & Penilai BUMDesa Berhasil (2015-2024).
                                </p>
                            </div>

                            <div className="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-7 text-center transition hover:-translate-y-1.5 hover:border-[#ff8c00]/40">
                                <div className="absolute top-0 left-0 right-0 h-1 bg-[#ff8c00] scale-x-0 group-hover:scale-x-100 transition-transform duration-300" />
                                <div className="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00]">
                                    <Briefcase className="h-6 w-6" />
                                </div>
                                <h3 className="text-lg font-bold text-white mb-2">Sektor Korporasi</h3>
                                <p className="text-sm text-[#8fa8c8] leading-relaxed">
                                    Manajer Keuangan, Audit Internal, Pengendalian Anggaran & CSR (Danone, USAID, Medco).
                                </p>
                            </div>

                            <div className="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-7 text-center transition hover:-translate-y-1.5 hover:border-[#ff8c00]/40">
                                <div className="absolute top-0 left-0 right-0 h-1 bg-[#ff8c00] scale-x-0 group-hover:scale-x-100 transition-transform duration-300" />
                                <div className="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#ff8c00]/10 text-[#ff8c00]">
                                    <GraduationCap className="h-6 w-6" />
                                </div>
                                <h3 className="text-lg font-bold text-white mb-2">Akademisi & Mentor</h3>
                                <p className="text-sm text-[#8fa8c8] leading-relaxed">
                                    Dosen Tetap Akuntansi, Mentor Inkubasi Bisnis, dan Narasumber Lokakarya Tingkat Provinsi.
                                </p>
                            </div>
                        </div>
                    </section>

                    {/* Ecosystem / Links Hub */}
                    <section id="links-hub" className="py-20 border-t border-white/10">
                        <div className="relative overflow-hidden rounded-3xl border border-[#ff8c00]/30 bg-gradient-to-br from-[#ff8c00]/10 via-[#1a2a4a]/80 to-[#0a1628] p-8 md:p-12 shadow-2xl backdrop-blur-xl">
                            {/* Decorative ambient ball */}
                            <div className="absolute -top-20 -right-20 h-56 w-56 rounded-full bg-[#ff8c00]/20 blur-3xl pointer-events-none" />

                            <div className="text-center mb-10">
                                <span className="text-xs font-bold tracking-widest text-[#ff8c00] uppercase">
                                    AKSES CEPAT
                                </span>
                                <h2 className="mt-2 text-2xl font-extrabold text-white sm:text-3xl">
                                    Sistem & Layanan Pendukung
                                </h2>
                                <p className="mt-2 text-sm text-[#8fa8c8] max-w-xl mx-auto">
                                    Hubungkan dengan platform akuntansi, portal koperasi, dan tata kelola digital lainnya dalam ekosistem kami.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {[
                                    {
                                        title: 'Solusi Consult',
                                        desc: 'Profil & Layanan Konsultan Bisnis',
                                        url: 'https://simpleakunting.my.id/',
                                        icon: Globe
                                    },
                                    {
                                        title: 'SimpleAkunting v2',
                                        desc: 'Classic Login Platform',
                                        url: 'https://simpleakunting.biz.id/login.php',
                                        icon: LogIn
                                    },
                                    {
                                        title: 'SimpleAkunting v3',
                                        desc: 'Sistem Akuntansi Koperasi & UMKM v3',
                                        url: 'https://v3.simpleakunting.biz.id/login',
                                        icon: Building2
                                    },
                                    {
                                        title: 'SimpleAkunting v4',
                                        desc: 'Sistem Akuntansi BUMDesa & Koperasi v4',
                                        url: 'https://v4.simpleakunting.biz.id/admin/login',
                                        icon: ShieldCheck
                                    },
                                    {
                                        title: 'Puspa Candra',
                                        desc: 'Login Portal Pelaku UMKM Mandiri',
                                        url: 'https://umkm.simkopdes.biz.id/login',
                                        icon: UserCheck
                                    },
                                    {
                                        title: 'BUMDesa Digital',
                                        desc: 'Platform Akuntansi Digital BUMDesa Jatim',
                                        url: 'https://bumdesadigital.my.id/',
                                        icon: Landmark
                                    },
                                ].map((item, idx) => {
                                    const IconComponent = item.icon;
                                    return (
                                        <a
                                            key={idx}
                                            href={item.url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="group flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] p-4.5 transition duration-200 hover:-translate-y-1 hover:border-[#ff8c00]/50 hover:bg-[#ff8c00]/10"
                                        >
                                            <div className="flex items-center gap-3.5">
                                                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#ff8c00]/15 text-[#ff8c00] group-hover:bg-[#ff8c00] group-hover:text-white transition">
                                                    <IconComponent className="h-5 w-5" />
                                                </div>
                                                <div>
                                                    <h4 className="text-sm font-bold text-white group-hover:text-[#ff8c00] transition">
                                                        {item.title}
                                                    </h4>
                                                    <p className="text-xs text-[#8fa8c8]">{item.desc}</p>
                                                </div>
                                            </div>
                                            <ArrowRight className="h-4 w-4 text-[#5a7090] group-hover:text-[#ff8c00] group-hover:translate-x-1 transition" />
                                        </a>
                                    );
                                })}
                            </div>
                        </div>
                    </section>

                    {/* Contact Section */}
                    <section id="contact" className="py-20 border-t border-white/10">
                        <div className="text-center mb-14">
                            <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Hubungi Kami</h2>
                            <p className="mt-3 text-slate-400">Siap meningkatkan tata kelola keuangan bisnis Anda bersama kami.</p>
                            <div className="mx-auto mt-3 h-1 w-16 rounded bg-[#ff8c00]" />
                        </div>

                        <div className="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] shadow-2xl backdrop-blur-md">
                            <div className="grid grid-cols-1 lg:grid-cols-12">
                                <div className="p-8 sm:p-12 lg:col-span-6 flex flex-col justify-center space-y-8">
                                    <div>
                                        <h3 className="text-2xl font-bold text-white">Konsultasikan Kebutuhan Anda</h3>
                                        <p className="mt-2 text-sm leading-relaxed text-[#8fa8c8]">
                                            Dapatkan solusi perangkat lunak akuntansi yang tepat guna, implementasi terarah, serta pendampingan langsung dari ahli.
                                        </p>
                                    </div>

                                    <div className="space-y-6">
                                        <div className="flex items-start gap-4">
                                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#ff8c00]/30 bg-[#ff8c00]/10 text-[#ff8c00]">
                                                <MapPin className="h-5 w-5" />
                                            </div>
                                            <div>
                                                <h4 className="text-sm font-bold text-white">Alamat Kantor</h4>
                                                <p className="text-sm text-[#8fa8c8] mt-0.5">
                                                    Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur, Indonesia.
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-4">
                                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#ff8c00]/30 bg-[#ff8c00]/10 text-[#ff8c00]">
                                                <Phone className="h-5 w-5" />
                                            </div>
                                            <div>
                                                <h4 className="text-sm font-bold text-white">Telepon / WhatsApp</h4>
                                                <p className="text-sm text-[#8fa8c8] mt-0.5">
                                                    <a 
                                                        href="https://wa.me/6282141643495" 
                                                        target="_blank" 
                                                        rel="noreferrer"
                                                        className="text-[#ff8c00] hover:underline"
                                                    >
                                                        +62 821 4164 3495
                                                    </a>
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-4">
                                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#ff8c00]/30 bg-[#ff8c00]/10 text-[#ff8c00]">
                                                <Mail className="h-5 w-5" />
                                            </div>
                                            <div>
                                                <h4 className="text-sm font-bold text-white">Email Resmi</h4>
                                                <p className="text-sm text-[#8fa8c8] mt-0.5">
                                                    <a 
                                                        href="mailto:kurniawan@petalmail.com" 
                                                        className="text-[#ff8c00] hover:underline"
                                                    >
                                                        kurniawan@petalmail.com
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Map Embed */}
                                <div className="min-h-[360px] lg:col-span-6 relative border-t lg:border-t-0 lg:border-l border-white/10 bg-slate-950">
                                    <iframe
                                        title="Google Map Mojokerto"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.779427499622!2d112.4340!3d-7.4670!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMjgnMDEuMiJTIDExMsKwMjYnMDIuNCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"
                                        className="h-full w-full border-0 filter grayscale invert contrast-90 opacity-80"
                                        allowFullScreen
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Footer */}
                    <footer className="py-12 text-center text-xs text-[#5a7090] border-t border-white/10 space-y-4">
                        <div className="inline-flex items-center gap-2 rounded-full border border-[#ff8c00]/30 bg-[#ff8c00]/10 px-4 py-1.5 text-xs font-medium text-white shadow-lg backdrop-blur-sm">
                            <Eye className="h-3.5 w-3.5 text-[#ff8c00]" />
                            <span>Total Kunjungan Platform: <strong className="text-[#ff8c00]">1.250+</strong></span>
                        </div>

                        <p className="leading-relaxed">
                            &copy; {new Date().getFullYear()} <strong className="text-slate-300">SimpleAkunting</strong>. Dibuat oleh{' '}
                            <span className="text-white font-medium">Kurniawan</span> dengan ❤️ untuk membantu bisnis berkembang lebih pesat. All rights reserved.
                        </p>

                        <div>
                            {user ? (
                                <Link href={dashboard()} className="text-xs text-slate-500 hover:text-[#ff8c00] transition">
                                    Buka Panel Dashboard
                                </Link>
                            ) : (
                                <Link href={login()} className="text-xs text-slate-500 hover:text-[#ff8c00] transition">
                                    Login Administrator Sistem
                                </Link>
                            )}
                        </div>
                    </footer>
                </div>
            </div>
        </>
    );
}
