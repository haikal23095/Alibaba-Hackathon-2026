{{-- #BACKEND Modal QR Code Boarding Pass Besar / Large Scannable QR Boarding Pass Modal
     id: Menampilkan QR code boarding pass resolusi tinggi untuk di-scan petugas di gate bandara.
         Payload QR code harus di-generate sesuai format IATA BCBP 2D Barcode standar.
     en: Displays high-resolution boarding pass QR code for airport gate scanning.
         QR code payload must be generated according to standard IATA BCBP 2D Barcode format. --}}
<!-- Large Scannable QR Boarding Pass Modal (Airport Gate Scanner Ready) -->
<div x-show="showQrModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showQrModal = false"
         class="bg-white rounded-xl max-w-xs w-full p-5 shadow-xl border border-slate-200 text-center space-y-3.5 relative overflow-hidden"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        {{-- id: Header Modal QR Boarding Pass
             en: QR Boarding Pass Modal Header --}}
        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-1.5 font-bold text-xs text-slate-900">
                <i class="fa-solid fa-qrcode text-brand-600"></i>
                <span x-text="lang === 'id' ? 'QR Boarding Pass' : 'Boarding Pass QR'"></span>
            </div>
            <button @click="showQrModal = false" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        {{-- #BACKEND Gambar QR Code Real
             id: Payload QR code saat ini menggunakan API eksternal (qrserver). Di produksi: generate internal SVG/PNG dari server Laravel menggunakan library bacon/bacon-qr-code.
             en: QR code payload currently uses external API (qrserver). In production: generate internal SVG/PNG from Laravel backend using bacon/bacon-qr-code library. --}}
        <!-- Real Large Scannable QR Code -->
        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 inline-block">
            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=' + encodeURIComponent('REBOUND AVIATION E-PASS\nPNR: GA-9821A\nPAX: ' + currentUser.passenger + '\nFLIGHT: ' + (flightStatus === 'rebooked' ? 'GA830' : 'GA826') + ' CGK->SIN\nGATE: ' + (flightStatus === 'rebooked' ? '4A' : '3B') + '\nSEAT: 14A\nZONE: 2\nSTATUS: CONFIRMED')" 
                 alt="Scannable Boarding QR"
                 class="w-40 h-40 mx-auto rounded bg-white p-1">
        </div>

        {{-- id: Ringkasan data penumpang & penerbangan di bawah QR
             en: Passenger & flight summary data below QR --}}
        <!-- Flight Summary -->
        <div class="text-left bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-1 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500" x-text="lang === 'id' ? 'Penumpang' : 'Passenger'"></span>
                <span class="font-semibold text-slate-900 truncate max-w-[140px]" x-text="currentUser.passenger"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500" x-text="lang === 'id' ? 'Penerbangan' : 'Flight'"></span>
                <span class="font-bold text-slate-900" x-text="(flightStatus === 'rebooked' ? 'GA830' : 'GA826') + ' (14A)'"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">PNR</span>
                <span class="font-mono font-bold text-brand-600">GA-9821A</span>
            </div>
        </div>

        {{-- id: Tombol tutup modal
             en: Close modal button --}}
        <!-- Close button -->
        <button @click="showQrModal = false" 
                class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-xs transition cursor-pointer">
            <span x-text="lang === 'id' ? 'Tutup' : 'Close'"></span>
        </button>

    </div>
</div>
