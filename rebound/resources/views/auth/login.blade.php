<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - REBOUND Flight Assistant</title>
    
    <!-- Favicon: AI Robot / Computer Bot Logo -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.svg">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', '"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.04)',
                        'floating': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.35s ease-in-out;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-[#F8FAFC] flex flex-col justify-center py-8 sm:py-12 px-6 sm:px-8 lg:px-8"
      x-data="loginPage">

    <div class="w-full max-w-md mx-auto text-center">
        <!-- Logo & Badge -->
        <a href="/" class="inline-flex items-center gap-2 mb-3 hover:opacity-90 transition justify-center">
            <x-logo size="lg" />
        </a>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
            Masuk ke Akun
        </h2>
    </div>

    <div class="mt-6 w-full max-w-md mx-auto">
        <div class="bg-white py-6 px-6 sm:px-8 shadow-sm border border-slate-200 rounded-2xl space-y-4">

            <!-- Alert info dari session -->
            @if(session('info'))
                <div class="p-3 bg-blue-50 border border-blue-200 text-brand-800 text-xs rounded-xl flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-brand-600 text-xs"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-900">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="list-disc list-inside text-rose-700 text-[11.5px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Interactive Error Alert Box (Firebase / Auth) -->
            <div x-show="errorMessage" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 :class="{ 'animate-shake': isShaking }"
                 class="p-3.5 bg-rose-50/90 border border-rose-200 text-rose-800 text-xs rounded-xl shadow-2xs flex items-start gap-3 relative">
                <div class="w-7 h-7 rounded-lg bg-rose-100 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                </div>
                <div class="flex-1 pr-4">
                    <div class="font-bold text-rose-900 text-xs" x-text="errorTitle"></div>
                    <div class="mt-0.5 text-[11.5px] leading-relaxed text-rose-700 font-medium" x-text="errorMessage"></div>
                </div>
                <button type="button" @click="clearError()" class="text-rose-400 hover:text-rose-700 transition cursor-pointer absolute top-3 right-3" title="Tutup">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <!-- Peringatan jika konfigurasi Firebase belum diisi -->
            @unless(config('firebase_web.configured'))
                <div class="p-3.5 bg-amber-50/90 border border-amber-200 text-amber-900 text-xs rounded-xl shadow-2xs flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-amber-950 text-xs">Konfigurasi Belum Lengkap</div>
                        <div class="mt-0.5 text-[11.5px] leading-relaxed text-amber-800">
                            Isi <code>FIREBASE_WEB_*</code> di <code>.env</code> untuk mengaktifkan login Google.
                        </div>
                    </div>
                </div>
            @endunless

            <!-- Email/Password Form -->
            <form @submit.prevent="signInWithEmail()" class="space-y-3.5">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope text-slate-400 text-xs absolute left-3 top-3"></i>
                        <input id="email" x-model="email" @input="clearFieldErrors()" name="email" type="email" autocomplete="email" required
                               placeholder="nama@email.com"
                               :class="errorType === 'invalid-email' || errorType === 'user-not-found' ? 'border-rose-400 bg-rose-50/30 focus:border-rose-500 ring-1 ring-rose-400/20' : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500'"
                               class="w-full rounded-lg pl-9 pr-3 py-2 text-xs text-slate-800 focus:outline-none transition">
                    </div>
                    <p x-show="errorType === 'user-not-found'" x-cloak class="text-[11px] text-rose-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Akun belum terdaftar. <a href="{{ route('register') }}" class="underline font-bold text-brand-700">Daftar</a>
                    </p>
                    <p x-show="errorType === 'invalid-email'" x-cloak class="text-[11px] text-rose-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Format email salah.
                    </p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock text-slate-400 text-xs absolute left-3 top-3"></i>
                        <input id="password" x-model="password" @input="clearFieldErrors()" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               :class="errorType === 'wrong-password' ? 'border-rose-400 bg-rose-50/30 focus:border-rose-500 ring-1 ring-rose-400/20' : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500'"
                               class="w-full rounded-lg pl-9 pr-3 py-2 text-xs text-slate-800 focus:outline-none transition">
                    </div>
                    <p x-show="errorType === 'wrong-password'" x-cloak class="text-[11px] text-rose-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Kata sandi salah.
                    </p>
                </div>

                <button type="submit"
                        :disabled="loading || !firebaseConfigured"
                        class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg shadow-2xs transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-3 text-[11px] font-medium text-slate-400">
                        atau
                    </span>
                </div>
            </div>

            <!-- Google Sign-In Button (Firebase Auth) -->
            <div>
                <button type="button"
                        @click="signInWithGoogle()"
                        :disabled="loading || !firebaseConfigured"
                        class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition flex items-center justify-center gap-2.5 group disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                    <!-- Official Google SVG Logo -->
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span x-show="!loading">Lanjutkan dengan Google</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Menghubungkan...
                    </span>
                </button>
            </div>

            <!-- Bottom Register Link -->
            <div class="text-center pt-1 text-xs text-slate-600 border-t border-slate-100">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700">Daftar</a>
            </div>

        </div>
    </div>

    <!-- Firebase JS SDK (compat) -->
    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
    @php
        $firebaseWebConfig = [
            'apiKey'            => config('firebase_web.api_key'),
            'authDomain'        => config('firebase_web.auth_domain'),
            'projectId'         => config('firebase_web.project_id'),
            'storageBucket'     => config('firebase_web.storage_bucket'),
            'messagingSenderId' => config('firebase_web.messaging_sender_id'),
            'appId'             => config('firebase_web.app_id'),
        ];
    @endphp
    <script>
        const firebaseConfig = @json($firebaseWebConfig);
        const firebaseConfigured = {{ config('firebase_web.configured') ? 'true' : 'false' }};
        const firebaseLoginUrl = '{{ route('auth.firebase') }}';
        const csrfToken = '{{ csrf_token() }}';

        if (firebaseConfigured) {
            firebase.initializeApp(firebaseConfig);
        }
        const fbAuth = firebaseConfigured ? firebase.auth() : null;

        // Terjemahan error Firebase Auth ke Bahasa Indonesia dengan deteksi tipe
        function parseAuthError(error) {
            const code = error?.code || '';
            let title = 'Peringatan Masuk Akun';
            let message = 'Terjadi kesalahan saat masuk akun.';
            let type = 'general';

            switch (code) {
                case 'auth/wrong-password':
                    title = 'Kata Sandi Salah';
                    message = 'Kata sandi yang Anda masukkan salah. Pastikan huruf besar/kecil dan angka sesuai.';
                    type = 'wrong-password';
                    break;
                case 'auth/invalid-credential':
                    title = 'Kredensial Tidak Valid';
                    message = 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.';
                    type = 'wrong-password';
                    break;
                case 'auth/user-not-found':
                    title = 'Akun Tidak Ditemukan';
                    message = 'Tidak ada akun yang terdaftar dengan alamat email ini. Silakan daftar terlebih dahulu.';
                    type = 'user-not-found';
                    break;
                case 'auth/invalid-email':
                    title = 'Format Email Tidak Valid';
                    message = 'Format alamat email yang Anda masukkan tidak sesuai (contoh: user@rebound.ai).';
                    type = 'invalid-email';
                    break;
                case 'auth/too-many-requests':
                    title = 'Percobaan Terlalu Banyak';
                    message = 'Terlalu banyak upaya login yang gagal. Akun diblokir sementara demi keamanan, silakan coba lagi beberapa saat.';
                    type = 'general';
                    break;
                case 'auth/network-request-failed':
                    title = 'Gangguan Koneksi Jaringan';
                    message = 'Gagal terhubung ke server autentikasi. Pastikan koneksi internet Anda aktif.';
                    type = 'general';
                    break;
                case 'auth/popup-closed-by-user':
                    title = 'Login Google Dibatalkan';
                    message = 'Jendela pop-up login Google ditutup sebelum proses verifikasi selesai.';
                    type = 'general';
                    break;
                case 'auth/popup-blocked':
                    title = 'Pop-up Diblokir Browser';
                    message = 'Peramban Anda memblokir jendela pop-up. Harap izinkan pop-up untuk situs ini.';
                    type = 'general';
                    break;
                default:
                    title = 'Gagal Masuk Akun';
                    message = error?.message || 'Terjadi kesalahan tak terduga saat memproses permintaan.';
                    type = 'general';
            }

            return { title, message, type };
        }

        // Kirim ID Token ke backend Laravel untuk diverifikasi & memulai sesi
        function submitFirebaseToken(idToken) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = firebaseLoginUrl;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = csrfToken;
            form.appendChild(csrf);

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = 'id_token';
            token.value = idToken;
            form.appendChild(token);

            document.body.appendChild(form);
            form.submit();
        }

        // Inisialisasi state Alpine setelah SDK dimuat
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginPage', () => ({
                loading: false,
                errorMessage: null,
                errorTitle: null,
                errorType: null,
                isShaking: false,
                email: '',
                password: '',

                triggerError(errObj) {
                    this.errorTitle = errObj.title;
                    this.errorMessage = errObj.message;
                    this.errorType = errObj.type;
                    this.isShaking = true;
                    setTimeout(() => { this.isShaking = false; }, 400);
                },

                clearError() {
                    this.errorMessage = null;
                    this.errorTitle = null;
                    this.errorType = null;
                },

                clearFieldErrors() {
                    if (this.errorType === 'wrong-password' || this.errorType === 'invalid-email' || this.errorType === 'user-not-found') {
                        this.errorType = null;
                    }
                },

                async signInWithGoogle() {
                    this.clearError();
                    this.loading = true;
                    try {
                        const provider = new firebase.auth.GoogleAuthProvider();
                        const result = await fbAuth.signInWithPopup(provider);
                        const idToken = await result.user.getIdToken();
                        submitFirebaseToken(idToken);
                    } catch (error) {
                        const parsed = parseAuthError(error);
                        this.triggerError(parsed);
                        this.loading = false;
                    }
                },

                async signInWithEmail() {
                    this.clearError();
                    this.loading = true;
                    try {
                        const result = await fbAuth.signInWithEmailAndPassword(this.email, this.password);
                        const idToken = await result.user.getIdToken();
                        submitFirebaseToken(idToken);
                    } catch (error) {
                        const parsed = parseAuthError(error);
                        this.triggerError(parsed);
                        this.loading = false;
                    }
                },
            }));
        });
    </script>
</body>
</html>
