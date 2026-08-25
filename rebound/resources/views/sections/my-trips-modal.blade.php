{{-- #BACKEND Modal Daftar Perjalanan Saya / My Trips Modal
     id: Menampilkan daftar seluruh tiket & perjalanan yang dimiliki oleh user yang sedang login.
         Di backend: query tabel `bookings` WHERE `user_id` = Auth::id() ORDER BY `departure_date` DESC.
     en: Displays all tickets & trips owned by the currently authenticated user.
         In backend: query `bookings` table WHERE `user_id` = Auth::id() ORDER BY `departure_date` DESC. --}}
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
         class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        {{-- id: Header Modal Perjalanan Saya
             en: My Trips Modal Header --}}
        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900"
                        x-text="lang === 'id' ? 'Perjalanan Saya' : 'My Trips'"></h3>
                    <p class="text-[11px] text-slate-500"
                       x-text="lang === 'id' ? 'Daftar tiket penerbangan aktif' : 'Active flight tickets'"></p>
                </div>
            </div>

            <button @click="showMyTripsModal = false"
                    class="w-7 h-7 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        {{-- #BACKEND Daftar Tiket Perjalanan User
             id: Item tiket di bawah saat ini hardcoded. Di backend: loop dari collection $bookings.
             en: Ticket items below are currently hardcoded. In backend: loop from $bookings collection. --}}
        <!-- Trip Cards List -->
        <div class="space-y-2">
            {{-- id: Tiket Aktif (GA826)
                 en: Active Trip Item (GA826) --}}
            <!-- Active Trip Item -->
            <div @click="showMyTripsModal = false; selectTicket('GA826')"
                 class="p-3 rounded-lg border-2 border-brand-500 bg-blue-50/40 hover:bg-blue-50/70 cursor-pointer transition flex items-center justify-between shadow-2xs">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">CGK → SIN</span>
                        <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded text-[9.5px] font-bold"
                              x-text="lang === 'id' ? 'Terlambat +4j' : 'Delayed +4h'"></span>
                    </div>
                    <p class="text-[11px] text-slate-500">Garuda Indonesia (GA826) • 30 Nov 2026</p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-brand-600"></i>
            </div>

            {{-- id: Tiket Mendatang (SQ638)
                 en: Future Trip Item (SQ638) --}}
            <!-- Future Trip Item -->
            <div @click="showMyTripsModal = false; selectTicket('SQ638')"
                 class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition flex items-center justify-between shadow-2xs">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">SIN → HND</span>
                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded text-[9.5px] font-bold"
                              x-text="lang === 'id' ? 'Tepat Waktu' : 'On Time'"></span>
                    </div>
                    <p class="text-[11px] text-slate-500">Singapore Airlines (SQ638) • 05 Des 2026</p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
            </div>
        </div>

        {{-- id: Tombol Aksi (Tambah Tiket Baru & Tutup)
             en: Action Buttons (Add New Ticket & Close) --}}
        <!-- Action buttons -->
        <div class="pt-2 space-y-1.5">
            <button @click="showMyTripsModal = false; hasSetupPnr = false"
                    class="w-full py-2 bg-brand-50 hover:bg-brand-100 text-brand-700 border border-brand-200 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span x-text="lang === 'id' ? 'Tambah Tiket Baru' : 'Add New Ticket'"></span>
            </button>
            <button @click="showMyTripsModal = false"
                    class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition cursor-pointer">
                <span x-text="lang === 'id' ? 'Tutup' : 'Close'"></span>
            </button>
        </div>
    </div>
</div>
