{{-- id: Navbar utama \u2014 berisi logo REBOUND, tombol sidebar, navigasi desktop, pemilih bahasa (ID/EN), notifikasi, profil user
     en: Main navbar \u2014 contains REBOUND logo, sidebar toggle, desktop navigation, language picker (ID/EN), notifications, user profile
     #BACKEND id: Data profil user (nama, inisial, email) diambil dari currentUser yang sudah dari Auth. Notifikasi harus dari database notifications.
     #BACKEND en: User profile data (name, initials, email) from currentUser already from Auth. Notifications must come from notifications database. --}}
<header class="h-[64px] sm:h-[56px] pt-2.5 sm:pt-0 bg-white border-b border-[#E2E8F0] px-3.5 sm:px-5 md:px-6 flex items-center justify-between z-30 shrink-0 select-none relative">
    
    <!-- LEFT: Logo -->
    <div class="flex items-center gap-2">
        <a href="/" class="hover:opacity-90 transition">
            <x-logo size="sm" />
        </a>
    </div>

    <!-- CENTER: Main Navigation Tabs (Clean & Balanced, centered on iPad & Desktop) -->
    <nav class="hidden md:flex items-center gap-1 bg-[#F1F5F9] p-0.5 rounded-xl border border-slate-200/70">
        <!-- Assistant Tab -->
        <button @click="mobileTab = 'assistant'"
                :class="mobileTab === 'assistant' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/50 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs rounded-lg transition">
            <i class="fa-solid fa-robot text-brand-600 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'Asisten AI' : 'Assistant'"></span>
        </button>

        <!-- My Trips Tab -->
        <button @click="showMyTripsModal = true"
                class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 rounded-lg hover:bg-white/60 transition">
            <i class="fa-solid fa-plane text-slate-500 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'Perjalanan Saya' : 'My Trips'"></span>
        </button>
    </nav>

    <!-- RIGHT: Actions, Scenario Controller & User Profile -->
    <div class="flex items-center gap-2 sm:gap-2.5">
        
        <!-- Compact Scenario Simulation Pill (Dropdown for testing different flight states) -->
        <div class="hidden xl:relative xl:block" x-data="{ openScenario: false }">
            <button @click="openScenario = !openScenario"
                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition bg-slate-50 hover:bg-slate-100/80 border-slate-200 text-slate-700">
                <span class="w-1.5 h-1.5 rounded-full" 
                      :class="flightStatus === 'on-time' ? 'bg-emerald-500' : (flightStatus === 'delayed' ? 'bg-amber-500' : 'bg-blue-500')"></span>
                <span x-text="flightStatus === 'on-time' ? (lang === 'id' ? 'Tepat Waktu' : 'On Time') : (flightStatus === 'delayed' ? (lang === 'id' ? 'Terlambat (+4j)' : 'Delayed (+4h)') : (lang === 'id' ? 'Terjadwal Baru' : 'Rebooked'))"></span>
                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 ml-0.5"></i>
            </button>

            <!-- Scenario Dropdown Menu -->
            <div x-show="openScenario" 
                 @click.away="openScenario = false" 
                 x-cloak
                 class="absolute right-0 mt-1.5 w-48 bg-white rounded-xl shadow-floating border border-slate-100 py-1 z-50 text-xs">
                <button @click="setStatus('on-time'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 flex items-center gap-2 text-[11px] font-medium cursor-pointer">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span x-text="lang === 'id' ? 'Tepat Waktu (On Time)' : 'On Time'"></span>
                </button>
                <button @click="setStatus('delayed'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-amber-50 text-slate-700 hover:text-amber-700 flex items-center gap-2 text-[11px] font-medium cursor-pointer">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span x-text="lang === 'id' ? 'Terlambat (+4j Cuaca)' : 'Delayed (+4h Weather)'"></span>
                </button>
                <button @click="setStatus('rebooked'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-blue-50 text-slate-700 hover:text-blue-700 flex items-center gap-2 text-[11px] font-medium cursor-pointer">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span x-text="lang === 'id' ? 'Terjadwal Baru (Rebooked)' : 'Rebooked (New Flight)'"></span>
                </button>
            </div>
        </div>

        <!-- Language Switcher Dropdown (Clean Flags Only, Sharp & Compact) -->
        <div class="relative" x-data="{ langDropdownOpen: false }">
            <button @click="langDropdownOpen = !langDropdownOpen"
                    class="flex items-center gap-1.5 px-2 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 shadow-2xs transition cursor-pointer"
                    :title="lang === 'id' ? 'Ganti Bahasa' : 'Switch Language'">
                
                <!-- Active Flag Indicator (EN / ID) -->
                <template x-if="lang === 'en'">
                    <svg class="w-5 h-3.5 rounded-[2px] shadow-2xs shrink-0 overflow-hidden border border-slate-200" viewBox="0 0 60 30">
                        <clipPath id="flag_uk_btn"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                        <g clip-path="url(#flag_uk_btn)">
                            <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#C8102E" stroke-width="3.5"/>
                            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
                        </g>
                    </svg>
                </template>
                <template x-if="lang === 'id'">
                    <svg class="w-5 h-3.5 rounded-[2px] shadow-2xs shrink-0 overflow-hidden border border-slate-200" viewBox="0 0 3 2">
                        <rect width="3" height="1" fill="#E70011"/>
                        <rect y="1" width="3" height="1" fill="#FFFFFF"/>
                    </svg>
                </template>

                <i class="fa-solid fa-chevron-down text-[8px] text-slate-400"></i>
            </button>

            <!-- Language Dropdown Menu (Flags Only, Minimalist & Crisp) -->
            <div x-show="langDropdownOpen" 
                 @click.away="langDropdownOpen = false" 
                 x-cloak
                 class="absolute right-0 mt-1 bg-white rounded-md shadow-md border border-slate-200 p-1 z-50 flex flex-col gap-1 min-w-[48px]">

                <!-- UK Flag Option -->
                <button @click="setLanguage('en'); langDropdownOpen = false" 
                        :class="lang === 'en' ? 'bg-blue-50 border border-brand-300' : 'border border-transparent hover:bg-slate-100'"
                        class="p-1.5 rounded-[4px] flex items-center justify-center transition cursor-pointer"
                        title="English">
                    <svg class="w-6 h-4 rounded-[2px] shadow-2xs shrink-0 overflow-hidden border border-slate-200" viewBox="0 0 60 30">
                        <clipPath id="flag_uk_menu"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                        <g clip-path="url(#flag_uk_menu)">
                            <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#C8102E" stroke-width="3.5"/>
                            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
                        </g>
                    </svg>
                </button>

                <!-- Indonesia Flag Option -->
                <button @click="setLanguage('id'); langDropdownOpen = false" 
                        :class="lang === 'id' ? 'bg-blue-50 border border-brand-300' : 'border border-transparent hover:bg-slate-100'"
                        class="p-1.5 rounded-[4px] flex items-center justify-center transition cursor-pointer"
                        title="Bahasa Indonesia">
                    <svg class="w-6 h-4 rounded-[2px] shadow-2xs shrink-0 overflow-hidden border border-slate-200" viewBox="0 0 3 2">
                        <rect width="3" height="1" fill="#E70011"/>
                        <rect y="1" width="3" height="1" fill="#FFFFFF"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Help & FAQ Guide Button -->
        <button @click="showHelpModal = true"
                class="hidden lg:flex w-7 h-7 rounded-md items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer border border-transparent hover:border-slate-200"
                :title="lang === 'id' ? 'Pusat Bantuan' : 'Help Guide'">
            <i class="fa-regular fa-circle-question text-xs"></i>
        </button>

        <!-- Notification Center Dropdown -->
        <div class="relative" x-data="{ notifOpen: false }">
            <button @click="notifOpen = !notifOpen"
                    class="w-7 h-7 rounded-md flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition relative cursor-pointer border border-transparent hover:border-slate-200"
                    :title="t('notifications')">
                <i class="fa-regular fa-bell text-xs"></i>
                <span x-show="hasUnreadNotif" class="absolute top-1 right-1 w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
            </button>

            <!-- Notifications Dropdown Panel -->
            <div x-show="notifOpen" 
                 @click.away="notifOpen = false" 
                 x-cloak
                 class="absolute right-0 mt-1.5 w-72 sm:w-80 bg-white rounded-lg shadow-lg border border-slate-200 py-1.5 z-50 text-xs text-left">
                
                <!-- Notification Header -->
                <div class="px-3.5 py-1.5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-900" x-text="lang === 'id' ? 'Notifikasi Operasional' : 'Operational Alerts'"></span>
                        <span x-show="hasUnreadNotif" class="px-1.5 py-0.2 bg-amber-100 text-amber-800 font-bold text-[9px] rounded">3 Baru</span>
                    </div>
                    <button @click="hasUnreadNotif = false" 
                            class="text-[10.5px] text-brand-600 hover:text-brand-700 font-semibold cursor-pointer"
                            x-text="lang === 'id' ? 'Tandai Dibaca' : 'Mark Read'">
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto custom-scrollbar">
                    
                    <!-- Alert 1: Flight Delay -->
                    <div class="p-2.5 hover:bg-slate-50 transition cursor-pointer space-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-amber-600 flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span x-text="lang === 'id' ? 'Keterlambatan Penerbangan' : 'Flight Delay'"></span>
                            </span>
                            <span class="text-[9px] text-slate-400 font-mono">2m lalu</span>
                        </div>
                        <p class="text-[11px] text-slate-700 font-medium leading-snug"
                           x-text="lang === 'id' ? 'GA826 resmi ditunda +4 jam karena cuaca buruk. Opsi rebooking telah aktif.' : 'GA826 officially delayed +4 hours due to weather. Rebooking options activated.'"></p>
                    </div>

                    <!-- Alert 2: Rebooking Available -->
                    <div class="p-2.5 hover:bg-slate-50 transition cursor-pointer space-y-0.5"
                         @click="mobileTab = 'assistant'; notifOpen = false">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-brand-600 flex items-center gap-1">
                                <i class="fa-solid fa-plane-departure"></i>
                                <span x-text="lang === 'id' ? 'Alternatif Tersedia' : 'Alternative Ready'"></span>
                            </span>
                            <span class="text-[9px] text-slate-400 font-mono">5m lalu</span>
                        </div>
                        <p class="text-[11px] text-slate-700 font-medium leading-snug"
                           x-text="lang === 'id' ? 'Garuda GA830 (Gate 4A) siap dialihkan bebas biaya (Waiver 72A).' : 'Garuda GA830 (Gate 4A) ready for zero-fee transfer (Waiver 72A).'"></p>
                    </div>

                    <!-- Alert 3: Baggage Telemetry -->
                    <div class="p-2.5 hover:bg-slate-50 transition cursor-pointer space-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-suitcase-rolling"></i>
                                <span x-text="lang === 'id' ? 'Status Bagasi' : 'Baggage Telemetry'"></span>
                            </span>
                            <span class="text-[9px] text-slate-400 font-mono">15m lalu</span>
                        </div>
                        <p class="text-[11px] text-slate-700 font-medium leading-snug"
                           x-text="lang === 'id' ? 'Tag bagasi #GA-489102 tercatat di sistem Terminal 3 CGK.' : 'Baggage tag #GA-489102 verified in Terminal 3 CGK system.'"></p>
                    </div>

                </div>
            </div>
        </div>

        <!-- User Profile Avatar & Switcher -->
        <div class="relative">
            <button @click="showUserDropdown = !showUserDropdown"
                    class="w-7 h-7 rounded-md bg-brand-600 text-white flex items-center justify-center font-bold text-[11px] shadow-2xs cursor-pointer hover:bg-brand-700 transition"
                    :title="currentUser.name">
                <span x-text="currentUser.initials"></span>
            </button>

            <!-- User Switcher Dropdown -->
            <div x-show="showUserDropdown"
                 @click.away="showUserDropdown = false"
                 x-cloak
                 class="absolute right-0 mt-1.5 w-60 bg-white rounded-lg shadow-lg border border-slate-200 py-1.5 z-50 text-xs">
                
                <div class="px-3.5 py-1.5 border-b border-slate-100">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold" x-text="t('active_account')"></p>
                    <p class="font-bold text-slate-900 mt-0.5" x-text="currentUser.name"></p>
                    <p class="text-[10.5px] text-slate-500 truncate" x-text="currentUser.email"></p>
                </div>

                <!-- Scenario Switcher for Tablet / Mobile / iPad -->
                <div class="px-3.5 py-1.5 border-b border-slate-100 xl:hidden">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1"
                       x-text="lang === 'id' ? 'Simulasi Status Flight:' : 'Simulate Flight Status:'"></p>
                    <div class="grid grid-cols-3 gap-1 text-[10.5px]">
                        <button @click="setStatus('on-time')" 
                                :class="flightStatus === 'on-time' ? 'bg-emerald-50 text-emerald-700 font-bold border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-md border text-center transition cursor-pointer"
                                x-text="lang === 'id' ? 'Tepat Waktu' : 'On Time'">
                        </button>
                        <button @click="setStatus('delayed')" 
                                :class="flightStatus === 'delayed' ? 'bg-amber-50 text-amber-700 font-bold border-amber-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-md border text-center transition cursor-pointer"
                                x-text="lang === 'id' ? 'Terlambat' : 'Delayed'">
                        </button>
                        <button @click="setStatus('rebooked')" 
                                :class="flightStatus === 'rebooked' ? 'bg-blue-50 text-blue-700 font-bold border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-md border text-center transition cursor-pointer"
                                x-text="lang === 'id' ? 'Terjadwal' : 'Rebooked'">
                        </button>
                    </div>
                </div>

                <!-- Logout Form -->
                <div class="border-t border-slate-100 pt-1 mt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-3.5 py-1.5 text-rose-600 hover:bg-rose-50 font-semibold flex items-center gap-2 transition cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span x-text="t('logout')"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
