<header class="h-[56px] bg-white border-b border-[#E2E8F0] px-4 md:px-6 flex items-center justify-between z-30 shrink-0 select-none">
    <!-- Brand Logo & Left Sidebar Toggle Button -->
    <div class="flex items-center gap-3">
        <!-- Toggle Left Sidebar (Riwayat Tiket) -->
        <button @click="leftSidebarOpen = !leftSidebarOpen" 
                class="w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition"
                title="Toggle Ticket History">
            <i class="fa-solid fa-bars-staggered text-sm"></i>
        </button>

        <a href="/" class="text-lg font-bold tracking-tight text-[#0F172A] flex items-center gap-2 hover:opacity-90 transition">
            <span>REBOUND</span>
        </a>

        <!-- Scenario Quick Switcher -->
        <div class="hidden lg:flex items-center bg-slate-100/90 p-0.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200/60 ml-2">
            <button @click="setStatus('on-time')" 
                    :class="flightStatus === 'on-time' ? 'bg-white text-emerald-700 shadow-xs font-bold' : 'hover:text-slate-900'"
                    class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5 text-[11px]">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span x-text="lang === 'id' ? 'Tepat Waktu' : 'On Time'"></span>
            </button>
            <button @click="setStatus('delayed')" 
                    :class="flightStatus === 'delayed' ? 'bg-white text-amber-700 shadow-xs font-bold' : 'hover:text-slate-900'"
                    class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5 text-[11px]">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span x-text="lang === 'id' ? 'Terlambat (+4j)' : 'Delayed (+4h)'"></span>
            </button>
            <button @click="setStatus('rebooked')" 
                    :class="flightStatus === 'rebooked' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'hover:text-slate-900'"
                    class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5 text-[11px]">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                <span x-text="lang === 'id' ? 'Terjadwal Baru' : 'Rebooked'"></span>
            </button>
        </div>
    </div>

    <!-- Center Navigation Links -->
    <nav class="flex items-center gap-1 bg-[#F1F5F9] p-0.5 rounded-xl border border-slate-200/60">
        <!-- Assistant Tab -->
        <button class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-900 shadow-xs border border-slate-200/50">
            <i class="fa-solid fa-wand-magic-sparkles text-brand-600 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'Assistant' : 'Assistant'"></span>
        </button>

        <!-- My Trips Tab -->
        <button @click="showMyTripsModal = true"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 rounded-lg hover:bg-white/60 transition">
            <i class="fa-solid fa-plane text-slate-500 text-[11px]"></i>
            <span x-text="lang === 'id' ? 'My Trips' : 'My Trips'"></span>
        </button>
    </nav>

    <!-- Right Actions & User Profile Dropdown -->
    <div class="flex items-center gap-2.5 relative">
        <!-- Active Flight Monitoring Pill Badge -->
        <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200/70 rounded-full text-[11px] font-semibold text-emerald-800">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span x-text="lang === 'id' ? 'Memantau 1 perjalanan aktif' : 'Monitoring 1 active trip'"></span>
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

        <!-- Help Button -->
        <button class="w-7 h-7 rounded-full flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
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

            <!-- User Switcher Dropdown (Zakaria MP, Haikal Firmansyah, Tiara Fatimah Azzahra) -->
            <div x-show="showUserDropdown"
                 @click.away="showUserDropdown = false"
                 x-cloak
                 class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-floating border border-slate-100 py-2 z-50 text-xs">
                
                <div class="px-3.5 py-2 border-b border-slate-100">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Akun Aktif</p>
                    <p class="font-bold text-slate-900 mt-0.5" x-text="currentUser.name"></p>
                    <p class="text-[11px] text-slate-500 truncate" x-text="currentUser.email"></p>
                </div>

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
                                class="w-full text-left px-3.5 py-2 text-rose-600 hover:bg-rose-50 font-semibold flex items-center gap-2 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
