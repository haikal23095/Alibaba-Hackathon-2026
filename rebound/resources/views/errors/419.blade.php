<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Sesi Kedaluwarsa | REBOUND</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Instrument Sans"', 'sans-serif'] },
                    colors: { brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' } }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 font-sans antialiased text-slate-800">
    <div class="max-w-md w-full text-center space-y-5 bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
        <div class="w-12 h-12 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center text-xl mx-auto border border-brand-100">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Error 419</span>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Sesi Anda Telah Berakhir</h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Token keamanan CSRF Anda telah kedaluwarsa karena tidak ada aktivitas. Silakan segarkan halaman untuk melanjutkan.
            </p>
        </div>
        <div class="pt-2 flex justify-center">
            <a href="/login" class="py-2 px-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-lg transition flex items-center gap-1.5">
                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                <span>Masuk Kembali</span>
            </a>
        </div>
    </div>
</body>
</html>
