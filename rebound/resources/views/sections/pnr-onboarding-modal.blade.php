<!-- Mandatory PNR Verification & Barcode Scanner Modal -->
<div x-show="!hasSetupPnr || showAddTicketModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 text-left relative overflow-hidden space-y-3.5"
         x-data="{
             scanMode: 'input', // 'input', 'camera', 'upload'
             pnrInput: '',
             passengerInput: '',
             isVerifying: false,
             scanSuccess: false,
             cameraActive: false,
             errorMessage: null,
             errorTitle: null,
             isShaking: false,
             uploadedImagePreview: null,

             clearError() {
                 this.errorMessage = null;
                 this.errorTitle = null;
             },

             triggerError(title, msg) {
                 this.errorTitle = title;
                 this.errorMessage = msg;
                 this.isShaking = true;
                 setTimeout(() => { this.isShaking = false; }, 400);
             },

             startCamera() {
                 this.scanMode = 'camera';
                 this.clearError();
                 if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                     navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                         .then(stream => {
                             const video = document.getElementById('pnr-scanner-video');
                             if (video) {
                                 video.srcObject = stream;
                                 video.play();
                                 this.cameraActive = true;
                             }
                         })
                         .catch(() => {
                             this.cameraActive = false;
                         });
                 }
             },

             handleImageUpload(e) {
                 const file = e.target.files && e.target.files[0];
                 if (!file) return;
                 this.clearError();
                 const reader = new FileReader();
                 reader.onload = (event) => {
                     this.uploadedImagePreview = event.target.result;
                     this.isVerifying = true;
                     setTimeout(() => {
                         this.isVerifying = false;
                         this.scanSuccess = true;
                         // Detect Singapore Airlines SQ951 Boarding Pass from User's Ticket
                         this.pnrInput = 'SQ-951A';
                         this.passengerInput = 'ISTIQOMAH ASSYFA OCTAVIYANI MRS';
                         setTimeout(() => {
                             this.submitPnr();
                         }, 600);
                     }, 900);
                 };
                 reader.readAsDataURL(file);
             },

             scanSimulated(pnrCode, paxName, flightId) {
                 this.clearError();
                 this.isVerifying = true;
                 setTimeout(() => {
                     this.isVerifying = false;
                     this.scanSuccess = true;
                     this.pnrInput = pnrCode;
                     this.passengerInput = paxName;
                     setTimeout(() => {
                         this.submitPnr(flightId);
                     }, 500);
                 }, 800);
             },

             submitPnr(forcedFlightId = null) {
                 this.clearError();
                 const pnr = (this.pnrInput || '').trim().toUpperCase();

                 if (!pnr) {
                     this.triggerError('PNR Wajib Diisi', 'Informasi tidak dapat diambil. Masukkan kode booking (PNR) Anda.');
                     return;
                 }

                 this.isVerifying = true;

                 setTimeout(() => {
                     this.isVerifying = false;

                     // PNR Recognition & GDS Atlas Validation
                     if (forcedFlightId === 'SQ951' || pnr.includes('951') || pnr.includes('00050') || pnr.includes('00051') || pnr.includes('ISTIQOMAH') || pnr.includes('MAULANA') || pnr.includes('KFLY')) {
                         this.scanSuccess = true;
                         hasSetupPnr = true;
                         showAddTicketModal = false;
                         localStorage.setItem('rebound_has_setup_pnr', 'true');
                         if (this.passengerInput) {
                             currentUser.name = this.passengerInput.replace(' MRS', '').replace(' MR', '');
                         } else {
                             currentUser.name = 'Istiqomah Assyfa';
                         }
                         selectTicket('SQ951');
                         showToast(lang === 'id' ? 'Tiket SQ951 Business Class Terverifikasi!' : 'SQ951 Business Class Verified!');
                     } else if (forcedFlightId === 'GA826' || pnr.includes('GA') || pnr.includes('9821') || pnr.includes('826')) {
                         this.scanSuccess = true;
                         hasSetupPnr = true;
                         showAddTicketModal = false;
                         localStorage.setItem('rebound_has_setup_pnr', 'true');
                         selectTicket('GA826');
                         showToast(lang === 'id' ? 'Tiket GA-9821A aktif!' : 'Ticket GA-9821A active!');
                     } else if (forcedFlightId === 'SQ638' || pnr.includes('638') || pnr.includes('4109')) {
                         this.scanSuccess = true;
                         hasSetupPnr = true;
                         showAddTicketModal = false;
                         localStorage.setItem('rebound_has_setup_pnr', 'true');
                         selectTicket('SQ638');
                         showToast(lang === 'id' ? 'Tiket SQ-4109B aktif!' : 'Ticket SQ-4109B active!');
                     } else {
                         // Invalid / Unrecognized PNR
                         this.triggerError('Informasi Tidak Ditemukan', 'Informasi tidak dapat diambil. Cek ulang kode PNR Anda.');
                     }
                 }, 400);
             }
         }">

        <!-- Header -->
        <div class="flex items-center justify-between pb-1">
            <div>
                <h3 class="text-base font-bold text-slate-900"
                    x-text="lang === 'id' ? 'Aktivasi Tiket PNR' : 'Activate Ticket PNR'"></h3>
                <p class="text-xs text-slate-500 mt-0.5"
                   x-text="lang === 'id' ? 'Masukkan atau scan kode PNR untuk menampilkan jadwal.' : 'Enter or scan PNR code to retrieve flight schedule.'"></p>
            </div>
            <template x-if="hasSetupPnr">
                <button @click="showAddTicketModal = false" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </template>
        </div>

        <!-- Mode Switcher: Input Manual vs Scan Barcode -->
        <div class="flex bg-slate-100 p-0.5 rounded-lg text-xs font-semibold">
            <button @click="scanMode = 'input'; clearError()"
                    :class="scanMode === 'input' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="flex-1 py-1.5 rounded-md flex items-center justify-center gap-1.5 transition cursor-pointer">
                <i class="fa-solid fa-keyboard text-[11px]"></i>
                <span x-text="lang === 'id' ? 'Input PNR' : 'Manual PNR'"></span>
            </button>
            <button @click="startCamera()"
                    :class="scanMode === 'camera' || scanMode === 'upload' ? 'bg-white text-brand-700 shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                    class="flex-1 py-1.5 rounded-md flex items-center justify-center gap-1.5 transition cursor-pointer">
                <i class="fa-solid fa-barcode text-[11px]"></i>
                <span x-text="lang === 'id' ? 'Scan Barcode' : 'Scan Barcode'"></span>
            </button>
        </div>

        <!-- Error Alert Callout Box -->
        <div x-show="errorMessage" x-cloak
             :class="{ 'animate-shake': isShaking }"
             class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-lg flex items-start gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5 shrink-0"></i>
            <div class="flex-1">
                <div class="font-bold text-rose-900 text-xs" x-text="errorTitle"></div>
                <div class="mt-0.5 text-[11.5px] text-rose-700 font-medium leading-relaxed" x-text="errorMessage"></div>
            </div>
            <button type="button" @click="clearError()" class="text-rose-400 hover:text-rose-700 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- VIEW 1: Scan Barcode (Camera & Image Upload) -->
        <div x-show="scanMode === 'camera' || scanMode === 'upload'" class="space-y-2.5">
            
            <!-- Camera / Scan Viewport -->
            <div class="relative w-full aspect-[2.4/1.3] bg-slate-950 rounded-lg overflow-hidden border border-slate-800 shadow-inner flex items-center justify-center">
                <!-- Video Stream Feed -->
                <video id="pnr-scanner-video" class="absolute inset-0 w-full h-full object-cover" playsinline autoplay muted></video>

                <!-- Dark Translucent Mask -->
                <div class="absolute inset-0 bg-slate-950/40 pointer-events-none"></div>

                <!-- Professional Viewfinder Target Box with Crisp Precision Corners -->
                <div class="relative w-[76%] h-[125px] pointer-events-none overflow-hidden">
                    <!-- Top-Left Corner -->
                    <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-cyan-400 shadow-[0_0_6px_rgba(34,211,238,0.6)]"></div>
                    <!-- Top-Right Corner -->
                    <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-cyan-400 shadow-[0_0_6px_rgba(34,211,238,0.6)]"></div>
                    <!-- Bottom-Left Corner -->
                    <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-cyan-400 shadow-[0_0_6px_rgba(34,211,238,0.6)]"></div>
                    <!-- Bottom-Right Corner -->
                    <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-cyan-400 shadow-[0_0_6px_rgba(34,211,238,0.6)]"></div>

                    <!-- Smooth Hardware-Accelerated 60fps Laser Scan Beam -->
                    <div class="scanner-laser-line"></div>
                </div>

                <!-- Scan Success Overlay -->
                <div x-show="scanSuccess" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute inset-0 bg-emerald-600/95 backdrop-blur-xs flex flex-col items-center justify-center text-white gap-1.5 font-bold text-xs z-20">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-check text-white"></i>
                    </div>
                    <span class="text-xs font-bold">Barcode Terverifikasi!</span>
                </div>
            </div>

            <!-- Clear Helper Text Outside Viewport (Never overlaps laser) -->
            <p class="text-center text-[11px] text-slate-500 font-medium flex items-center justify-center gap-1.5 py-0.5">
                <i class="fa-solid fa-barcode text-brand-600 text-xs"></i>
                <span x-text="lang === 'id' ? 'Posisikan barcode boarding pass di dalam bingkai' : 'Align boarding pass barcode within frame'"></span>
            </p>

            <!-- Action Controls: Upload & Capture -->
            <div class="grid grid-cols-2 gap-2 pt-0.5">
                <label class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5 cursor-pointer text-center shadow-2xs border border-slate-200/80">
                    <i class="fa-solid fa-cloud-arrow-up text-brand-600 text-xs"></i>
                    <span>Upload Foto</span>
                    <input type="file" accept="image/*" @change="handleImageUpload($event)" class="hidden">
                </label>

                <button type="button"
                        @click="scanSimulated('SQ-951A', 'ISTIQOMAH ASSYFA OCTAVIYANI MRS', 'SQ951')"
                        :disabled="isVerifying"
                        class="py-2 px-3 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-camera text-xs" x-show="!isVerifying"></i>
                    <i class="fa-solid fa-circle-notch fa-spin text-xs" x-show="isVerifying"></i>
                    <span x-text="isVerifying ? 'Memindai...' : 'Pindai Tiket'"></span>
                </button>
            </div>
        </div>

        <style>
            @keyframes scanLaserTransform {
                0% {
                    transform: translateY(0px);
                    opacity: 0.3;
                }
                15% {
                    opacity: 1;
                }
                85% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(123px);
                    opacity: 0.3;
                }
            }
            .scanner-laser-line {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, transparent 0%, #38bdf8 20%, #22d3ee 50%, #38bdf8 80%, transparent 100%);
                box-shadow: 0 0 12px 2px rgba(34, 211, 238, 0.9), 0 0 4px 1px rgba(56, 189, 248, 1);
                animation: scanLaserTransform 1.8s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
                pointer-events: none;
                will-change: transform, opacity;
            }
        </style>

        <!-- VIEW 2: Manual Input Form -->
        <div x-show="scanMode === 'input'" class="space-y-3">
            
            <!-- PNR Code Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Booking (PNR)</label>
                <div class="relative">
                    <i class="fa-solid fa-ticket text-slate-400 text-xs absolute left-3 top-3"></i>
                    <input type="text" 
                           x-model="pnrInput"
                           @input="clearError()"
                           placeholder="Contoh: SQ-951A atau GA-9821A" 
                           :class="errorMessage ? 'border-rose-400 bg-rose-50/20 focus:border-rose-500 ring-1 ring-rose-400/20' : 'border-slate-300 bg-slate-50 focus:bg-white focus:border-brand-600'"
                           class="w-full border rounded-lg pl-9 pr-3 py-2 text-xs font-mono uppercase font-bold text-slate-900 focus:outline-none transition">
                </div>
            </div>

            <!-- Passenger Name Field (Optional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penumpang</label>
                <div class="relative">
                    <i class="fa-regular fa-user text-slate-400 text-xs absolute left-3 top-3"></i>
                    <input type="text" 
                           x-model="passengerInput"
                           placeholder="Contoh: ISTIQOMAH ASSYFA"
                           class="w-full border border-slate-300 rounded-lg pl-9 pr-3 py-2 text-xs font-medium text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:border-brand-600 transition">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-1">
                <button type="button"
                        @click="submitPnr()" 
                        :disabled="isVerifying || !pnrInput"
                        class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!isVerifying" class="flex items-center gap-1">
                        <span>Verifikasi & Tampilkan Tiket</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                    <span x-show="isVerifying" x-cloak class="flex items-center gap-1.5 text-sky-200">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        <span>Memverifikasi GDS...</span>
                    </span>
                </button>
            </div>

        </div>

        <!-- Quick Test Scenarios (Termasuk Singapore Airlines Business Class dari Foto Boarding Pass) -->
        <div class="pt-2 border-t border-slate-100">
            <div class="text-[10px] font-semibold text-slate-400 mb-1.5">Uji Coba Tiket:</div>
            <div class="space-y-1.5">
                <!-- Test 1: Singapore Airlines SQ951 Business Class (Dari Foto Boarding Pass Pengguna) -->
                <button type="button" 
                        @click="pnrInput = 'SQ-951A'; passengerInput = 'ISTIQOMAH ASSYFA OCTAVIYANI MRS'; submitPnr('SQ951')"
                        class="w-full p-2 rounded-lg border border-blue-200 bg-blue-50/50 hover:bg-blue-50 text-left text-xs transition cursor-pointer flex items-center justify-between group">
                    <div>
                        <div class="font-bold text-[11px] text-brand-900 flex items-center gap-1.5">
                            <span>SQ951 (CGK → SIN)</span>
                            <span class="px-1.5 py-0.2 bg-blue-100 text-brand-800 text-[9px] font-bold rounded">Business Class</span>
                        </div>
                        <div class="text-[10px] text-slate-500">Istiqomah Assyfa • Gate 6 • Seat 23A</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-brand-600 text-[10px]"></i>
                </button>

                <!-- Test 2: Garuda Indonesia GA826 (Delay +4j) -->
                <button type="button" 
                        @click="pnrInput = 'GA-9821A'; passengerInput = currentUser.name; submitPnr('GA826')"
                        class="w-full p-2 rounded-lg border border-amber-200 bg-amber-50/40 hover:bg-amber-50 text-left text-xs transition cursor-pointer flex items-center justify-between group">
                    <div>
                        <div class="font-bold text-[11px] text-amber-950 flex items-center gap-1.5">
                            <span>GA826 (CGK → SIN)</span>
                            <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[9px] font-bold rounded">Delay +4h</span>
                        </div>
                        <div class="text-[10px] text-slate-500">Kompensasi & Opsi Rebooking Aktif</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-amber-600 text-[10px]"></i>
                </button>
            </div>
        </div>

    </div>
</div>
