<!-- Left Sidebar: Riwayat Tiket (Figma Light Theme Consistency) -->
<aside class="w-full lg:w-64 xl:w-72 bg-white text-slate-800 border-r border-[#E2E8F0] flex flex-col h-full shrink-0 z-30 select-none text-xs shadow-xs pb-16 lg:pb-0">
    
    <!-- Top Action: Add / Monitor New Ticket & Collapse Button -->
    <div class="p-3 border-b border-slate-100 space-y-2">
        <div class="flex items-center gap-1.5">
            <button @click="showAddTicketModal = true"
                    class="flex-1 h-9 px-3 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 shadow-2xs transition cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span x-text="lang === 'id' ? 'Tambah Tiket PNR' : 'Add Ticket PNR'"></span>
            </button>
            <button @click="leftSidebarOpen = false"
                    class="hidden lg:flex w-9 h-9 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition cursor-pointer shrink-0 shadow-2xs"
                    :title="lang === 'id' ? 'Sembunyikan Sidebar' : 'Hide Sidebar'">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
        </div>

        <!-- Search Ticket PNR / Route -->
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass text-[10px] text-slate-400 absolute left-2.5 top-2.5"></i>
            <input type="text" 
                    x-model="ticketSearch"
                    :placeholder="lang === 'id' ? 'Cari tiket...' : 'Search tickets...'"
                    class="w-full h-8 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-lg pl-7 pr-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 transition">
        </div>
    </div>

    <!-- Ticket History List (Clean Enterprise Style) -->
    <div class="flex-1 overflow-y-auto px-2 py-2 space-y-3">
        
        <!-- Category 1: Sedang Dipantau (Active) -->
        <div>
            <div class="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span x-text="lang === 'id' ? 'Aktif Dipantau' : 'Active Monitoring'"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <div class="space-y-1">
                <!-- Ticket 1 (Current Active) -->
                <button @click="selectTicket('GA826')"
                        :class="selectedTicketId === 'GA826' ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                        class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1 shadow-2xs group">
                    <div class="flex items-center justify-between">
                        <span class="font-bold flex items-center gap-1.5 text-slate-900 text-xs">
                            <i class="fa-solid fa-plane-departure text-brand-600 text-[10px]"></i>
                            <span>GA826 • CGK</span>
                            <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                            <span>SIN</span>
                        </span>
                        <span class="text-[9.5px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-200"
                              x-text="lang === 'id' ? '+4j 25m' : '+4h 25m'">
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500">
                        <span class="font-mono">PNR: GA-9821A</span>
                        <span x-text="lang === 'id' ? '30 Nov 2026' : 'Nov 30, 2026'"></span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Category 2: Bulan Ini (This Month) -->
        <div>
            <div class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                 x-text="lang === 'id' ? 'Bulan Ini' : 'This Month'"></div>

            <div class="space-y-1">
                <!-- Ticket 2: SQ951 (From Boarding Pass Photo) -->
                <button @click="selectTicket('SQ951')"
                        :class="selectedTicketId === 'SQ951' ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                        class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1 shadow-2xs group">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-800 text-xs">
                            <i class="fa-solid fa-plane text-brand-600 text-[10px]"></i>
                            <span>SQ951 • CGK</span>
                            <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                            <span>SIN</span>
                        </span>
                        <span class="text-[9.5px] font-medium px-1.5 py-0.2 rounded bg-blue-50 text-brand-700 border border-blue-200">
                            Business
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500">
                        <span class="font-mono">PNR: SQ-951A</span>
                        <span x-text="lang === 'id' ? '15 Okt 2026' : 'Oct 15, 2026'"></span>
                    </div>
                </button>

                <!-- Ticket 3: SQ638 -->
                <button @click="selectTicket('SQ638')"
                        :class="selectedTicketId === 'SQ638' ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                        class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1 shadow-2xs group">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-800 text-xs">
                            <i class="fa-solid fa-plane text-slate-400 text-[10px]"></i>
                            <span>SQ638 • SIN</span>
                            <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                            <span>HND</span>
                        </span>
                        <span class="text-[9.5px] font-medium px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200"
                              x-text="lang === 'id' ? 'Tepat Waktu' : 'On Time'">
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500">
                        <span class="font-mono">PNR: SQ-4109B</span>
                        <span x-text="lang === 'id' ? '05 Des 2026' : 'Dec 05, 2026'"></span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Category 3: Riwayat Sebelumnya (Past Trips) -->
        <div>
            <div class="px-1.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400"
                 x-text="lang === 'id' ? 'Riwayat Sebelumnya' : 'Previous Trips'"></div>

            <div class="space-y-1">
                <!-- Ticket 3 -->
                <button @click="selectTicket('QZ502')"
                        :class="selectedTicketId === 'QZ502' ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-600 border-slate-200/70 opacity-85 hover:opacity-100'"
                        class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-700 text-xs">
                            <i class="fa-solid fa-check text-slate-400 text-[10px]"></i>
                            <span>QZ502 • DPS</span>
                            <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                            <span>SIN</span>
                        </span>
                        <span class="text-[9.5px] text-slate-400" x-text="lang === 'id' ? 'Selesai' : 'Completed'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono">PNR: QZ-1102K</span>
                        <span x-text="lang === 'id' ? '14 Okt 2026' : 'Oct 14, 2026'"></span>
                    </div>
                </button>

                <!-- Ticket 4 -->
                <button @click="selectTicket('JT028')"
                        :class="selectedTicketId === 'JT028' ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-600 border-slate-200/70 opacity-85 hover:opacity-100'"
                        class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="font-medium flex items-center gap-1.5 text-slate-700 text-xs">
                            <i class="fa-solid fa-check text-slate-400 text-[10px]"></i>
                            <span>JT028 • CGK</span>
                            <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                            <span>SUB</span>
                        </span>
                        <span class="text-[9.5px] text-slate-400" x-text="lang === 'id' ? 'Selesai' : 'Completed'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono">PNR: JT-7741R</span>
                        <span x-text="lang === 'id' ? '22 Sep 2026' : 'Sep 22, 2026'"></span>
                    </div>
                </button>
            </div>
        </div>

    </div>

    <!-- Bottom User Bar (Dynamic Current User) -->
    <div class="p-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/80">
        <button @click="showUserDropdown = !showUserDropdown"
                class="flex items-center gap-2 text-left hover:opacity-80 transition flex-1 overflow-hidden mr-1 cursor-pointer">
            <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0"
                 x-text="currentUser.initials">
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-semibold text-slate-900 truncate" x-text="currentUser.name"></div>
                <div class="text-[10px] text-slate-500 truncate" x-text="currentUser.email"></div>
            </div>
        </button>

        <button @click="showUserDropdown = !showUserDropdown"
                class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-200/60 transition cursor-pointer"
                :title="lang === 'id' ? 'Ganti Akun' : 'Switch Account'">
            <i class="fa-solid fa-chevron-up text-[10px]"></i>
        </button>
    </div>
</aside>
