<!-- Ticket Policy Details Component (Compact & Sleek) -->
<div class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs space-y-2.5">
    <!-- Header -->
    <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded-md bg-blue-50 text-brand-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h4 class="font-bold text-slate-900 text-xs"
            x-text="lang === 'id' ? 'Aturan Tiket Terverifikasi' : 'Ticket Policy Verified'"></h4>
    </div>

    <!-- Rules Table -->
    <div class="space-y-1.5 text-[11px]">
        <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
            <span class="text-slate-500" x-text="lang === 'id' ? 'Perubahan jadwal' : 'Change allowed'"></span>
            <span class="font-semibold text-slate-900" x-text="lang === 'id' ? flight.original.changeAllowedId : flight.original.changeAllowedEn"></span>
        </div>

        <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
            <span class="text-slate-500" x-text="lang === 'id' ? 'Biaya perubahan' : 'Fee amount'"></span>
            <span class="font-semibold text-slate-900" x-text="lang === 'id' ? flight.original.feeAmountId : flight.original.feeAmountEn"></span>
        </div>

        <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
            <span class="text-slate-500" x-text="lang === 'id' ? 'Selisih tarif' : 'Fare diff.'"></span>
            <span class="inline-block px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded font-semibold text-[10px]"
                  x-text="lang === 'id' ? flight.original.fareDiffId : flight.original.fareDiffEn"></span>
        </div>

        <div class="flex items-center justify-between py-0.5">
            <span class="text-slate-500" x-text="lang === 'id' ? 'Perubahan akibat gangguan' : 'Disruption waiver'"></span>
            <span class="font-bold text-brand-600" x-text="lang === 'id' ? 'Memenuhi syarat' : 'Eligible / Free'"></span>
        </div>
    </div>

    <!-- Detail Box -->
    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-[10.5px] text-slate-600 leading-snug">
        <p x-text="lang === 'id' ? 'Tiket Ekonomi Anda mengizinkan perubahan jadwal penerbangan sebelum waktu keberangkatan dengan biaya standar.' : 'Your Economy ticket allows flight rebooking before departure time with standard fee.'"></p>
    </div>

    <!-- Footer Action Link -->
    <button @click="sendMessage(lang === 'id' ? 'Tolong jelaskan rincian kebijakan tiket saya' : 'Please explain my ticket policy details in full')"
            class="text-[11px] font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition pt-0.5">
        <span x-text="lang === 'id' ? 'Lihat rincian kebijakan' : 'View full policy details'"></span>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
    </button>
</div>
