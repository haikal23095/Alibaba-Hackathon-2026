{{-- #BACKEND Rincian Aturan Kebijakan Tiket / Ticket Policy Details Component
     id: Menampilkan klausul perubahan tiket, biaya denda, selisih tarif, dan klausul gangguan dari database `fare_rules`.
     en: Displays ticket change clauses, penalty fees, fare differences, and disruption waivers from `fare_rules` database. --}}
<!-- Ticket Policy Details Component (Compact & Sleek) -->
<div class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs space-y-2.5">
    {{-- id: Header verifikasi kebijakan tiket
         en: Ticket policy verification header --}}
    <!-- Header -->
    <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded-md bg-blue-50 text-brand-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h4 class="font-bold text-slate-900 text-xs"
            x-text="lang === 'id' ? 'Aturan Tiket Terverifikasi' : 'Ticket Policy Verified'"></h4>
    </div>

    {{-- #BACKEND Tabel Aturan Tiket
         id: Data diambil dari `flight.original` yang di-query dari tabel `fare_rules` berdasarkan fare class/basis.
         en: Data sourced from `flight.original` queried from `fare_rules` table based on fare class/basis. --}}
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

    {{-- id: Kotak ringkasan penjelasan aturan tiket
         en: Ticket rules summary explanation box --}}
    <!-- Detail Box -->
    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-[10.5px] text-slate-600 leading-snug">
        <p x-text="lang === 'id' ? 'Tiket Ekonomi Anda mengizinkan perubahan jadwal penerbangan sebelum waktu keberangkatan dengan biaya standar.' : 'Your Economy ticket allows flight rebooking before departure time with standard fee.'"></p>
    </div>

    {{-- id: Tombol pintasan untuk tanya AI di chat mengenai detail kebijakan tiket
         en: Quick shortcut button to ask AI in chat regarding ticket policy details --}}
    <!-- Footer Action Link -->
    <button @click="sendMessage(lang === 'id' ? 'Tolong jelaskan rincian kebijakan tiket saya' : 'Please explain my ticket policy details in full')"
            class="text-[11px] font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition pt-0.5 cursor-pointer">
        <span x-text="lang === 'id' ? 'Lihat rincian kebijakan' : 'View full policy details'"></span>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
    </button>
</div>
