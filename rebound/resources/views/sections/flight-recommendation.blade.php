<!-- Flight Recommendation Card (Compact & Balanced, Figma Node 21:894) -->
<div class="w-full bg-white rounded-lg border border-slate-200 p-2.5 sm:p-3 shadow-xs transition hover:border-brand-300 my-2 text-left">
    
    <!-- Top Header: Badge & Status -->
    <div class="flex items-center justify-between gap-1.5 mb-2">
        <span class="inline-block px-1.5 py-0.5 bg-blue-50 border border-blue-100 text-brand-600 text-[9.5px] font-bold tracking-wide rounded uppercase"
              x-text="lang === 'id' ? 'DIREKOMENDASIKAN' : 'RECOMMENDED'"></span>
        
        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-semibold rounded"
              x-text="lang === 'id' ? flight.alternative.departureCountdownId : flight.alternative.departureCountdownEn"></span>
    </div>

    <!-- Airline Title -->
    <div class="flex items-center gap-2 mb-2">
        <div class="w-6 h-6 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-[10px] shrink-0">
            <i class="fa-solid fa-plane-departure text-[10px]"></i>
        </div>
        <div>
            <h4 class="font-bold text-slate-900 text-xs" x-text="flight.alternative.airline"></h4>
            <p class="text-[10px] text-slate-500 font-medium"
               x-text="flight.alternative.flightNumber + ' • ' + flight.alternative.fromCode + ' → ' + flight.alternative.toCode"></p>
        </div>
    </div>

    <!-- Flight Times Timeline (Compact) -->
    <div class="py-1.5 px-2 bg-slate-50 rounded-md border border-slate-100 mb-2 flex items-center justify-between">
        <!-- Departure City -->
        <div class="text-left">
            <div class="text-sm sm:text-base font-bold text-slate-900 tracking-tight" x-text="flight.alternative.depTime"></div>
            <div class="text-[9.5px] text-slate-500 font-medium"
                 x-text="flight.alternative.fromCity + ' (' + flight.alternative.fromCode + ')'"></div>
        </div>

        <!-- Transit & Duration Line -->
        <div class="flex-1 px-2 flex flex-col items-center">
            <span class="text-[9px] font-bold text-emerald-600 mb-0.5"
                  x-text="lang === 'id' ? 'Langsung' : 'Direct'"></span>
            
            <div class="w-full flex items-center gap-1">
                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
                <div class="w-3 h-3 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[6px]">
                    <i class="fa-solid fa-plane"></i>
                </div>
                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
            </div>

            <span class="text-[9px] text-slate-500 font-medium mt-0.5"
                  x-text="lang === 'id' ? flight.alternative.duration : flight.alternative.durationEn"></span>
        </div>

        <!-- Arrival City -->
        <div class="text-right">
            <div class="text-sm sm:text-base font-bold text-slate-900 tracking-tight" x-text="flight.alternative.arrTime"></div>
            <div class="text-[9.5px] text-slate-500 font-medium"
                 x-text="(lang === 'id' ? flight.alternative.toCity : flight.alternative.toCityEn) + ' (' + flight.alternative.toCode + ')'"></div>
        </div>
    </div>

    <!-- Waiver & Entitlement Info -->
    <div class="border-t border-slate-100 pt-1.5 mb-2">
        <p class="text-[10px] text-slate-600 mb-1 leading-relaxed"
           x-text="lang === 'id' ? 'Tiket Anda memenuhi syarat pengalihan jadwal bebas biaya.' : 'Your ticket is eligible for complimentary rebooking.'"></p>
        
        <div class="space-y-0.5 text-[10px] font-semibold text-emerald-700">
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-circle-check text-emerald-600 text-[10px]"></i>
                <span x-text="lang === 'id' ? 'Ditanggung maskapai (Waiver 72A)' : 'Covered by airline (Waiver 72A)'"></span>
            </div>
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-circle-check text-emerald-600 text-[10px]"></i>
                <span x-text="lang === 'id' ? 'Biaya tambahan Rp 0' : 'No additional fees ($0)'"></span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center gap-1.5 pt-0.5">
        <button @click="rebookFlight()"
                class="w-full sm:flex-1 py-1.5 px-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-[11px] rounded-md shadow-2xs transition flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-plane-circle-check text-[10px]"></i>
            <span x-text="lang === 'id' ? 'Pindah ke GA830' : 'Switch to GA830'"></span>
        </button>

        <button @click="showFlightOptionsModal = true"
                class="w-full sm:flex-1 py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] rounded-md transition text-center cursor-pointer">
            <span x-text="lang === 'id' ? 'Lihat opsi lainnya' : 'View other options'"></span>
        </button>
    </div>
</div>
