{{-- #BACKEND Sidebar Kanan Detail Penerbangan & Boarding Pass / Right Workspace Sidebar
     id: Sidebar kanan interaktif dengan 4 tab: Ringkasan (Overview), Aturan (Policy), Jadwal (Schedule), dan Resi (Receipts / Boarding Pass).
         Seluruh data penerbangan (timeline, nomor penerbangan, kelas, status radar, aturan, perbandingan jadwal) harus diambil dari database `flights`, `bookings`, `fare_rules` & GDS API real-time.
     en: Interactive right sidebar with 4 tabs: Overview, Policy, Schedule, and Receipts (Boarding Pass).
         All flight data (timeline, flight number, cabin class, radar status, rules, schedule comparison) must be fetched from `flights`, `bookings`, `fare_rules` database & real-time GDS API. --}}
<!-- Right Workspace Sidebar: Zoom-out & Compact Edition (Figma Nodes 3:342, 3:198, 21:1065, 25:1168) -->
<aside class="w-full lg:w-[310px] xl:w-[330px] bg-white border-l border-slate-200 flex flex-col h-full overflow-y-auto shrink-0 z-20 text-xs pb-16 lg:pb-0 select-none">
    
    {{-- id: Header Sidebar (Nomor Penerbangan & Status Badge)
         en: Sidebar Header (Flight Number & Status Badge) --}}
    <!-- Sidebar Header (Compact) -->
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900 tracking-tight"
                x-text="lang === 'id' ? 'Status ' + activeFlight.flightNumber : activeFlight.flightNumber + ' Status'"></h3>
            
            <div class="flex items-center gap-1.5 mt-0.5 text-[11px]">
                <template x-if="flightStatus === 'on-time' || flightStatus === 'on_time' || flightStatus === 'active'">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span x-text="lang === 'id' ? 'Tepat Waktu • ' + activeFlight.fromCode + ' ke ' + activeFlight.toCode : 'On Time • ' + activeFlight.fromCode + ' to ' + activeFlight.toCode"></span>
                    </span>
                </template>
                <template x-if="flightStatus === 'delayed'">
                    <span class="text-amber-600 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span x-text="lang === 'id' ? 'Terlambat • ' + activeFlight.fromCode + ' ke ' + activeFlight.toCode : 'Delayed • ' + activeFlight.fromCode + ' to ' + activeFlight.toCode"></span>
                    </span>
                </template>
                <template x-if="flightStatus === 'cancelled'">
                    <span class="text-rose-600 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span x-text="lang === 'id' ? 'Dibatalkan • ' + activeFlight.fromCode + ' ke ' + activeFlight.toCode : 'Cancelled • ' + activeFlight.fromCode + ' to ' + activeFlight.toCode"></span>
                    </span>
                </template>
                <template x-if="flightStatus === 'rebooked'">
                    <span class="text-brand-600 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        <span x-text="lang === 'id' ? 'Telah Diubah ke ' + activeFlight.flightNumber : 'Rebooked to ' + activeFlight.flightNumber"></span>
                    </span>
                </template>
            </div>
        </div>

        {{-- id: Menu Dropdown Aksi Penerbangan (Perbarui Radar, Salin PNR, Kelola Tiket, Unduh PDF, Aktifkan SMS Alert)
             en: Flight Actions Context Dropdown Menu (Refresh Radar, Copy PNR, Manage Ticket, Download PDF, Enable SMS Alerts) --}}
        <!-- Flight Actions Context Menu Dropdown (Interactive) -->
        <div class="relative" x-data="{ flightMenuOpen: false }">
            <button @click="flightMenuOpen = !flightMenuOpen"
                    class="w-7 h-7 rounded-lg border border-slate-200/80 bg-slate-50 text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer shadow-2xs"
                    :title="lang === 'id' ? 'Opsi Penerbangan' : 'Flight Options'">
                <i class="fa-solid fa-ellipsis text-xs"></i>
            </button>

            <!-- Context Dropdown Menu -->
            <div x-show="flightMenuOpen" 
                 @click.away="flightMenuOpen = false" 
                 x-cloak
                 class="absolute right-0 mt-1.5 w-56 bg-white rounded-lg shadow-lg border border-slate-200 py-1.5 z-50 text-xs text-left">
                
                <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                     x-text="lang === 'id' ? 'Tindakan Penerbangan' : 'Flight Actions'"></div>

                <!-- 1. Refresh Radar Status -->
                <button @click="flightMenuOpen = false; showToast(lang === 'id' ? 'Status radar telemetri penerbangan berhasil diperbarui live!' : 'Live radar flight telemetry refreshed!')"
                        class="w-full text-left px-3.5 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 transition cursor-pointer text-xs">
                    <i class="fa-solid fa-arrows-rotate text-brand-600 text-xs w-4 text-center"></i>
                    <span x-text="lang === 'id' ? 'Perbarui Data Radar Live' : 'Refresh Live Radar Data'"></span>
                </button>

                <!-- 2. Copy PNR Code -->
                <button @click="navigator.clipboard.writeText(selectedTicketId); flightMenuOpen = false; showToast(lang === 'id' ? 'Kode PNR ' + selectedTicketId + ' disalin ke clipboard!' : 'PNR Code ' + selectedTicketId + ' copied to clipboard!')"
                        class="w-full text-left px-3.5 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 transition cursor-pointer text-xs">
                    <i class="fa-regular fa-copy text-slate-500 text-xs w-4 text-center"></i>
                    <span x-text="lang === 'id' ? 'Salin Kode PNR (' + selectedTicketId + ')' : 'Copy PNR Code (' + selectedTicketId + ')'"></span>
                </button>

                <!-- 3. Switch / Manage Ticket in My Trips -->
                <button @click="showMyTripsModal = true; flightMenuOpen = false"
                        class="w-full text-left px-3.5 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 transition cursor-pointer text-xs">
                    <i class="fa-solid fa-plane-up text-sky-600 text-xs w-4 text-center"></i>
                    <span x-text="lang === 'id' ? 'Kelola di Perjalanan Saya' : 'Manage in My Trips'"></span>
                </button>

                <!-- 4. Download / Print PDF -->
                <button @click="downloadPdf(); flightMenuOpen = false"
                        class="w-full text-left px-3.5 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 transition cursor-pointer text-xs">
                    <i class="fa-solid fa-file-pdf text-red-500 text-xs w-4 text-center"></i>
                    <span x-text="lang === 'id' ? 'Cetak / Unduh PDF Tiket' : 'Print / Download Ticket PDF'"></span>
                </button>

                <div class="my-1 border-t border-slate-100"></div>

                <!-- 5. Toggle Alert Notifications -->
                <button @click="flightMenuOpen = false; showToast(lang === 'id' ? 'Pengingat SMS & Notifikasi Gate aktif!' : 'SMS Alert & Gate Reminders activated!')"
                        class="w-full text-left px-3.5 py-2 hover:bg-amber-50 text-amber-900 flex items-center gap-2.5 transition cursor-pointer text-xs font-medium">
                    <i class="fa-solid fa-bell text-amber-500 text-xs w-4 text-center"></i>
                    <span x-text="lang === 'id' ? 'Aktifkan Pengingat Gate SMS' : 'Enable SMS Gate Alerts'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- id: Baris Tab Navigasi Sidebar (Ringkasan, Aturan, Jadwal, Resi)
         en: Sidebar Navigation Tabs (Overview, Policy, Schedule, Receipts) --}}
    <!-- Navigation Tabs (Compact) -->
    <div class="p-2.5 border-b border-slate-100">
        <div class="grid grid-cols-4 gap-1 bg-slate-100/90 p-0.5 rounded-lg text-[11px] font-semibold text-slate-600">
            <!-- Tab 1: Overview -->
            <button @click="activeSidebarTab = 'overview'"
                    :class="activeSidebarTab === 'overview' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-md transition text-center flex flex-col items-center gap-0.5 cursor-pointer">
                <i class="fa-regular fa-compass text-[10px]"></i>
                <span class="truncate" x-text="lang === 'id' ? 'Ringkasan' : 'Overview'"></span>
            </button>

            <!-- Tab 2: Policy -->
            <button @click="activeSidebarTab = 'policy'"
                    :class="activeSidebarTab === 'policy' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-md transition text-center flex flex-col items-center gap-0.5 cursor-pointer">
                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                <span class="truncate" x-text="lang === 'id' ? 'Aturan' : 'Policy'"></span>
            </button>

            <!-- Tab 3: Schedule -->
            <button @click="activeSidebarTab = 'schedule'"
                    :class="activeSidebarTab === 'schedule' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-md transition text-center flex flex-col items-center gap-0.5 cursor-pointer">
                <i class="fa-regular fa-clock text-[10px]"></i>
                <span class="truncate" x-text="lang === 'id' ? 'Jadwal' : 'Schedule'"></span>
            </button>

            <!-- Tab 4: Receipts -->
            <button @click="activeSidebarTab = 'receipts'"
                    :class="activeSidebarTab === 'receipts' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'hover:text-slate-900'"
                    class="py-1.5 px-1 rounded-md transition text-center flex flex-col items-center gap-0.5 cursor-pointer">
                <i class="fa-solid fa-receipt text-[10px]"></i>
                <span class="truncate" x-text="lang === 'id' ? 'Resi' : 'Receipts'"></span>
            </button>
        </div>
    </div>

    <!-- Tab Content (Compact Zoom-out padding) -->
    <div class="p-2.5 space-y-2.5 flex-1 overflow-y-auto custom-scrollbar">
        
        {{-- id: ================= TAB 1: OVERVIEW / RINGKASAN =================
             en: ================= TAB 1: OVERVIEW / RINGKASAN ================= --}}
        <div x-show="activeSidebarTab === 'overview'" class="space-y-3">
            
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                 x-text="lang === 'id' ? 'DETAIL PERJALANAN' : 'TRIP DETAILS'"></div>

            {{-- id: Kartu Detail Perjalanan & Timeline Rute
                 en: Trip Details Card & Route Timeline --}}
            <!-- Trip Details Card (Figma Node 3:342, 3:198) -->
            <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs space-y-3">
                <!-- Airline & Class -->
                <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                    <div class="w-8 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700 text-xs font-bold border border-slate-200">
                        <i class="fa-solid fa-plane text-[11px]"></i>
                    </div>
                    <div>
                        {{-- id: Data maskapai & kelas mengikuti activeFlight agar setelah rebooking menampilkan maskapai baru (mis. Batik Air), bukan Garuda lama
                             en: Airline & class follow activeFlight so after rebooking the new airline (e.g. Batik Air) is shown, not the old Garuda --}}
                        <div class="font-bold text-slate-900 text-xs" x-text="activeFlight.airline"></div>
                        <div class="text-[11px] text-slate-500 font-medium" x-text="activeFlight.class"></div>
                    </div>
                </div>

                <!-- Flight Route Timeline with Dots -->
                <div class="relative pl-5 space-y-3.5 before:absolute before:left-1.5 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200">
                    <!-- Origin Departure -->
                    <div class="relative">
                        <div class="absolute -left-5 top-1 w-2 h-2 rounded-full bg-slate-400 ring-2 ring-white"></div>
                        <div class="font-bold text-slate-900 text-xs" x-text="activeFlight.fromCode + ' ' + activeFlight.fromCity"></div>
                        <div class="text-[11px] text-slate-500 font-medium"
                             x-text="activeFlight.date + ', ' + activeFlight.depTime"></div>
                    </div>

                    <!-- Destination Arrival -->
                    <div class="relative">
                        <div class="absolute -left-5 top-1 w-2 h-2 rounded-full bg-slate-900 ring-2 ring-white"></div>
                        <div class="font-bold text-slate-900 text-xs"
                             x-text="activeFlight.toCode + ' ' + (lang === 'id' ? activeFlight.toCity : activeFlight.toCityEn)"></div>
                        <div class="text-[11px] text-slate-500 font-medium"
                             x-text="activeFlight.date + ', ' + activeFlight.arrTime"></div>
                    </div>
                </div>
            </div>

            {{-- id: Widget Pencarian Alternatif saat status delayed (Figma Node 21:1065)
                 en: Alternative Search Widget when flight is delayed (Figma Node 21:1065) --}}
            <!-- Alternative Search Widget (Figma Node 21:1065) -->
            <div x-show="flightStatus === 'delayed'" class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs space-y-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-blue-50 text-brand-600 flex items-center justify-center text-[11px]">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs"
                        x-text="lang === 'id' ? 'Pencarian Alternatif' : 'Alternative Search'"></h4>
                </div>

                <div class="space-y-1.5 text-[11px] divide-y divide-slate-100">
                    <div class="flex justify-between py-0.5">
                        <span class="text-slate-500" x-text="lang === 'id' ? 'Penerbangan Asal' : 'Original Flight'"></span>
                        <span class="font-semibold text-slate-900" x-text="(flight.original.flightNumber || selectedTicketId) + ' (Terlambat)'"></span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-slate-500" x-text="lang === 'id' ? 'Rute' : 'Route'"></span>
                        <span class="font-semibold text-slate-900" x-text="(flight.original.fromCode || 'CGK') + ' → ' + (flight.original.toCode || 'SIN')"></span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-slate-500" x-text="lang === 'id' ? 'Tanggal' : 'Date'"></span>
                        <span class="font-semibold text-slate-900" x-text="lang === 'id' ? 'Hari ini' : 'Today'"></span>
                    </div>
                </div>

                <!-- Verified Badge Note -->
                <div class="p-2.5 bg-emerald-50/70 border border-emerald-100 rounded-lg text-[11px] space-y-0.5">
                    <div class="flex items-center gap-1 font-bold text-emerald-800 uppercase text-[10px]">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                        <span x-text="lang === 'id' ? 'KEBIJAKAN TERVERIFIKASI' : 'POLICY VERIFIED'"></span>
                    </div>
                    <p class="text-[10.5px] text-emerald-700 leading-tight"
                       x-text="lang === 'id' ? 'Bebas biaya perubahan karena keterlambatan operasional maskapai ' + (flight.original.flightNumber || selectedTicketId) + '. Selisih tarif ditiadakan.' : 'Free rebooking due to ' + (flight.original.flightNumber || selectedTicketId) + ' operational delays. Fare diff waived.'"></p>
                </div>
            </div>

            <!-- Quick Policy Breakdown Card -->
            @include('sections.ticket-policy')
        </div>

        {{-- id: ================= TAB 2: POLICY / ATURAN TIKET =================
             en: ================= TAB 2: POLICY / ATURAN TIKET ================= --}}
        <div x-show="activeSidebarTab === 'policy'" class="space-y-3">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                 x-text="lang === 'id' ? 'KEBIJAKAN MASKAPAI' : 'AIRLINE POLICY'"></div>
            
            @include('sections.ticket-policy')

            <!-- Compensation Notice -->
            <div class="bg-amber-50/70 border border-amber-200/70 rounded-xl p-3 text-[11px] space-y-1.5">
                <div class="flex items-center gap-1.5 font-bold text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xs"></i>
                    <span x-text="lang === 'id' ? 'Hak Kompensasi Penumpang' : 'Passenger Rights & Compensation'"></span>
                </div>
                <p class="text-amber-700 leading-snug"
                   x-text="lang === 'id' ? 'Penundaan > 4 jam memberi Anda hak atas voucher makan dan prioritas rebooking gratis pada penerbangan berikutnya.' : 'Delays > 4 hours entitle passengers to meal vouchers and complimentary rebooking on the next available flight.'"></p>
            </div>
        </div>

        {{-- id: ================= TAB 3: SCHEDULE / JADWAL =================
             en: ================= TAB 3: SCHEDULE / JADWAL ================= --}}
        <div x-show="activeSidebarTab === 'schedule'" class="space-y-3">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                 x-text="lang === 'id' ? 'JADWAL & STATUS TERKINI' : 'SCHEDULE & LIVE STATUS'"></div>

            <!-- Comparison Card -->
            <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs space-y-2.5 text-xs">
                <div class="font-bold text-slate-900" x-text="lang === 'id' ? 'Penerbangan Hari Ini' : 'Flights Today'"></div>

                {{-- id: Kartu penerbangan aktif — mengikuti activeFlight sehingga setelah rebooking menampilkan data penerbangan baru
                     en: Active flight card — follows activeFlight so after rebooking it shows the new flight data --}}
                <!-- Active Flight Card -->
                <div class="p-2.5 rounded-lg border text-[11px]"
                     :class="flightStatus === 'delayed' ? 'border-amber-200 bg-amber-50/50' : 'border-emerald-200 bg-emerald-50/50'">
                    <div class="flex items-center justify-between font-bold text-slate-900 mb-0.5">
                        <span x-text="activeFlight.flightNumber + ' • ' + activeFlight.depTime"></span>
                        <span :class="flightStatus === 'delayed' ? 'text-amber-700 font-semibold' : 'text-emerald-700 font-semibold'"
                              x-text="flightStatus === 'delayed' ? (lang === 'id' ? 'Terlambat +4j 25m' : 'Delayed +4h 25m') : (flightStatus === 'rebooked' ? (lang === 'id' ? 'Terkonfirmasi (Rebook)' : 'Confirmed (Rebook)') : (lang === 'id' ? 'Tepat Waktu' : 'On Time'))"></span>
                    </div>
                    <p class="text-slate-500 text-[10px]" x-text="'Gate ' + activeFlight.gate + ' • Terminal ' + activeFlight.terminal + ' ' + activeFlight.fromCode"></p>
                </div>

                {{-- id: Kartu penerbangan alternatif hanya muncul saat masih delayed (sebelum rebooking),
                         karena setelah rebooked kartu di atas sudah menampilkan penerbangan baru
                     en: Alternative flight card only appears while delayed (before rebooking),
                         because after rebooked the card above already shows the new flight --}}
                <!-- Alternative Flight Card (when delayed) -->
                <template x-if="flightStatus === 'delayed'">
                    <div class="p-2.5 rounded-lg border border-brand-200 bg-blue-50/60 text-[11px]">
                        <div class="flex items-center justify-between font-bold text-slate-900 mb-0.5">
                            <span x-text="flight.alternative.flightNumber + ' • ' + flight.alternative.depTime"></span>
                            <span class="text-brand-700 font-semibold" x-text="lang === 'id' ? 'Tepat Waktu (Langsung)' : 'On Time (Direct)'"></span>
                        </div>
                        <p class="text-slate-500 text-[10px]" x-text="'Gate ' + flight.alternative.gate + ' • Terminal ' + (flight.original.terminal || '3') + ' ' + (flight.original.fromCode || 'CGK') + ' • Boarding'"></p>
                    </div>
                </template>
            </div>
        </div>


        {{-- id: ================= TAB 4: RECEIPTS / RESI & BOARDING PASS =================
             en: ================= TAB 4: RECEIPTS / RESI & BOARDING PASS ================= --}}
        <div x-show="activeSidebarTab === 'receipts'" class="space-y-3">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                 x-text="lang === 'id' ? 'E-BOARDING PASS & BUKTI RESMI' : 'E-BOARDING PASS & TICKET'"></div>

            <!-- Authentic Aviation Boarding Pass with Perforated Tear Line & Barcode -->
            @include('sections.boarding-pass')
        </div>

    </div>
</aside>
