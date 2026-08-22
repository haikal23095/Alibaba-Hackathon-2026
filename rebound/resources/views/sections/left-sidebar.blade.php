<!-- Left Sidebar: Riwayat Tiket (Figma Light Theme Consistency) -->
<aside :class="leftSidebarOpen ? 'block' : 'hidden lg:block'"
       class="w-full lg:w-64 xl:w-72 bg-white text-slate-800 border-r border-[#E2E8F0] flex flex-col h-full shrink-0 z-30 select-none text-xs shadow-xs pb-16 lg:pb-0">
    
    <!-- Top Action: Add / Monitor New Ticket -->
    <div class="p-3 border-b border-slate-100 space-y-2.5">
        <button @click="showAddTicketModal = true"
                class="w-full h-10 px-3.5 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white rounded-xl text-xs sm:text-[13px] font-bold flex items-center justify-between shadow-xs transition hover:shadow cursor-pointer">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span x-text="lang === 'id' ? 'Pantau Tiket Baru' : 'Monitor New Ticket'"></span>
            </span>
            <span class="text-[10px] font-bold bg-white/20 border border-white/25 px-2 py-0.5 rounded-md text-white font-mono tracking-wider">PNR</span>
        </button>

        <!-- Search Ticket PNR / Route -->
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass text-[11px] text-slate-400 absolute left-3 top-3"></i>
            <input type="text" 
                    x-model="ticketSearch"
                    :placeholder="lang === 'id' ? 'Cari PNR / Rute / Maskapai...' : 'Search PNR / Route...'"
                    class="w-full h-9 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-xl pl-8 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 transition">
        </div>
    </div>

    <!-- Ticket History List (Figma Clean Light Style) -->
    <div class="flex-1 overflow-y-auto px-2.5 py-2.5 space-y-3.5">
        
        <!-- Category 1: Sedang Dipantau (Active) -->
        <div>
            <div class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span x-text="lang === 'id' ? 'Aktif Dipantau' : 'Active Monitoring'"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <div class="space-y-1">
                <!-- Ticket 1 (Current Active) -->
                <button @click="selectTicket('GA826')"
                        :class="selectedTicketId === 'GA826' ? 'bg-blue-50/80 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                        class="w-full text-left p-2.5 rounded-xl border transition flex flex-col gap-1 shadow-xs group">
                    <div class="flex items-center justify-between">
                        <span class="font-bold flex items-center gap-1.5 text-slate-900 text-xs">
                            <i class="fa-solid fa-plane-departure text-brand-600 text-[11px]"></i>
                            <span>GA826 • CGK ➔ SIN</span>
                        </span>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-200">
                            +4j 25m
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10.5px] text-slate-500">
                        <span class="font-mono">PNR: GA-9821A</span>
                        <span>30 Nov 2026</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Category 2: Bulan Ini (This Month) -->
        <div>
            <div class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                 x-text="lang === 'id' ? 'Bulan Ini' : 'This Month'"></div>

            <div class="space-y-1">
                <!-- Ticket 2 -->
                <button @click="selectTicket('SQ638')"
                        :class="selectedTicketId === 'SQ638' ? 'bg-blue-50/80 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                        class="w-full text-left p-2.5 rounded-xl border transition flex flex-col gap-1 shadow-xs group">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-800 text-xs">
                            <i class="fa-solid fa-plane text-slate-400 text-[11px]"></i>
                            <span>SQ638 • SIN ➔ HND</span>
                        </span>
                        <span class="text-[10px] font-medium px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            On Time
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10.5px] text-slate-500">
                        <span class="font-mono">PNR: SQ-4109B</span>
                        <span>05 Des 2026</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Category 3: Riwayat Sebelumnya (Past Trips) -->
        <div>
            <div class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                 x-text="lang === 'id' ? 'Riwayat Sebelumnya' : 'Previous Trips'"></div>

            <div class="space-y-1">
                <!-- Ticket 3 -->
                <button @click="selectTicket('QZ502')"
                        :class="selectedTicketId === 'QZ502' ? 'bg-blue-50/80 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-600 border-slate-200/70 opacity-85 hover:opacity-100'"
                        class="w-full text-left p-2.5 rounded-xl border transition flex flex-col gap-1 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-700 text-xs">
                            <i class="fa-solid fa-check text-slate-400 text-[10px]"></i>
                            <span>QZ502 • DPS ➔ SIN</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Selesai</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono">PNR: QZ-1102K</span>
                        <span>14 Okt 2026</span>
                    </div>
                </button>

                <!-- Ticket 4 -->
                <button @click="selectTicket('JT028')"
                        :class="selectedTicketId === 'JT028' ? 'bg-blue-50/80 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-600 border-slate-200/70 opacity-85 hover:opacity-100'"
                        class="w-full text-left p-2.5 rounded-xl border transition flex flex-col gap-1 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-700 text-xs">
                            <i class="fa-solid fa-check text-slate-400 text-[10px]"></i>
                            <span>JT028 • CGK ➔ SUB</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Selesai</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono">PNR: JT-7741R</span>
                        <span>22 Sep 2026</span>
                    </div>
                </button>
            </div>
        </div>

    </div>

    <!-- Bottom User Bar (Dynamic Current User) -->
    <div class="p-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/80">
        <button @click="showUserDropdown = !showUserDropdown"
                class="flex items-center gap-2 text-left hover:opacity-80 transition flex-1 overflow-hidden mr-1">
            <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0"
                 x-text="currentUser.initials">
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-semibold text-slate-900 truncate" x-text="currentUser.name"></div>
                <div class="text-[10px] text-slate-500 truncate" x-text="currentUser.email"></div>
            </div>
        </button>

        <button @click="showUserDropdown = !showUserDropdown"
                class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-200/60 transition"
                title="Ganti Akun">
            <i class="fa-solid fa-chevron-up text-[10px]"></i>
        </button>
    </div>
</aside>
