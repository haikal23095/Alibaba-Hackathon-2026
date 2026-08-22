<!-- My Trips Modal Backdrop & Dialog -->
<div x-show="showMyTripsModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showMyTripsModal = false"
         class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-floating border border-slate-100 space-y-5"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900"
                        x-text="lang === 'id' ? 'Perjalanan Saya' : 'My Trips'"></h3>
                    <p class="text-xs text-slate-500"
                       x-text="lang === 'id' ? 'Kelola dan pantau seluruh penerbangan aktif Anda' : 'Manage and monitor all your active bookings'"></p>
                </div>
            </div>

            <button @click="showMyTripsModal = false"
                    class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Trip Cards List -->
        <div class="space-y-3">
            <!-- Active Trip Item -->
            <div @click="showMyTripsModal = false; setStatus('delayed');"
                 class="p-4 rounded-2xl border-2 border-brand-500 bg-blue-50/30 hover:bg-blue-50/60 cursor-pointer transition flex items-center justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-900">CGK → SIN</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">Terlambat +4j</span>
                    </div>
                    <p class="text-xs text-slate-500">Garuda Indonesia (GA826) • 30 Nov 2026</p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-brand-600"></i>
            </div>

            <!-- Future Trip Item -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition flex items-center justify-between opacity-80">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-900">SIN → HND</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold">Tepat Waktu</span>
                    </div>
                    <p class="text-xs text-slate-500">Singapore Airlines (SQ638) • 05 Des 2026</p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
            </div>
        </div>

        <!-- Close button -->
        <div class="pt-2">
            <button @click="showMyTripsModal = false"
                    class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                <span x-text="lang === 'id' ? 'Tutup' : 'Close'"></span>
            </button>
        </div>
    </div>
</div>
