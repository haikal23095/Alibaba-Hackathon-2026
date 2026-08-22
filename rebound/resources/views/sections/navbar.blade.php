<header class="h-[56px] bg-white border-b border-[#E2E8F0] px-3 sm:px-5 md:px-6 flex items-center justify-between z-30 shrink-0 select-none relative">
    
    <!-- LEFT: Logo & Ticket History Toggle Button -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Toggle Riwayat Tiket / PNR History Button -->
        <button @click="if (window.innerWidth < 1024) { mobileTab = (mobileTab === 'tickets' ? 'assistant' : 'tickets'); } else { leftSidebarOpen = !leftSidebarOpen; }" 
                :class="(mobileTab === 'tickets' || (leftSidebarOpen && window.innerWidth >= 1024)) ? 'bg-blue-50 text-brand-600 border border-brand-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent'"
                class="w-8 h-8 rounded-lg flex items-center justify-center transition cursor-pointer"
                title="Riwayat Tiket PNR">
            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
        </button>

        <a href="/" class="text-lg font-black tracking-tight text-[#0F172A] flex items-center gap-2 hover:opacity-90 transition">
            <span>REBOUND</span>
        </a>
    </div>

    <!-- CENTER: Main Navigation Tabs (Clean & Balanced, centered on iPad & Desktop) -->
    <nav class="hidden md:flex items-center gap-1 bg-[#F1F5F9] p-0.5 rounded-xl border border-slate-200/70">
        <!-- Assistant Tab -->
        <button @click="mobileTab = 'assistant'"
                :class="mobileTab === 'assistant' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/50 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs rounded-lg transition">
            <i class="fa-solid fa-wand-magic-sparkles text-brand-600 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'Assistant' : 'Assistant'"></span>
        </button>

        <!-- My Trips Tab -->
        <button @click="showMyTripsModal = true"
                class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 rounded-lg hover:bg-white/60 transition">
            <i class="fa-solid fa-plane text-slate-500 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'My Trips' : 'My Trips'"></span>
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
                 class="absolute right-0 mt-1.5 w-44 bg-white rounded-xl shadow-floating border border-slate-100 py-1 z-50 text-xs">
                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Simulasi Status:</div>
                <button @click="setStatus('on-time'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 flex items-center gap-2 text-[11px] font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Tepat Waktu (On Time)</span>
                </button>
                <button @click="setStatus('delayed'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-amber-50 text-slate-700 hover:text-amber-700 flex items-center gap-2 text-[11px] font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Terlambat (+4j Cuaca)</span>
                </button>
                <button @click="setStatus('rebooked'); openScenario = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-blue-50 text-slate-700 hover:text-blue-700 flex items-center gap-2 text-[11px] font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Terjadwal Baru (Rebooked)</span>
                </button>
            </div>
        </div>

        <!-- Language Switcher (ID / EN) -->
        <div class="flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200 text-[11px] font-bold">
            <button @click="setLanguage('id')"
                    :class="lang === 'id' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                    class="px-2 py-0.5 rounded transition">ID</button>
            <button @click="setLanguage('en')"
                    :class="lang === 'en' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                    class="px-2 py-0.5 rounded transition">EN</button>
        </div>

        <!-- Help Button (Hidden on small tablets, shown on desktop) -->
        <button class="hidden lg:flex w-7 h-7 rounded-full items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                title="Help & FAQ">
            <i class="fa-regular fa-circle-question text-xs"></i>
        </button>

        <!-- Notification Bell -->
        <button class="w-7 h-7 rounded-full flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition relative"
                title="Notifications">
            <i class="fa-regular fa-bell text-xs"></i>
            <span class="absolute top-1 right-1 w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
        </button>

        <!-- User Profile Avatar & Switcher -->
        <div class="relative">
            <button @click="showUserDropdown = !showUserDropdown"
                    class="w-7 h-7 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-[11px] shadow-xs cursor-pointer hover:ring-2 hover:ring-brand-500 hover:ring-offset-2 transition"
                    :title="currentUser.name">
                <span x-text="currentUser.initials"></span>
            </button>

            <!-- User Switcher Dropdown -->
            <div x-show="showUserDropdown"
                 @click.away="showUserDropdown = false"
                 x-cloak
                 class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-floating border border-slate-100 py-2 z-50 text-xs">
                
                <div class="px-3.5 py-2 border-b border-slate-100">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Akun Aktif</p>
                    <p class="font-bold text-slate-900 mt-0.5" x-text="currentUser.name"></p>
                    <p class="text-[11px] text-slate-500 truncate" x-text="currentUser.email"></p>
                </div>

                <!-- Scenario Switcher for Tablet / Mobile / iPad -->
                <div class="px-3.5 py-2 border-b border-slate-100 xl:hidden">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1.5">Simulasi Status Flight:</p>
                    <div class="grid grid-cols-3 gap-1 text-[10.5px]">
                        <button @click="setStatus('on-time')" 
                                :class="flightStatus === 'on-time' ? 'bg-emerald-50 text-emerald-700 font-bold border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-lg border text-center transition">
                            Tepat Waktu
                        </button>
                        <button @click="setStatus('delayed')" 
                                :class="flightStatus === 'delayed' ? 'bg-amber-50 text-amber-700 font-bold border-amber-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-lg border text-center transition">
                            Delayed
                        </button>
                        <button @click="setStatus('rebooked')" 
                                :class="flightStatus === 'rebooked' ? 'bg-blue-50 text-blue-700 font-bold border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                class="py-1 px-1 rounded-lg border text-center transition">
                            Rebooked
                        </button>
                    </div>
                </div>

                <!-- User Demo Switcher -->
                <div class="py-1">
                    <div class="px-3.5 py-1 text-[10px] uppercase tracking-wider text-slate-400 font-bold">Ganti Akun Demo:</div>
                    <template x-for="user in users" :key="user.id">
                        <button @click="setUser(user)"
                                :class="currentUser.id === user.id ? 'bg-blue-50/80 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-slate-50'"
                                class="w-full text-left px-3.5 py-2 flex items-center justify-between transition">
                            <div class="flex items-center gap-2.5">
                                <div class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-[10px] flex items-center justify-center"
                                     :class="currentUser.id === user.id ? 'bg-brand-600' : 'bg-slate-800'"
                                     x-text="user.initials"></div>
                                <div>
                                    <div class="font-medium" x-text="user.name"></div>
                                    <div class="text-[10px] text-slate-400" x-text="user.role"></div>
                                </div>
                            </div>
                            <i x-show="currentUser.id === user.id" class="fa-solid fa-check text-brand-600 text-xs"></i>
                        </button>
                    </template>
                </div>

                <!-- Logout Form -->
                <div class="border-t border-slate-100 pt-1 mt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-3.5 py-2 text-rose-600 hover:bg-rose-50 font-semibold flex items-center gap-2 transition cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
