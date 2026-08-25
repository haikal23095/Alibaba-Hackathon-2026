<!-- Universal Digital Wallet Selector Modal (Android Google Wallet + iOS Apple Wallet + Image) -->
<div x-show="showWalletModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showWalletModal = false"
         class="bg-white rounded-xl max-w-sm w-full p-5 shadow-xl border border-slate-200 text-left space-y-3.5 relative overflow-hidden"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900"
                    x-text="lang === 'id' ? 'Simpan ke Wallet' : 'Save to Wallet'"></h3>
                <p class="text-[11px] text-slate-500"
                   x-text="lang === 'id' ? 'Pilih format dompet digital HP Anda' : 'Choose mobile wallet format'"></p>
            </div>

            <button @click="showWalletModal = false" class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- 3 Wallet Format Options -->
        <div class="space-y-2 text-xs">
            
            <!-- OPTION 1: Google Wallet (For Android Users) -->
            <button @click="saveGoogleWallet(); showWalletModal = false" 
                    class="w-full text-left p-3 rounded-lg border border-emerald-300 bg-emerald-50/40 hover:bg-emerald-50 transition flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-emerald-200 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-brands fa-google text-emerald-600"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span>Google Wallet</span>
                            <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[9px] font-bold uppercase rounded">Android</span>
                        </div>
                        <p class="text-slate-500 text-[10px]"
                           x-text="lang === 'id' ? 'Pass digital untuk HP Android' : 'Digital pass for Android'"></p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-emerald-600 text-xs"></i>
            </button>

            <!-- OPTION 2: Apple Wallet (For iPhone / iOS Users) -->
            <button @click="downloadPkpass(); showWalletModal = false" 
                    class="w-full text-left p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm shrink-0">
                        <i class="fa-brands fa-apple"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span>Apple Wallet</span>
                            <span class="px-1.5 py-0.2 bg-slate-100 text-slate-700 text-[9px] font-bold uppercase rounded">iOS</span>
                        </div>
                        <p class="text-slate-500 text-[10px]"
                           x-text="lang === 'id' ? 'File .pkpass untuk Apple Wallet' : '.pkpass file for Apple Wallet'"></p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </button>

            <!-- OPTION 3: Save as Image (PNG/QR) to Photo Gallery -->
            <button @click="downloadPassImage(); showWalletModal = false" 
                    class="w-full text-left p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 border border-brand-200 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span x-text="lang === 'id' ? 'Galeri Foto (Gambar PNG)' : 'Photo Gallery (PNG)'"></span>
                        </div>
                        <p class="text-slate-500 text-[10px]"
                           x-text="lang === 'id' ? 'Simpan gambar tiket ke galeri HP' : 'Save pass image to gallery'"></p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </button>

        </div>

        <!-- Footer -->
        <div class="pt-1 flex justify-end">
            <button @click="showWalletModal = false" class="text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer" x-text="lang === 'id' ? 'Batal' : 'Cancel'"></button>
        </div>

    </div>
</div>
