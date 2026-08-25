<!-- Flight Reschedule Success Card (Figma Node 25:1210) -->
<div class="w-full bg-white rounded-lg border border-emerald-200 p-4 shadow-2xs my-3 text-left">
    <!-- Top Status Header -->
    <div class="flex items-center justify-between gap-3 mb-1.5">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-sm"
                x-text="lang === 'id' ? 'Penerbangan Berhasil Diubah' : 'Flight Successfully Rebooked'"></h4>
        </div>

        <span class="inline-block px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold text-[10px] rounded-md"
              x-text="lang === 'id' ? 'Selesai' : 'Completed'"></span>
    </div>

    <!-- Confirmation Description -->
    <p class="text-xs text-slate-600 ml-8 mb-3"
       x-text="lang === 'id' ? 'Semuanya sudah siap. Tiket baru Anda telah diterbitkan.' : 'Everything is set. Your new e-ticket has been issued.'"></p>

    <!-- Bottom Monitoring Note -->
    <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-[11px] font-medium text-slate-600 ml-0.5">
        <i class="fa-solid fa-plane-departure text-slate-400"></i>
        <span x-text="lang === 'id' ? 'REBOUND akan terus memantau penerbangan ' + flight.alternative.flightNumber + ' Anda.' : 'REBOUND will continue monitoring your ' + flight.alternative.flightNumber + ' flight.'"></span>
    </div>
</div>
