<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Internal - PBS-ERP PT Pinastika Bhakti Semesta</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #070f1e 0%, #0a1628 50%, #0d1e38 100%);
            color: #e2e8f0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient glowing backgrounds -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-32 -left-32 h-[450px] w-[450px] rounded-full bg-amber-500/10 blur-[130px]"></div>
        <div class="absolute -bottom-32 -right-32 h-[500px] w-[500px] rounded-full bg-blue-600/10 blur-[140px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex h-20 w-20 rounded-2xl bg-white p-2 items-center justify-center shadow-xl shadow-emerald-900/20 mb-4 border border-slate-700/80 overflow-hidden">
                <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PT Pinastika Bhakti Semesta" class="h-full w-full object-contain">
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">PBS-ERP</h1>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mt-1">PT Pinastika Bhakti Semesta</p>
            
            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                <i class="fas fa-lock text-[10px]"></i>
                <span>Sistem Tertutup & Eksklusif Internal</span>
            </div>
        </div>

        <!-- Login Card -->
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl backdrop-blur-xl">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-white">Login Manajemen & Karyawan</h2>
                <p class="text-xs text-slate-400 mt-1">Gunakan akun kredensial resmi PT Pinastika Bhakti Semesta.</p>
            </div>

            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-amber-400 mt-0.5"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email Karyawan / BOD</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', 'kurniawan@pinastika.co.id') }}" 
                            required 
                            autofocus
                            placeholder="nama@pinastika.co.id"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-[#ff8c00] focus:ring-1 focus:ring-[#ff8c00] transition"
                        >
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fas fa-key text-xs"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            value="password"
                            required 
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-700/80 text-white text-sm focus:outline-none focus:border-[#ff8c00] focus:ring-1 focus:ring-[#ff8c00] transition"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-950 text-[#ff8c00] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs text-slate-400">Ingat sesi saya</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full mt-2 py-3 px-4 rounded-xl bg-[#ff8c00] hover:bg-[#e07b00] text-white text-sm font-bold shadow-lg shadow-orange-500/25 transition duration-200 flex items-center justify-center gap-2"
                >
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Masuk ke PBS-ERP</span>
                </button>
            </form>

            <!-- Quick Demo Credential Switcher for Development -->
            <div class="mt-6 pt-6 border-t border-slate-800 text-xs text-slate-400">
                <span class="font-semibold text-slate-300 block mb-2">Akses Cepat Pengujian Intern:</span>
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        type="button" 
                        onclick="document.getElementById('email').value='kurniawan@pinastika.co.id'; document.getElementById('password').value='password';"
                        class="p-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 text-left border border-slate-700/60 transition"
                    >
                        <strong class="text-amber-400 block text-[11px]">BOD Finance & Tax</strong>
                        <span class="text-[10px] text-slate-400">Kurniawan, S.E.</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="document.getElementById('email').value='finance@pinastika.co.id'; document.getElementById('password').value='password';"
                        class="p-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 text-left border border-slate-700/60 transition"
                    >
                        <strong class="text-sky-400 block text-[11px]">Finance & Accounting</strong>
                        <span class="text-[10px] text-slate-400">Finance Team</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-amber-400 transition flex items-center justify-center gap-1.5">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Beranda Resmi PT Pinastika Bhakti Semesta</span>
            </a>
        </div>
    </div>

</body>
</html>
