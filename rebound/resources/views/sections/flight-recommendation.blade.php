<!-- Flight Recommendation Card (Compact & Balanced, Figma Node 21:894) -->
<div class="w-full bg-white rounded-2xl border border-slate-200/90 p-4 shadow-card transition-all hover:border-brand-300 my-3 text-left">
    
    <!-- Top Header: Badge & Status -->
    <div class="flex items-center justify-between gap-2 mb-3">
        <span class="inline-block px-2.5 py-0.5 bg-blue-50 border border-blue-100 text-brand-600 text-[10px] font-bold tracking-wide rounded-md uppercase"
              x-text="lang === 'id' ? 'DIREKOMENDASIKAN' : 'RECOMMENDED'"></span>
        
        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-semibold rounded-md"
              x-text="lang === 'id' ? flight.alternative.departureCountdownId : flight.alternative.departureCountdownEn"></span>
    </div>

    <!-- Airline Title -->
    <div class="flex items-center gap-2.5 mb-3.5">
        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0">
            <i class="fa-solid fa-plane-departure text-xs"></i>
        </div>
        <div>
            <h4 class="font-bold text-slate-900 text-sm" x-text="flight.alternative.airline"></h4>
            <p class="text-[11px] text-slate-500 font-medium"
               x-text="flight.alternative.flightNumber + ' • ' + flight.alternative.fromCode + ' → ' + flight.alternative.toCode"></p>
        </div>
    </div>

    <!-- Flight Times Timeline (Compact) -->
    <div class="py-2.5 px-3 bg-slate-50 rounded-xl border border-slate-100 mb-3 flex items-center justify-between">
        <!-- Departure City -->
        <div class="text-left">
            <div class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight" x-text="flight.alternative.depTime"></div>
            <div class="text-[10.5px] text-slate-500 font-medium"
                 x-text="flight.alternative.fromCity + ' (' + flight.alternative.fromCode + ')'"></div>
        </div>

        <!-- Transit & Duration Line -->
        <div class="flex-1 px-3 flex flex-col items-center">
            <span class="text-[10px] font-bold text-emerald-600 mb-0.5"
                  x-text="lang === 'id' ? 'Tanpa transit' : 'Direct / Non-stop'"></span>
            
            <div class="w-full flex items-center gap-1">
                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
                <div class="w-4 h-4 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[8px]">
                    <i class="fa-solid fa-plane"></i>
                </div>
                <div class="h-0.5 flex-1 bg-slate-300 rounded"></div>
            </div>

            <span class="text-[10px] text-slate-500 font-medium mt-0.5"
                  x-text="lang === 'id' ? flight.alternative.duration : flight.alternative.durationEn"></span>
        </div>

        <!-- Arrival City -->
        <div class="text-right">
            <div class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight" x-text="flight.alternative.arrTime"></div>
            <div class="text-[10.5px] text-slate-500 font-medium"
                 x-text="(lang === 'id' ? flight.alternative.toCity : flight.alternative.toCityEn) + ' (' + flight.alternative.toCode + ')'"></div>
        </div>
    </div>

    <!-- Waiver & Entitlement Info -->
    <div class="border-t border-slate-100 pt-2.5 mb-3">
        <p class="text-[11px] text-slate-600 mb-2 leading-relaxed"
           x-text="lang === 'id' ? 'Tiket Anda memenuhi syarat untuk perubahan jadwal akibat gangguan penerbangan.' : 'Your ticket is eligible for complimentary rebooking due to flight disruption.'"></p>
        
        <div class="space-y-1 text-[11px] font-semibold text-emerald-700">
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-circle-check text-emerald-600 text-xs"></i>
                <span x-text="lang === 'id' ? 'Ditanggung maskapai' : 'Covered by airline'"></span>
            </div>
            <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-circle-check text-emerald-600 text-xs"></i>
                <span x-text="lang === 'id' ? 'Tanpa biaya tambahan' : 'No additional fees'"></span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center gap-2 pt-0.5">
        <button @click="rebookFlight()"
                class="w-full sm:flex-1 py-2.5 px-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-xl shadow-xs transition hover:shadow flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-plane-circle-check"></i>
            <span x-text="lang === 'id' ? 'Pindah ke GA830' : 'Switch to GA830'"></span>
        </button>

        <button @click="sendMessage(lang === 'id' ? 'Tampilkan opsi penerbangan lainnya' : 'Show other flight options')"
                class="w-full sm:flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition text-center">
            <span x-text="lang === 'id' ? 'Lihat opsi lainnya' : 'View other options'"></span>
        </button>
    </div>
</div>
