{{-- #BACKEND Kartu Analisis Gangguan & Progres Solusi / Disruption Analysis Progress Card
     id: Kartu progres dengan siklus hidup terbatas — mengisi 0→100% lalu berubah menjadi status
         "selesai" agar tidak memberi kesan loading tanpa akhir. State lokal x-data (started/done)
         dipakai karena kartu ikut dirender ulang dari riwayat tersimpan (tipe disruption_alert).
     en: Progress card with a bounded lifecycle — fills 0→100% then switches to a "complete" state
         so it never looks like an endless loader. Local x-data state (started/done) is used because
         the card is also re-rendered from stored history (disruption_alert type). --}}
<!-- In-Chat Disruption Analysis Progress Card (Figma Node 15:777) -->
<div x-data="{ started: false, done: false }"
    x-init="setTimeout(() => started = true, 150); setTimeout(() => done = true, 3400)"
    class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200/80 space-y-2.5">
    {{-- id: Label status — spinner saat menganalisis, berubah centang hijau saat selesai
         en: Status label — spinner while analyzing, switches to a green check when complete --}}
    <!-- Spinner & Label -->
    <div class="flex items-center gap-2 text-xs font-semibold" :class="done ? 'text-emerald-600' : 'text-brand-600'">
        <i class="fa-solid text-xs" :class="done ? 'fa-circle-check' : 'fa-circle-notch fa-spin'"></i>
        <span x-text="done
            ? (lang === 'id' ? 'Analisis gangguan selesai.' : 'Disruption analysis complete.')
            : (lang === 'id' ? 'Memeriksa solusi terbaik...' : 'Finding best flight solutions...')"></span>
    </div>

    {{-- id: Progress bar animasi analisis AI — mengisi bertahap lalu penuh saat selesai
         en: Animated progress bar for AI analysis — fills progressively then completes --}}
    <!-- Animated Progress Bar -->
    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all ease-out"
            :class="done
                ? 'bg-emerald-500 w-full duration-500'
                : (started ? 'bg-brand-600 w-[85%] duration-[3000ms]' : 'bg-brand-600 w-[8%] duration-300')"></div>
    </div>
</div>
