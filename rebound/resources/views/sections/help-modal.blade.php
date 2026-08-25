<!-- Help & Aviation FAQ Guide Modal -->
<div x-show="showHelpModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-250"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showHelpModal = false"
         class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 text-left relative overflow-hidden"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-base shrink-0 shadow-xs border border-brand-100">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-tight"
                        x-text="lang === 'id' ? 'Pusat Bantuan & Panduan Penumpang' : 'Passenger Help & Disruption Guide'"></h3>
                    <p class="text-[11px] text-slate-500 font-medium"
                       x-text="lang === 'id' ? 'Panduan regulasi aviasi, hak kompensasi & rebooking' : 'Aviation rules, compensation rights & smart rebooking'"></p>
                </div>
            </div>

            <button @click="showHelpModal = false" 
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- FAQ Items Accordion / Cards -->
        <div class="space-y-2.5 max-h-[60vh] overflow-y-auto custom-scrollbar pr-1 text-xs">
            
            <!-- FAQ 1: How Rebooking Works -->
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <i class="fa-solid fa-bolt text-brand-600"></i>
                    <span x-text="lang === 'id' ? 'Bagaimana cara kerja Rebooking Otomatis REBOUND?' : 'How does REBOUND Smart Rebooking work?'"></span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed"
                   x-text="lang === 'id' ? 'Sistem REBOUND terhubung langsung ke GDS maskapai penerbangan. Ketika terjadi keterlambatan atau pembatalan, AI secara otomatis mencarikan rute tercepat yang tersedia dan mengalihkan kursi Anda tanpa perlu antre di loket bandara.' : 'REBOUND links directly to airline GDS systems. When delays or cancellations occur, AI instantly computes the fastest alternative flight and rebooks your seat without waiting at airport counters.'"></p>
            </div>

            <!-- FAQ 2: Zero Fee Waiver Policy -->
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <i class="fa-solid fa-scale-balanced text-emerald-600"></i>
                    <span x-text="lang === 'id' ? 'Apakah ada biaya tambahan untuk perubahan jadwal?' : 'Are there any fees for rescheduling?'"></span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed"
                   x-text="lang === 'id' ? 'Tidak ada biaya sama sekali (Rp 0). Berdasarkan regulasi Permenhub PM 89/2015 dan Klausul Disruption Waiver Rule 72A, seluruh penalti perubahan dibebaskan 100% saat penerbangan terganggu oleh maskapai/cuaca.' : 'Zero fee ($0). Under Aviation Tariff Disruption Rules (Waiver 72A) and Passenger Protection Regulations, 100% of rebooking fees and fare differences are fully waived.'"></p>
            </div>

            <!-- FAQ 3: Baggage Continuity -->
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <i class="fa-solid fa-suitcase-rolling text-sky-600"></i>
                    <span x-text="lang === 'id' ? 'Bagaimana dengan bagasi tercatat saya?' : 'What happens to my checked baggage?'"></span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed"
                   x-text="lang === 'id' ? 'Tag bagasi Anda (misal #GA-489102) akan secara otomatis diperbarui di sistem penanganan bagasi ground handler bandara dan dipindahkan ke pesawat baru.' : 'Your baggage tag (#GA-489102) is automatically updated across airport ground handling systems and redirected to your new aircraft.'"></p>
            </div>

            <!-- Emergency Dispatch Contact Box -->
            <div class="p-3 rounded-lg bg-blue-50 border border-brand-200 text-[11px] flex items-center justify-between">
                <div class="flex items-center gap-2 text-brand-900">
                    <i class="fa-solid fa-headset text-brand-600 text-sm"></i>
                    <div>
                        <span class="font-bold block" x-text="lang === 'id' ? 'Bantuan Operasional Darurat' : '24/7 Airline Emergency Dispatch'"></span>
                        <span class="text-slate-500 text-[10.5px]">Hotline: +62 21 2351 9999 (Toll Free)</span>
                    </div>
                </div>
                <a href="tel:+622123519999" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-[10.5px] transition shadow-xs">
                    <span x-text="lang === 'id' ? 'Hubungi' : 'Call'"></span>
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div class="pt-2 flex justify-end">
            <button @click="showHelpModal = false" 
                    class="py-2 px-5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition cursor-pointer"
                    x-text="lang === 'id' ? 'Tutup Panduan' : 'Close Guide'">
            </button>
        </div>

    </div>
</div>
