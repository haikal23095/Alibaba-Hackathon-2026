<!-- Active Trip Status Card (Compact & Balanced, Figma Nodes 3:142, 14:693, 14:729, 14:656) -->
<div class="w-full max-w-[620px] mx-auto mb-3.5 bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs transition hover:border-slate-300">
    
    <!-- Top Row: Flight Route & Status Badge -->
    <div class="flex items-center justify-between gap-3">
        <!-- Route & Airline Icon -->
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition"
                 :class="flightStatus === 'delayed' ? 'bg-amber-100/80 text-amber-700' : 'bg-blue-100/80 text-brand-600'">
                <i class="fa-solid fa-plane text-xs"
                   :class="flightStatus === 'delayed' ? 'fa-plane-slash' : 'fa-plane'"></i>
            </div>
            
            <div class="flex items-center gap-1.5">
                <span class="text-sm sm:text-base font-bold text-slate-900 tracking-tight"
                      x-text="flight.original.fromCode"></span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-slate-400"></i>
                <span class="text-sm sm:text-base font-bold text-slate-900 tracking-tight"
                      x-text="flight.original.toCode"></span>
            </div>
        </div>

        <!-- Status Badge Dynamic -->
        <div>
            <!-- On Time Badge -->
            <template x-if="flightStatus === 'on-time'">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 border border-emerald-200/70 rounded-full text-[11px] font-bold text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span x-text="lang === 'id' ? 'Tepat Waktu' : 'On Time'"></span>
                </span>
            </template>

            <!-- Delayed Badge (Nodes 14:693, 14:729) -->
            <template x-if="flightStatus === 'delayed'">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 border border-amber-200 rounded-full text-[11px] font-bold text-amber-700">
                    <i class="fa-regular fa-clock text-[10px]"></i>
                    <span x-text="lang === 'id' ? flight.original.statusId : flight.original.statusEn"></span>
                </span>
            </template>

            <!-- Rebooked / Selesai Badge -->
            <template x-if="flightStatus === 'rebooked'">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-blue-50 border border-blue-200 rounded-full text-[11px] font-bold text-brand-700">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span x-text="lang === 'id' ? 'Terjadwal GA830' : 'Rescheduled GA830'"></span>
                </span>
            </template>
        </div>
    </div>

    <!-- Bottom Row: Airline Info & Timing -->
    <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
        <!-- Airline & Flight No -->
        <div class="flex items-center gap-2">
            <span class="font-medium text-slate-800 text-[11px]"
                  x-text="flightStatus === 'rebooked' ? flight.alternative.flightNumber + ' • ' + flight.alternative.airline : flight.original.flightNumber + ' • ' + flight.original.airline"></span>
        </div>

        <!-- Schedule / Delay details -->
        <div class="text-right">
            <!-- Normal Time -->
            <template x-if="flightStatus === 'on-time'">
                <span class="font-semibold text-slate-800 text-[11px]"
                      x-text="lang === 'id' ? flight.original.dateFullId : flight.original.dateFullEn"></span>
            </template>

            <!-- Delayed Time & Reason (Node 14:729, 14:656) -->
            <template x-if="flightStatus === 'delayed'">
                <div>
                    <div class="font-bold text-slate-900 text-[11px]" x-text="flight.original.delayTime"></div>
                    <div class="text-[10px] text-amber-600 font-medium flex items-center justify-end gap-1 mt-0.5">
                        <i class="fa-solid fa-cloud-bolt text-[9px]"></i>
                        <span x-text="lang === 'id' ? 'Penyebab: ' + flight.original.delayCauseId : 'Cause: ' + flight.original.delayCauseEn"></span>
                    </div>
                </div>
            </template>

            <!-- Rebooked Time -->
            <template x-if="flightStatus === 'rebooked'">
                <div>
                    <span class="font-bold text-emerald-700 text-[11px]">30 Nov, 12:40 PM</span>
                    <span class="text-[10px] text-emerald-600 block" x-text="lang === 'id' ? 'Siap Berangkat' : 'Ready for Boarding'"></span>
                </div>
            </template>
        </div>
    </div>
</div>
