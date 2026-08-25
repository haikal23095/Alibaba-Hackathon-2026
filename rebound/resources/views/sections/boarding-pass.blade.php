<!-- Ultra-Compact Aviation E-Boarding Pass (Zoom-Out Fitted Layout with Real Scannable QR & Dual Downloads) -->
<div class="w-full bg-white rounded-lg border border-slate-200 shadow-2xs overflow-hidden text-[10px] relative select-none">
    
    <!-- Top Airline Color Bar (Garuda Navy/Teal) -->
    <div class="bg-gradient-to-r from-[#0B3B60] to-[#0A548A] text-white py-1.5 px-2.5 flex items-center justify-between">
        <div class="flex items-center gap-1.5">
            <div class="w-4 h-4 rounded bg-white/15 flex items-center justify-center text-[8px] backdrop-blur-xs">
                <i class="fa-solid fa-plane text-sky-200"></i>
            </div>
            <div>
                <h5 class="font-bold text-[11px] leading-tight" x-text="flightStatus === 'rebooked' ? 'Garuda Indonesia' : flight.original.airline"></h5>
                <p class="text-[8px] text-sky-200/90 font-mono" x-text="flightStatus === 'rebooked' ? 'GA830 • B737-800' : (flight.original.flightNumber + ' • A330')"></p>
            </div>
        </div>

        <span class="px-1.5 py-0.2 bg-emerald-400/20 border border-emerald-300/40 text-emerald-300 text-[8px] font-bold uppercase tracking-wider rounded">
            <span x-text="flightStatus === 'rebooked' ? (lang === 'id' ? 'BOARDING' : 'BOARDING') : (lang === 'id' ? 'TERBIT' : 'ISSUED')"></span>
        </span>
    </div>

    <!-- Main Boarding Body (Ultra Compact) -->
    <div class="p-2 space-y-1.5 bg-white">
        
        <!-- Origin to Destination Route -->
        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
            <div>
                <span class="text-[7.5px] text-slate-400 font-bold uppercase block" x-text="lang === 'id' ? 'DARI' : 'FROM'"></span>
                <span class="text-xs font-black text-slate-900 tracking-tight" x-text="flight.original.fromCode"></span>
                <span class="text-[8px] text-slate-500 block font-medium">Jakarta (CGK)</span>
            </div>

            <!-- Plane Route Icon -->
            <div class="flex flex-col items-center px-1">
                <span class="text-[7.5px] font-bold text-emerald-600 mb-0.2" x-text="lang === 'id' ? 'Langsung' : 'Non-Stop'"></span>
                <div class="flex items-center gap-0.5">
                    <div class="w-4 h-0.5 bg-slate-200"></div>
                    <i class="fa-solid fa-plane text-brand-600 text-[8px]"></i>
                    <div class="w-4 h-0.5 bg-slate-200"></div>
                </div>
                <span class="text-[7.5px] text-slate-400 font-mono">1h 45m</span>
            </div>

            <div class="text-right">
                <span class="text-[7.5px] text-slate-400 font-bold uppercase block" x-text="lang === 'id' ? 'KE' : 'TO'"></span>
                <span class="text-xs font-black text-slate-900 tracking-tight" x-text="flight.original.toCode"></span>
                <span class="text-[8px] text-slate-500 block font-medium" x-text="lang === 'id' ? 'Singapura (SIN)' : 'Singapore (SIN)'"></span>
            </div>
        </div>

        <!-- 4-Column Boarding Telemetry Grid (Fitted) -->
        <div class="grid grid-cols-4 gap-0.5 p-1 bg-slate-50 rounded-md border border-slate-100 text-center">
            <!-- Gate -->
            <div class="border-r border-slate-200/80 pr-0.5">
                <span class="text-[7.5px] font-bold text-slate-400 uppercase block" x-text="lang === 'id' ? 'GATE' : 'GATE'"></span>
                <span class="text-[10px] font-black text-brand-600" x-text="flightStatus === 'rebooked' ? '4A' : '3B'"></span>
            </div>
            <!-- Boarding Time -->
            <div class="border-r border-slate-200/80 pr-0.5">
                <span class="text-[7.5px] font-bold text-slate-400 uppercase block" x-text="lang === 'id' ? 'BOARDING' : 'BOARDING'"></span>
                <span class="text-[9.5px] font-bold text-slate-900" x-text="flightStatus === 'rebooked' ? '12:00' : '08:50'"></span>
            </div>
            <!-- Seat -->
            <div class="border-r border-slate-200/80 pr-0.5">
                <span class="text-[7.5px] font-bold text-slate-400 uppercase block" x-text="lang === 'id' ? 'KURSI' : 'SEAT'"></span>
                <span class="text-[9.5px] font-black text-slate-900">14A</span>
            </div>
            <!-- Zone -->
            <div>
                <span class="text-[7.5px] font-bold text-slate-400 uppercase block">ZONE</span>
                <span class="text-[9.5px] font-bold text-slate-900">2</span>
            </div>
        </div>

        <!-- Baggage Transfer Guarantee Indicator -->
        <div class="flex items-center justify-between px-1.5 py-0.5 bg-emerald-50/80 border border-emerald-200/60 rounded text-[8.5px]">
            <div class="flex items-center gap-1 text-emerald-800 font-semibold truncate">
                <i class="fa-solid fa-suitcase-rolling text-emerald-600 text-[8.5px]"></i>
                <span>Bag Tag #GA-489102</span>
            </div>
            <span class="text-emerald-700 font-bold text-[7.5px] uppercase shrink-0" x-text="lang === 'id' ? 'Auto-Teralihkan' : 'Auto-Transferred'"></span>
        </div>
    </div>

    <!-- ================= PERFORATED TEAR-OFF LINE ================= -->
    <div class="relative w-full py-0 bg-white flex items-center justify-center">
        <!-- Left Notch Cutout -->
        <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-[#F8FAFC] rounded-full border-r border-slate-200"></div>
        <!-- Dashed Cutout Line -->
        <div class="w-full mx-2.5 border-t border-dashed border-slate-200"></div>
        <!-- Right Notch Cutout -->
        <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-[#F8FAFC] rounded-full border-l border-slate-200"></div>
    </div>

    <!-- Lower Stub (Passenger Info, E-Ticket & Barcode) -->
    <div class="p-2 pt-1 bg-white space-y-1.5">
        
        <!-- Passenger & PNR Details -->
        <div class="grid grid-cols-2 gap-1 text-[9px]">
            <div>
                <span class="text-[7.5px] text-slate-400 font-bold uppercase block" x-text="lang === 'id' ? 'NAMA PENUMPANG' : 'PASSENGER'"></span>
                <span class="font-bold text-slate-900 truncate block" x-text="currentUser.passenger"></span>
            </div>
            <div class="text-right">
                <span class="text-[7.5px] text-slate-400 font-bold uppercase block">PNR / ETKT</span>
                <span class="font-mono font-bold text-slate-900">GA-9821A</span>
            </div>
        </div>

        <!-- Tariff & Penalty Waiver Info -->
        <div class="flex items-center justify-between text-[8.5px] py-0.5 px-1.5 bg-slate-50 rounded border border-slate-100 font-mono">
            <span class="text-slate-500" x-text="lang === 'id' ? 'Penalti' : 'Fee'"></span>
            <span class="font-bold text-emerald-600" x-text="lang === 'id' ? 'Rp 0 (Bebas Biaya / Waiver 72A)' : '$0 (Disruption Waiver 72A)'"></span>
        </div>

        <!-- Real Scannable Barcode & Real Scannable QR Code -->
        <div class="flex items-center justify-between gap-1.5 pt-0.5 border-t border-slate-100">
            <!-- REAL 100% SCANNABLE CODE-128 BARCODE (JsBarcode & Airport Scanner Ready) -->
            <div class="flex-1 overflow-hidden flex items-center justify-center py-0.5" 
                 x-init="$nextTick(() => renderBarcode())" 
                 :key="flightStatus + selectedTicketId + (currentUser ? currentUser.id : 1)">
                <svg id="live-boarding-barcode" class="w-full h-4.5"></svg>
            </div>

            <!-- REAL SCANNABLE QR CODE (Clickable to Enlarge & Airport Scanner Ready) -->
            <div class="relative group cursor-pointer" @click="showQrModal = true" title="Scan QR / Klik untuk perbesar">
                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=100x100&margin=0&data=' + encodeURIComponent('REBOUND PASS: PNR GA-9821A | PAX ' + currentUser.passenger + ' | FLIGHT ' + (flightStatus === 'rebooked' ? 'GA830' : 'GA826') + ' CGK->SIN | GATE: ' + (flightStatus === 'rebooked' ? '4A' : '3B') + ' | SEAT 14A | STATUS: CONFIRMED')"
                     alt="Scannable QR Code"
                     class="w-6.5 h-6.5 p-0.5 bg-white rounded border border-slate-300 shadow-2xs hover:scale-110 transition duration-150 object-contain">
            </div>
        </div>

        <!-- DUAL DISTINCT ACTIONS: PDF TICKET & DIGITAL WALLET (ANDROID / IOS) -->
        <div class="grid grid-cols-2 gap-1.5 pt-0.5">
            <!-- 1. Download Official PDF Ticket -->
            <button @click="downloadPdf()"
                    :disabled="isDownloadingPdf"
                    class="py-1.5 px-1.5 bg-brand-600 hover:bg-brand-700 active:scale-[0.98] text-white rounded-md font-bold text-[9px] transition flex items-center justify-center gap-1 cursor-pointer shadow-2xs">
                <template x-if="!isDownloadingPdf">
                    <span class="flex items-center gap-1 truncate">
                        <i class="fa-solid fa-file-pdf text-[10px]"></i>
                        <span x-text="lang === 'id' ? 'Unduh PDF' : 'Download PDF'"></span>
                    </span>
                </template>
                <template x-if="isDownloadingPdf">
                    <span class="flex items-center gap-1 text-sky-200">
                        <i class="fa-solid fa-circle-notch animate-spin text-[9px]"></i>
                        <span x-text="lang === 'id' ? 'Membuat PDF...' : 'Creating PDF...'"></span>
                    </span>
                </template>
            </button>

            <!-- 2. Save to Digital Wallet (Google Wallet / Apple Wallet / Gallery) -->
            <button @click="showWalletModal = true"
                    class="py-1.5 px-1.5 bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white rounded-md font-bold text-[9px] transition flex items-center justify-center gap-1 cursor-pointer shadow-2xs"
                    :title="lang === 'id' ? 'Pilih Google Wallet (Android) atau Apple Wallet (iOS)' : 'Save to Google Wallet (Android) or Apple Wallet (iOS)'">
                <span class="flex items-center gap-1 truncate">
                    <i class="fa-solid fa-mobile-screen text-[9.5px]"></i>
                    <span x-text="lang === 'id' ? 'Simpan ke Wallet' : 'Save to Wallet'"></span>
                </span>
            </button>
        </div>

    </div>
</div>
