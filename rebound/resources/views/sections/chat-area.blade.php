<!-- Chat Conversation & Input Area (Figma Nodes 3:158, 3:159, 15:816, 16:873, 16:884) -->
<div class="flex-1 flex flex-col justify-between max-w-[620px] w-full mx-auto pb-4">
    
    <!-- Messages Scrollable Container -->
    <div class="flex-1 overflow-y-auto pr-1 space-y-5 pb-6">
        
        <!-- Active Trip Card at the top of workspace -->
        @include('sections.active-trip-card')

        <!-- Dynamic Message History -->
        <template x-for="(msg, index) in messages" :key="index">
            <div class="w-full">
                <!-- AI Message Row -->
                <template x-if="msg.sender === 'ai'">
                    <div class="flex items-start gap-3.5">
                        <!-- AI Bot Icon Avatar -->
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-brand-600 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-robot text-sm"></i>
                        </div>

                        <!-- Message Body -->
                        <div class="flex-1 max-w-[520px]">
                            <div class="bg-white rounded-2xl rounded-tl-sm border border-slate-200/80 p-4 sm:p-5 text-sm text-slate-700 leading-relaxed chat-bubble-shadow">
                                <p x-text="lang === 'id' ? msg.textId : msg.textEn"></p>

                                <!-- Show Recommendation Card if available and not yet rebooked -->
                                <template x-if="msg.showRecommendation && flightStatus === 'delayed'">
                                    @include('sections.flight-recommendation')
                                </template>

                                <!-- Show Success Card if rebooked -->
                                <template x-if="msg.showSuccess || (msg.showRecommendation && flightStatus === 'rebooked')">
                                    @include('sections.success-card')
                                </template>
                            </div>
                            
                            <span class="text-[10px] text-slate-400 font-medium mt-1 ml-1 block" x-text="msg.time"></span>
                        </div>
                    </div>
                </template>

                <!-- User Message Row -->
                <template x-if="msg.sender === 'user'">
                    <div class="flex items-start justify-end gap-3">
                        <div class="max-w-[420px]">
                            <div class="bg-brand-600 text-white rounded-2xl rounded-tr-sm p-3.5 sm:p-4 text-sm leading-relaxed shadow-sm">
                                <p x-text="lang === 'id' ? msg.textId : msg.textEn"></p>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium mt-1 mr-1 text-right block" x-text="msg.time"></span>
                        </div>

                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-sm"
                             x-text="currentUser.initials">
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- AI Typing Indicator -->
        <div x-show="isTyping" x-cloak class="flex items-center gap-3.5">
            <div class="w-8 h-8 rounded-full bg-blue-100 text-brand-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-robot text-sm"></i>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-3.5 flex items-center gap-1.5 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-bounce"></span>
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-bounce" style="animation-delay: 0.15s"></span>
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-bounce" style="animation-delay: 0.3s"></span>
            </div>
        </div>
    </div>

    <!-- Bottom Input & Suggestions Area (Figma Node 16:873) -->
    <div class="shrink-0 pt-2 bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC] to-transparent">
        
        <!-- Suggestions Chips (Figma Nodes 15:816, 16:874, 16:880, 13:638) -->
        <div class="mb-3.5 space-y-2">
            <div class="text-xs font-bold text-brand-600 tracking-wide uppercase flex items-center gap-1.5">
                <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
                <span x-text="lang === 'id' ? 'Saran tentang apa yang harus ditanyakan kepada AI Kami' : 'Suggestions on what to ask Our AI'"></span>
            </div>

            <div class="space-y-2">
                <!-- Suggestion 1 -->
                <button @click="sendMessage(lang === 'id' ? 'Tanyakan tentang penerbangan atau perubahan jadwal...' : 'Ask about flight status or rescheduling options...')"
                        class="w-full text-left p-3 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-2xl text-xs sm:text-sm text-slate-600 hover:text-slate-900 transition flex items-center justify-between group shadow-sm hover:border-slate-300">
                    <span x-text="lang === 'id' ? 'Tanyakan tentang penerbangan atau perubahan jadwal...' : 'Ask about flight status or rescheduling options...'"></span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-brand-500 transition"></i>
                </button>

                <!-- Suggestion 2 -->
                <button @click="sendMessage(lang === 'id' ? 'Tanyakan tentang kondisi cuaca di jadwal penerbangan anda..' : 'Ask about weather conditions affecting your flight..')"
                        class="w-full text-left p-3 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-2xl text-xs sm:text-sm text-slate-600 hover:text-slate-900 transition flex items-center justify-between group shadow-sm hover:border-slate-300">
                    <span x-text="lang === 'id' ? 'Tanyakan tentang kondisi cuaca di jadwal penerbangan anda..' : 'Ask about weather conditions affecting your flight..'"></span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-brand-500 transition"></i>
                </button>
            </div>
        </div>

        <!-- Chat Input Field (Figma Node 16:884) -->
        <form @submit.prevent="sendMessage()" 
              class="bg-white rounded-2xl border border-slate-200/90 p-1.5 sm:p-2 shadow-sm flex items-center gap-2 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100 transition">
            
            <input type="text" 
                   x-model="chatInput"
                   :placeholder="lang === 'id' ? 'Tanyakan sesuatu tentang perjalanan Anda...' : 'Ask about your flight or rescheduling...'"
                   class="flex-1 bg-transparent border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0">

            <button type="submit"
                    :disabled="!chatInput || chatInput.trim() === ''"
                    :class="chatInput && chatInput.trim() !== '' ? 'bg-brand-600 hover:bg-brand-700 text-white cursor-pointer shadow-sm' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition shrink-0"
                    title="Send Message">
                <i class="fa-solid fa-paper-plane text-sm"></i>
            </button>
        </form>
    </div>
</div>
