{{-- #BACKEND Sidebar Kiri Riwayat & Manajemen Tiket / Left Sidebar: Ticket History & Management
     id: Sidebar kiri menampilkan daftar seluruh tiket penerbangan yang dipantau (aktif, bulan ini, riwayat selesai).
         Di backend: data harus di-query dari tabel `bookings` & `flights` milik pengguna yang sedang login (Auth::id()).
     en: Left sidebar displays list of all tracked flight tickets (active monitoring, this month, past completed).
         In backend: data must be queried from `bookings` & `flights` tables belonging to authenticated user (Auth::id()). --}}
<!-- Left Sidebar: Riwayat Tiket (Figma Light Theme Consistency) -->
<aside class="w-full lg:w-64 xl:w-72 bg-white text-slate-800 border-r border-[#E2E8F0] flex flex-col h-full shrink-0 z-30 select-none text-xs shadow-xs pb-16 lg:pb-0">
    
    {{-- id: Header Aksi Atas: Tombol Tambah Tiket PNR, Collapse Sidebar, & Pencarian Tiket
         en: Top Action Header: Add PNR Ticket Button, Sidebar Collapse, & Ticket Search Input --}}
    <!-- Top Action: Add / Monitor New Ticket & Collapse Button -->
    <div class="p-3 border-b border-slate-100 space-y-2">
        <div class="flex items-center gap-1.5">
            <button @click="showAddTicketModal = true"
                    class="flex-1 h-9 px-3 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 shadow-2xs transition cursor-pointer">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span x-text="lang === 'id' ? 'Tambah Tiket PNR' : 'Add Ticket PNR'"></span>
            </button>
            {{-- id: Tombol tutup khusus mobile — panel tiket kini overlay penuh (fixed), tombol ini
                 mengembalikan user ke tab chat. Di desktop collapse tetap memakai chevron kiri di bawahnya.
                 en: Mobile-only close button — the tickets panel is now a full overlay (fixed); this
                 returns the user to the chat tab. Desktop collapse keeps the chevron-left below. --}}
            <button @click="mobileTab = 'assistant'"
                    class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition cursor-pointer shrink-0 shadow-2xs"
                    :title="lang === 'id' ? 'Kembali ke Chat' : 'Back to Chat'">
                <i class="fa-solid fa-xmark text-xs"></i>
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

    {{-- id: Container Daftar Riwayat Tiket
         en: Ticket History List Container --}}
    {{-- id: Container Daftar Riwayat Chat AI Agent & Tiket (Dinamis dari DB)
         en: AI Agent Chat Session & Ticket History List Container (Dynamic from DB) --}}
    <!-- Ticket & AI Chat History List (Clean Enterprise Style) -->
    <div class="flex-1 overflow-y-auto px-2 py-2 space-y-3">
        
        {{-- id: Tampilan Riwayat Chat Sesi AI Agent dari Database (agent_chat_sessions) --}}
        <template x-if="filteredChatSessions && filteredChatSessions.length > 0">
            <div>
                <div class="px-1.5 pb-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                    <span x-text="lang === 'id' ? 'Riwayat Chat AI Agent' : 'AI Agent Chat Sessions'"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                </div>

                <div class="space-y-1.5">
                    <template x-for="session in filteredChatSessions" :key="session.id">
                        <div @click="selectTicket(session.pnr_code)"
                             :class="selectedTicketId === session.pnr_code ? 'bg-blue-50 text-brand-950 border-brand-300 ring-1 ring-brand-500/20 font-semibold shadow-xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200/80'"
                             class="w-full text-left p-2.5 rounded-lg border transition flex flex-col gap-1.5 shadow-2xs group cursor-pointer relative overflow-hidden">
                            
                            <!-- Header Penerbangan & Rute -->
                            <div class="flex items-center justify-between">
                                <span class="font-bold flex items-center gap-1.5 text-slate-900 text-xs truncate">
                                    <i class="fa-solid fa-plane-departure text-brand-600 text-[10px]" x-show="session.status === 'delayed' || session.status === 'active'"></i>
                                    <i class="fa-solid fa-plane text-slate-400 text-[10px]" x-show="session.status !== 'delayed' && session.status !== 'active'"></i>
                                    <span x-text="(session.flight_number || session.pnr_code) + ' • ' + (session.from_code || 'CGK')"></span>
                                    <i class="fa-solid fa-arrow-right text-[8px] text-slate-400"></i>
                                    <span x-text="session.to_code || 'SIN'"></span>
                                </span>

                                <!-- Status Badge Dinamis -->
                                <template x-if="session.status === 'delayed'">
                                    <span class="text-[9.5px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-200">
                                        +4j 25m
                                    </span>
                                </template>
                                <template x-if="session.status === 'on_time' || session.status === 'active'">
                                    <span class="text-[9.5px] font-medium px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200"
                                          x-text="session.cabin_class === 'Business' ? 'Business' : (lang === 'id' ? 'Tepat Waktu' : 'On Time')">
                                    </span>
                                </template>
                                <template x-if="session.status === 'cancelled'">
                                    <span class="text-[9.5px] font-medium px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 border border-rose-200"
                                          x-text="lang === 'id' ? 'Dibatalkan' : 'Cancelled'">
                                    </span>
                                </template>
                                <template x-if="session.status === 'flown' || session.status === 'completed'">
                                    <span class="text-[9.5px] text-slate-400 font-medium" x-text="lang === 'id' ? 'Selesai' : 'Completed'"></span>
                                </template>
                            </div>

                            <!-- Preview Pesan / Ringkasan Konteks AI Agent -->
                            <div class="flex items-start gap-1.5 bg-slate-50/80 p-1.5 rounded-md border border-slate-100/90 text-[10.5px] text-slate-600 leading-snug">
                                <i class="fa-solid fa-robot text-brand-500 text-[10px] mt-0.5 shrink-0"></i>
                                <span class="line-clamp-2" x-text="session.context_summary || session.last_message"></span>
                            </div>

                            <!-- Footer PNR, Waktu Pesan & Tombol Hapus Sesi -->
                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                                <span class="font-bold text-slate-600" x-text="'PNR: ' + session.pnr_code"></span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-sans text-[9.5px] text-slate-400" x-text="session.last_message_time || session.departure_time"></span>
                                    <button type="button"
                                            @click.stop="deleteChatSession(session.id, session.pnr_code)"
                                            class="w-6 h-6 rounded-md hover:bg-rose-100 text-slate-400 hover:text-rose-600 flex items-center justify-center transition cursor-pointer"
                                            :title="lang === 'id' ? 'Hapus Riwayat Chat Sesi Ini' : 'Delete Chat History Session'">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

            </div>
        </template>

        {{-- id: Tampilan Kosong Jika Belum Ada Sesi Chat / Hasil Pencarian Tidak Ditemukan --}}
        <template x-if="!filteredChatSessions || filteredChatSessions.length === 0">
            <div class="py-8 px-3 text-center text-slate-400 space-y-2 select-none">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div class="text-xs font-medium text-slate-600" x-text="ticketSearch ? (lang === 'id' ? 'Hasil tidak ditemukan' : 'No results found') : (lang === 'id' ? 'Belum Ada Riwayat Chat' : 'No Chat History Yet')"></div>
                <p class="text-[10.5px] text-slate-400 leading-relaxed" x-text="lang === 'id' ? 'Aktivasi tiket PNR Anda untuk memulai sesi obrolan dengan asisten AI.' : 'Activate your PNR ticket to start a chat session with the AI assistant.'"></p>
            </div>
        </template>

    </div>


    {{-- #BACKEND Bar Profil Pengguna di Bawah Sidebar
         id: Data profil user (nama, inisial, email) diambil dari currentUser yang sudah sinkron dengan Auth::user().
         en: User profile bar data (name, initials, email) taken from currentUser synced with Auth::user(). --}}
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
