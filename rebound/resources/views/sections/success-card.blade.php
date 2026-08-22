<!-- Flight Reschedule Success Card (Figma Node 25:1210) -->
<div class="w-full bg-white rounded-2xl border border-emerald-200/90 p-5 shadow-card my-4 text-left">
    <!-- Top Status Header -->
    <div class="flex items-center justify-between gap-3 mb-2">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-base"
                x-text="lang === 'id' ? 'Penerbangan Berhasil Diubah' : 'Flight Successfully Rebooked'"></h4>
        </div>

        <span class="inline-block px-3 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold text-xs rounded-full"
              x-text="lang === 'id' ? 'Selesai' : 'Completed'"></span>
    </div>

    <!-- Confirmation Description -->
    <p class="text-sm text-slate-600 ml-9 mb-4"
       x-text="lang === 'id' ? 'Semuanya sudah siap. Tiket baru Anda telah diterbitkan.' : 'Everything is set. Your new e-ticket has been issued.'"></p>

    <!-- Bottom Monitoring Note -->
    <div class="pt-3 border-t border-slate-100 flex items-center gap-2.5 text-xs font-medium text-slate-700 ml-1">
        <i class="fa-solid fa-plane-departure text-slate-500"></i>
        <span x-text="lang === 'id' ? 'REBOUND akan terus memantau penerbangan GA830 Anda.' : 'REBOUND will continue monitoring your GA830 flight.'"></span>
    </div>
</div>
