{{-- #BACKEND Kartu Analisis Gangguan & Progres Solusi / Disruption Analysis Progress Card
     id: Komponen loading progress saat AI Agent sedang memproses aturan tarif & mencari jadwal alternatif di GDS.
     en: Loading progress card displayed while AI Agent is processing tariff rules & querying alternative schedules in GDS. --}}
<!-- In-Chat Disruption Analysis Progress Card (Figma Node 15:777) -->
<div class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200/80 space-y-2.5">
    {{-- id: Label status dengan spinner animasi
         en: Status label with animated spinner --}}
    <!-- Spinner & Label -->
    <div class="flex items-center gap-2 text-xs font-semibold text-brand-600">
        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
        <span x-text="lang === 'id' ? 'Memeriksa solusi terbaik...' : 'Finding best flight solutions...'"></span>
    </div>

    {{-- id: Progress bar animasi analisis AI
         en: Animated progress bar for AI analysis --}}
    <!-- Animated Progress Bar -->
    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-brand-600 rounded-full transition-all duration-700 w-3/4 animate-pulse"></div>
    </div>
</div>
