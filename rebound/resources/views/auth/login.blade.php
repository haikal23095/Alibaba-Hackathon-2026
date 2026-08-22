<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - REBOUND Flight Assistant</title>

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
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-[#F8FAFC] flex flex-col justify-center py-10 sm:px-6 lg:px-8"
      x-data="loginPage">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo & Badge -->
        <a href="/" class="inline-flex items-center gap-2 mb-3 hover:opacity-90 transition">
            <span class="text-2xl font-black tracking-tight text-[#0F172A]">REBOUND</span>
        </a>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
            Masuk ke Akun Anda
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Pantau penerbangan, aturan tiket PNR & rekomendasi rebooking AI.
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-7 px-5 sm:px-8 shadow-sm border border-slate-200/90 rounded-3xl space-y-5">

            <!-- Alert info/error -->
            @if(session('info'))
                <div class="p-3 bg-blue-50 border border-blue-200 text-brand-700 text-xs rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Error dari Firebase JS SDK (ditampilkan via Alpine) -->
            <div x-show="firebaseError" x-cloak
                 class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <span x-text="firebaseError"></span>
            </div>

            <!-- Peringatan jika konfigurasi Firebase belum diisi -->
            @unless(config('firebase_web.configured'))
                <div class="p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>Konfigurasi Firebase belum lengkap. Isi <code>FIREBASE_WEB_*</code> di file <code>.env</code> agar login Google dapat digunakan.</span>
                </div>
            @endunless

            <!-- 1. Google Sign-In Primary Button (Firebase Auth) -->
            <div>
                <button type="button"
                        @click="signInWithGoogle()"
                        :disabled="loading || !firebaseConfigured"
                        class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition hover:shadow flex items-center justify-center gap-3 group disabled:opacity-60 disabled:cursor-not-allowed">
                    <!-- Official Google SVG Logo -->
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span x-show="!loading">Masuk dengan Google</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                    </span>
                </button>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-3 text-[11px] font-semibold text-slate-400">
                        atau dengan email
                    </span>
                </div>
            </div>

            <!-- Email/Password Form (juga diverifikasi via Firebase Auth) -->
            <form @submit.prevent="signInWithEmail()" class="space-y-3.5">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope text-slate-400 text-xs absolute left-3 top-3"></i>
                        <input id="email" x-model="email" name="email" type="email" autocomplete="email" required
                               placeholder="nama@email.com"
                               class="w-full bg-slate-50 focus:bg-white border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-500 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock text-slate-400 text-xs absolute left-3 top-3"></i>
                        <input id="password" x-model="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="w-full bg-slate-50 focus:bg-white border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-500 transition">
                    </div>
                </div>

                <button type="submit"
                        :disabled="loading || !firebaseConfigured"
                        class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition hover:shadow flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span x-show="!loading">Masuk ke Dashboard</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                    </span>
                </button>
            </form>

            <!-- Bottom Register Link -->
            <div class="text-center pt-2 text-xs text-slate-600 border-t border-slate-100">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700">Daftar sekarang</a>
            </div>

        </div>
    </div>

    <!-- Firebase JS SDK (compat) -->
    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
    @php
        $firebaseWebConfig = [
            'apiKey' => config('firebase_web.api_key'),
            'authDomain' => config('firebase_web.auth_domain'),
            'projectId' => config('firebase_web.project_id'),
            'storageBucket' => config('firebase_web.storage_bucket'),
            'messagingSenderId' => config('firebase_web.messaging_sender_id'),
            'appId' => config('firebase_web.app_id'),
        ];
    @endphp
    <script>
        // Konfigurasi Firebase Web App diambil dari config/firebase_web.php (.env)
        const firebaseConfig = @json($firebaseWebConfig);
        const firebaseConfigured = @json(config('firebase_web.configured'));
        const firebaseLoginUrl = @json(route('auth.firebase'));
        const csrfToken = @json(csrf_token());

        if (firebaseConfigured) {
            firebase.initializeApp(firebaseConfig);
        }
        const fbAuth = firebaseConfigured ? firebase.auth() : null;

        // Terjemahan error Firebase Auth ke Bahasa Indonesia
        function firebaseErrorMessage(error) {
            const messages = {
                'auth/popup-closed-by-user': 'Jendela login Google ditutup sebelum selesai.',
                'auth/popup-blocked': 'Popup diblokir browser. Izinkan popup lalu coba lagi.',
                'auth/invalid-credential': 'Email atau kata sandi salah.',
                'auth/user-not-found': 'Akun tidak ditemukan. Silakan daftar terlebih dahulu.',
                'auth/wrong-password': 'Email atau kata sandi salah.',
                'auth/too-many-requests': 'Terlalu banyak percobaan. Coba lagi beberapa saat.',
                'auth/network-request-failed': 'Koneksi internet bermasalah. Periksa jaringan Anda.',
            };
            return messages[error?.code] || ('Login gagal: ' + (error?.message || 'terjadi kesalahan tak terduga.'));
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
                firebaseError: null,
                email: '',
                password: '',

                async signInWithGoogle() {
                    this.firebaseError = null;
                    this.loading = true;
                    try {
                        const provider = new firebase.auth.GoogleAuthProvider();
                        const result = await fbAuth.signInWithPopup(provider);
                        const idToken = await result.user.getIdToken();
                        submitFirebaseToken(idToken); // halaman akan pindah ke backend
                    } catch (error) {
                        this.firebaseError = firebaseErrorMessage(error);
                        this.loading = false;
                    }
                },

                async signInWithEmail() {
                    this.firebaseError = null;
                    this.loading = true;
                    try {
                        const result = await fbAuth.signInWithEmailAndPassword(this.email, this.password);
                        const idToken = await result.user.getIdToken();
                        submitFirebaseToken(idToken);
                    } catch (error) {
                        this.firebaseError = firebaseErrorMessage(error);
                        this.loading = false;
                    }
                },
            }));
        });
    </script>

</body>
</html>
