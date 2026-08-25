<!-- In-Chat Disruption Analysis Progress Card (Figma Node 15:777) -->
<div class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200/80 space-y-2.5">
    
    <!-- Spinner & Label -->
    <div class="flex items-center gap-2 text-xs font-semibold text-brand-600">
        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
        <span x-text="lang === 'id' ? 'Memeriksa solusi terbaik...' : 'Finding best flight solutions...'"></span>
    </div>

    <!-- Animated Progress Bar -->
    <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-brand-600 rounded-full transition-all duration-700 w-3/4 animate-pulse"></div>
    </div>

</div>
