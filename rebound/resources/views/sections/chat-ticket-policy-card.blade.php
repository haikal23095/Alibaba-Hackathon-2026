{{-- #BACKEND Kartu Kebijakan Tiket
     id: Data kebijakan tiket (ubah jadwal, biaya, selisih tarif) masih statis dari flight.original \u2014 harus dari database fare_rules berdasarkan PNR/booking
     en: Ticket policy data (change allowed, fees, fare diff) is still static from flight.original \u2014 must be from fare_rules database based on PNR/booking --}}
<!-- In-Chat Verified Ticket Rules Card (Figma Node 22:1109) -->
<div class="w-full bg-white rounded-lg border border-slate-200 p-4 shadow-xs my-3 text-left space-y-3">
    
    <!-- Top Header with Shield Icon & Airline Subtitle -->
    <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
        <div class="w-8 h-8 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center text-sm shrink-0 border border-brand-100">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h4 class="font-bold text-slate-900 text-sm"
                x-text="lang === 'id' ? 'Aturan Tiket Terverifikasi' : 'Verified Ticket Policy'"></h4>
            <p class="text-[11px] text-slate-500 font-medium"
               x-text="flight.original.airline + ' • ' + flight.original.class"></p>
        </div>
    </div>

    <!-- 4 Rule Attributes Rows -->
    <div class="space-y-2 text-xs">
        
        <!-- Row 1: Perubahan Jadwal -->
        <div class="flex items-center justify-between py-1 border-b border-slate-50">
            <span class="text-slate-600" x-text="lang === 'id' ? 'Perubahan jadwal' : 'Schedule change'"></span>
            <span class="font-bold text-amber-600" x-text="lang === 'id' ? 'Memproses' : 'Processing'"></span>
        </div>

        <!-- Row 2: Biaya Perubahan -->
        <div class="flex items-center justify-between py-1 border-b border-slate-50">
            <span class="text-slate-600" x-text="lang === 'id' ? 'Biaya perubahan' : 'Change fee'"></span>
            <span class="font-bold text-slate-900" x-text="flight.original.feeAmountId"></span>
        </div>

        <!-- Row 3: Selisih Tarif -->
        <div class="flex items-center justify-between py-1 border-b border-slate-50">
            <span class="text-slate-600" x-text="lang === 'id' ? 'Selisih tarif' : 'Fare difference'"></span>
            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold text-[10px] rounded"
                  x-text="flight.original.fareDiffId"></span>
        </div>

        <!-- Row 4: Perubahan Akibat Gangguan (Waiver Eligibility) -->
        <div class="flex items-center justify-between py-1">
            <span class="text-slate-600" x-text="lang === 'id' ? 'Perubahan akibat gangguan' : 'Disruption waiver'"></span>
            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-[10px] rounded flex items-center gap-1">
                <i class="fa-solid fa-circle-check text-[9px] text-emerald-600"></i>
                <span x-text="lang === 'id' ? 'Memenuhi syarat' : 'Eligible'"></span>
            </span>
        </div>

    </div>

    <!-- Explanatory Box -->
    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-[11px] text-slate-600 leading-relaxed">
        <p x-text="lang === 'id' ? 'Karena pembatalan penerbangan Anda sebelumnya, biaya perubahan akan dibebaskan sesuai kebijakan maskapai.' : 'Due to your flight disruption, rebooking fees are fully waived under airline disruption policy.'"></p>
    </div>

    <!-- Action Link / Button -->
    <button @click="activeSidebarTab = 'policy'; showToast(lang === 'id' ? 'Membuka rincian aturan tiket di sidebar...' : 'Opening ticket policy details in sidebar...')"
            class="w-full py-2 px-3 border border-slate-200 hover:border-brand-400 hover:bg-slate-50 text-brand-600 font-semibold text-xs rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
        <span x-text="lang === 'id' ? 'Lihat detail aturan tiket' : 'View ticket policy details'"></span>
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
    </button>

</div>
