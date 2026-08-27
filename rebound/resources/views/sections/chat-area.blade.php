{{-- #BACKEND Area Percakapan Asisten AI / Chat Conversation & Floating Sticky Input Area
     id: Area utama percakapan dengan AI. Menampilkan pesan AI, pesan user, indikator mengetik, kartu telemetri/rekomendasi, dan floating chat composer.
         Pesan baru dikirim via `sendMessage()`. Riwayat percakapan harus disimpan di database tabel `messages`.
     en: Main AI conversation area. Displays AI messages, user messages, typing indicators, telemetry/recommendation cards, and floating chat composer.
         New messages sent via `sendMessage()`. Chat history must be persisted in `messages` database table. --}}
<!-- Chat Conversation & Floating Sticky Input Area (Figma Nodes 3:158, 3:159, 15:816, 16:873, 16:884) -->
<div class="flex-1 flex flex-col h-full max-w-[640px] w-full mx-auto relative overflow-hidden">
    
    {{-- id: Tombol toggle saat sidebar kiri di-collapse pada desktop
         en: Toggle button when left sidebar is collapsed on desktop --}}
    <!-- Top Toggle when Sidebar is Collapsed on Desktop -->
    <div x-show="!leftSidebarOpen" x-cloak class="hidden lg:flex items-center justify-start pb-2 shrink-0">
        <button @click="leftSidebarOpen = true"
                class="flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 border border-slate-200 rounded-lg text-xs font-semibold shadow-2xs transition cursor-pointer">
            <i class="fa-solid fa-chevron-right text-[10px] text-brand-600"></i>
            <span x-text="lang === 'id' ? 'Tampilkan Riwayat Tiket' : 'Show Ticket History'"></span>
        </button>
    </div>

    {{-- id: Container Scroll Pesan Chat
         en: Scrollable Chat Messages Container --}}
    <!-- Messages Scrollable Container -->
    <div id="chat-messages-container" class="flex-1 overflow-y-auto px-1 sm:px-2 pt-1 space-y-4 pb-48 sm:pb-52 custom-scrollbar">
        
        {{-- id: Kartu status perjalanan aktif di bagian atas percakapan
             en: Active trip status card placed at the top of conversation --}}
        <!-- Active Trip Card at the top of workspace -->
        @include('sections.active-trip-card')

        {{-- #BACKEND Loop Riwayat Pesan Dinamis
             id: Di-loop dari state `messages`. Di produksi: data di-stream via SSE/WebSocket atau REST API dari database.
             en: Looped from `messages` state. In production: streamed via SSE/WebSocket or REST API from database. --}}
        <!-- Dynamic Message History -->
        <template x-for="(msg, index) in messages" :key="index">
            <div class="w-full">
                {{-- id: Baris Pesan dari Asisten AI
                     en: AI Assistant Message Row --}}
                <!-- AI Message Row -->
                <template x-if="msg.sender === 'ai'">
                    <div class="flex items-start gap-2 sm:gap-2.5">
                        <!-- AI Bot Icon Avatar -->
                        <div class="w-6 h-6 rounded-md bg-blue-100 text-brand-600 flex items-center justify-center shrink-0 mt-0.5 shadow-2xs text-[10px]">
                            <i class="fa-solid fa-robot text-[10px]"></i>
                        </div>

                        <!-- Message Body -->
                        <div class="flex-1 max-w-[500px]">
                            <div class="bg-white rounded-lg rounded-tl-none border border-slate-200 p-2.5 sm:p-3 text-xs text-slate-700 leading-relaxed shadow-xs">
                                <p x-text="lang === 'id' ? msg.textId : msg.textEn"></p>

                                {{-- id: Tampilkan Kartu Progress Analisis Gangguan (Figma Node 15:777)
                                     en: Show Disruption Analysis Progress Card (Figma Node 15:777) --}}
                                 <!-- Show Disruption Analysis Progress Card (Figma Node 15:777) -->
                                <template x-if="msg.showDisruptionProgress || msg.type === 'disruption_alert'">
                                    @include('sections.disruption-progress-card')
                                </template>

                                {{-- id: Tampilkan Kartu Aturan Kebijakan Tiket (Figma Node 22:1109)
                                     en: Show Verified Ticket Policy Card (Figma Node 22:1109) --}}
                                <!-- Show In-Chat Verified Ticket Policy Card (Figma Node 22:1109) -->
                                <template x-if="msg.showTicketPolicy || msg.type === 'policy_card'">
                                    @include('sections.chat-ticket-policy-card')
                                </template>

                                {{-- id: Tampilkan Kartu Rekomendasi Penerbangan jika tertunda & belum rebooked (Figma Node 21:894)
                                     en: Show Flight Recommendation Card if delayed & not yet rebooked (Figma Node 21:894) --}}
                                <!-- Show Recommendation Card if available and not yet rebooked (Figma Node 21:894) -->
                                <template x-if="(msg.showRecommendation || msg.type === 'options_list') && flightStatus === 'delayed'">
                                    @include('sections.flight-recommendation')
                                </template>

                                {{-- id: Tampilkan Kartu Sukses Rebooking (Figma Node 25:1210)
                                     en: Show Rebooking Success Card (Figma Node 25:1210) --}}
                                <!-- Show Success Card if rebooked (Figma Node 25:1210) -->
                                <template x-if="msg.showSuccess || msg.type === 'success_card' || (msg.showRecommendation && flightStatus === 'rebooked')">
                                    @include('sections.success-card')
                                </template>

                            </div>
                            
                            <span class="text-[9.5px] text-slate-400 font-medium mt-1 ml-1 block" x-text="msg.time"></span>
                        </div>
                    </div>
                </template>

                {{-- id: Baris Pesan dari Pengguna (User)
                     en: User Message Row --}}
                <!-- User Message Row -->
                <template x-if="msg.sender === 'user'">
                    <div class="flex items-start justify-end gap-2 sm:gap-2.5">
                        <div class="max-w-[420px]">
                            <div class="bg-brand-600 text-white rounded-lg rounded-tr-none p-2.5 sm:p-3 text-xs leading-relaxed shadow-xs">
                                <p x-text="lang === 'id' ? msg.textId : msg.textEn"></p>
                            </div>
                            <span class="text-[9.5px] text-slate-400 font-medium mt-1 mr-1 text-right block" x-text="msg.time"></span>
                        </div>

                        <div class="w-6 h-6 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5 shadow-2xs"
                             x-text="currentUser.initials">
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- id: Indikator AI Sedang Mengetik
             en: AI Typing Animation Indicator --}}
        <!-- AI Typing Indicator -->
        <div x-show="isTyping" x-cloak class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-blue-100 text-brand-600 flex items-center justify-center shrink-0 text-[10px]">
                <i class="fa-solid fa-robot text-[10px]"></i>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 flex items-center gap-1 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-bounce"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-bounce" style="animation-delay: 0.15s"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-bounce" style="animation-delay: 0.3s"></span>
            </div>
        </div>
    </div>

    {{-- id: Area Input Chat & Saran Pesan Mengambang di Bawah
         en: Floating Sticky Chat Bar & Context-Aware Prompt Suggestions --}}
    <!-- ================= STICKY FLOATING CHAT BAR & SUGGESTIONS ================= -->
    <div class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none pb-2 sm:pb-3 pt-4 bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC]/95 to-transparent">
        
        <div class="pointer-events-auto max-w-[620px] w-full mx-auto space-y-1.5">
            
            {{-- #BACKEND Saran Prompt Cerdas Kontekstual
                 id: dynamicSuggestions di-generate oleh AI Engine berdasarkan status penerbangan & tiket aktif
                 en: dynamicSuggestions generated by AI Engine based on flight status & active ticket context --}}
            <!-- Floating Context-Aware Suggestions Chips (Figma Nodes 15:816, 22:1109, 16:880) -->
            <div class="space-y-1" x-show="dynamicSuggestions && dynamicSuggestions.length > 0">
                <template x-for="(sug, index) in dynamicSuggestions.slice(0, 2)" :key="index">
                    <button @click="sendMessage(lang === 'id' ? sug.id : sug.en)"
                            class="w-full text-left py-1.5 px-2.5 bg-white/95 hover:bg-white backdrop-blur-md border border-slate-200 hover:border-brand-300 rounded-lg text-[10.5px] text-slate-700 hover:text-brand-700 transition flex items-center justify-between group shadow-2xs hover:shadow-xs cursor-pointer">
                        <span class="truncate pr-2 font-medium" x-text="lang === 'id' ? sug.id : sug.en"></span>
                        <i class="fa-solid fa-chevron-right text-[8.5px] text-slate-300 group-hover:text-brand-500 transition shrink-0"></i>
                    </button>
                </template>
            </div>

            {{-- id: Form Input Pesan Chat Pengguna
                 en: User Chat Message Input Form --}}
            <!-- Floating Sticky Chat Input Composer (Figma Node 16:884) -->
            <form @submit.prevent="sendMessage()" 
                  class="bg-white/95 backdrop-blur-md rounded-xl border border-slate-200 p-1 shadow-sm flex items-center gap-1.5 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-200 transition">
                
                <input type="text" 
                       x-model="chatInput"
                       :placeholder="lang === 'id' ? 'Tanyakan sesuatu tentang perjalanan Anda...' : 'Ask about your flight or rescheduling...'"
                       class="flex-1 bg-transparent border-0 px-2.5 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0 font-medium">

                <button type="submit"
                        :disabled="!chatInput || chatInput.trim() === ''"
                        :class="chatInput && chatInput.trim() !== '' ? 'bg-brand-600 hover:bg-brand-700 text-white cursor-pointer shadow-xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center transition shrink-0"
                        title="Kirim Pesan">
                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                </button>
            </form>
        </div>
    </div>

</div>
