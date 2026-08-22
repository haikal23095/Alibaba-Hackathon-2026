<!-- Smartphone Bottom Navigation Bar (Muncul Hanya di Layar HP / Mobile) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-2 py-1.5 flex items-center justify-around shadow-floating select-none">
    
    <!-- Tab 1: Assistant / AI Chat -->
    <button @click="mobileTab = 'assistant'"
            :class="mobileTab === 'assistant' ? 'text-brand-600 font-bold' : 'text-slate-500 font-medium'"
            class="flex flex-col items-center justify-center py-1 px-3 rounded-xl transition relative">
        <div class="relative">
            <i class="fa-solid fa-wand-magic-sparkles text-base mb-0.5"></i>
            <span x-show="flightStatus === 'delayed'" class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-amber-500"></span>
        </div>
        <span class="text-[10px] tracking-tight" x-text="lang === 'id' ? 'Asisten AI' : 'Assistant'"></span>
        <span x-show="mobileTab === 'assistant'" class="w-4 h-0.5 bg-brand-600 rounded-full mt-0.5"></span>
    </button>

    <!-- Tab 2: Flight Status & Policy Details -->
    <button @click="mobileTab = 'details'"
            :class="mobileTab === 'details' ? 'text-brand-600 font-bold' : 'text-slate-500 font-medium'"
            class="flex flex-col items-center justify-center py-1 px-3 rounded-xl transition">
        <i class="fa-solid fa-circle-info text-base mb-0.5"></i>
        <span class="text-[10px] tracking-tight" x-text="lang === 'id' ? 'Status & Info' : 'Status & Info'"></span>
        <span x-show="mobileTab === 'details'" class="w-4 h-0.5 bg-brand-600 rounded-full mt-0.5"></span>
    </button>

    <!-- Tab 3: Tiket PNR List -->
    <button @click="mobileTab = 'tickets'"
            :class="mobileTab === 'tickets' ? 'text-brand-600 font-bold' : 'text-slate-500 font-medium'"
            class="flex flex-col items-center justify-center py-1 px-3 rounded-xl transition">
        <i class="fa-solid fa-ticket text-base mb-0.5"></i>
        <span class="text-[10px] tracking-tight" x-text="lang === 'id' ? 'Tiket PNR' : 'PNR Tickets'"></span>
        <span x-show="mobileTab === 'tickets'" class="w-4 h-0.5 bg-brand-600 rounded-full mt-0.5"></span>
    </button>

    <!-- Tab 4: My Trips -->
    <button @click="showMyTripsModal = true"
            class="flex flex-col items-center justify-center py-1 px-3 rounded-xl text-slate-500 font-medium hover:text-slate-900 transition">
        <i class="fa-solid fa-plane text-base mb-0.5"></i>
        <span class="text-[10px] tracking-tight" x-text="lang === 'id' ? 'Perjalanan' : 'My Trips'"></span>
    </button>

</nav>
