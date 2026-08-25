<!-- Multi-Flight Alternative Options Modal (GDS Atlas Airline Network) -->
<div x-show="showFlightOptionsModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showFlightOptionsModal = false"
         class="bg-white rounded-xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-slate-200 text-left space-y-4 relative overflow-hidden"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-900"
                        x-text="lang === 'id' ? 'Pilihan Penerbangan Alternatif (GDS Atlas)' : 'Alternative Flight Schedules (GDS Atlas)'"></h3>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5"
                   x-text="lang === 'id' ? 'Pilih jadwal pengganti bebas biaya (Waiver 72A terverifikasi)' : 'Select alternative schedule with $0 fee waiver'"></p>
            </div>

            <button @click="showFlightOptionsModal = false" class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Flight Options List -->
        <div class="space-y-2.5 max-h-[60vh] overflow-y-auto pr-1 text-xs custom-scrollbar">
            
            <template x-for="(altFlight, idx) in alternativeFlightsList" :key="idx">
                <div class="p-3 rounded-lg border transition hover:border-brand-400 hover:shadow-xs bg-white space-y-2"
                     :class="altFlight.isRecommended ? 'border-brand-300 bg-blue-50/20' : 'border-slate-200'">
                    
                    <!-- Airline & Badges -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs font-bold shrink-0"
                                 :class="altFlight.isRecommended ? 'bg-brand-600 text-white' : 'bg-slate-800 text-white'">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span x-text="altFlight.airline"></span>
                                    <span class="font-mono text-[10px] text-slate-500" x-text="'(' + altFlight.flightNumber + ')'"></span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium" x-text="altFlight.aircraft + ' • Gate ' + altFlight.gate"></div>
                            </div>
                        </div>

                        <div>
                            <span x-show="altFlight.isRecommended" 
                                  class="px-2 py-0.5 bg-blue-100 text-brand-700 text-[9.5px] font-bold uppercase rounded"
                                  x-text="lang === 'id' ? 'Rekomendasi' : 'Recommended'"></span>
                            <span x-show="!altFlight.isRecommended" 
                                  class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9.5px] font-bold rounded"
                                  x-text="altFlight.seatsAvailable + ' ' + (lang === 'id' ? 'Kursi' : 'Seats')"></span>
                        </div>
                    </div>

                    <!-- Schedule Route Line -->
                    <div class="p-2 bg-slate-50 rounded-md border border-slate-100 flex items-center justify-between">
                        <div class="text-left">
                            <div class="text-sm font-bold text-slate-900" x-text="altFlight.depTime"></div>
                            <div class="text-[10px] text-slate-500">Jakarta (CGK)</div>
                        </div>

                        <div class="flex-1 px-3 flex flex-col items-center">
                            <span class="text-[9px] font-semibold text-emerald-600 mb-0.5"
                                  x-text="lang === 'id' ? 'Langsung (' + altFlight.duration + ')' : 'Direct (' + altFlight.duration + ')'"></span>
                            <div class="w-full flex items-center gap-1">
                                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
                                <i class="fa-solid fa-plane text-[7px] text-slate-400"></i>
                                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-900" x-text="altFlight.arrTime"></div>
                            <div class="text-[10px] text-slate-500" x-text="lang === 'id' ? 'Singapura (SIN)' : 'Singapore (SIN)'"></div>
                        </div>
                    </div>

                    <!-- Rebook CTA -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="text-[10.5px] text-emerald-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-xs text-emerald-600"></i>
                            <span x-text="lang === 'id' ? 'Biaya Tambahan Rp 0 (Waiver 72A)' : '$0 Fee (Waiver 72A)'"></span>
                        </div>

                        <button @click="selectCustomAlternative(altFlight); showFlightOptionsModal = false"
                                class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1 shadow-2xs">
                            <span x-text="lang === 'id' ? 'Pilih Penerbangan Ini' : 'Select Flight'"></span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>

                </div>
            </template>

        </div>

        <!-- Footer -->
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-[10.5px] text-slate-400">
                <i class="fa-solid fa-database text-slate-400 mr-1"></i>
                <span x-text="lang === 'id' ? 'Sinkronisasi GDS Atlas: 4 Penerbangan Aktif' : 'GDS Atlas Synced: 4 Flights Active'"></span>
            </span>
            <button @click="showFlightOptionsModal = false" class="text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer" x-text="lang === 'id' ? 'Tutup' : 'Close'"></button>
        </div>

    </div>
</div>
