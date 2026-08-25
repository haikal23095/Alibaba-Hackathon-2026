@extends('layouts.app')

@section('content')
<div class="flex-1 flex w-full h-full overflow-hidden relative">
    
    <!-- 1. Left Sidebar: Riwayat Tiket -->
    <div :class="mobileTab === 'tickets' ? 'block w-full' : (leftSidebarOpen ? 'hidden lg:block' : 'hidden')" class="h-full">
        @include('sections.left-sidebar')
    </div>

    <!-- 2. Main Center Chat & Assistant Workspace Area -->
    <section :class="mobileTab === 'assistant' ? 'flex' : 'hidden lg:flex'" 
             class="flex-1 flex-col h-full overflow-hidden px-3 sm:px-6 lg:px-8 pt-3.5 sm:pt-3 pb-20 lg:pb-3 relative bg-[#F8FAFC]">
        
        <!-- Saat PNR Belum Terverifikasi / Belum Diinput -->
        <template x-if="!hasSetupPnr">
            <div class="flex-1 flex flex-col items-center justify-center p-6 text-center space-y-4 max-w-sm mx-auto my-auto select-none">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 flex items-center justify-center text-2xl shadow-xs">
                    <i class="fa-solid fa-ticket-simple"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900" x-text="lang === 'id' ? 'Informasi Belum Dapat Ditampilkan' : 'Information Cannot Be Displayed'"></h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="lang === 'id' ? 'Silakan masukkan atau scan kode booking (PNR) tiket Anda terlebih dahulu untuk memulai pemantauan & analisis penerbangan.' : 'Please enter or scan your booking code (PNR) first to activate flight monitoring & AI assistance.'"></p>
                </div>
                <button @click="showAddTicketModal = true" class="py-2.5 px-5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-barcode text-xs"></i>
                    <span x-text="lang === 'id' ? 'Aktivasi Tiket PNR' : 'Activate PNR Ticket'"></span>
                </button>
            </div>
        </template>

        <!-- Saat PNR Sudah Terverifikasi & Aktif -->
        <template x-if="hasSetupPnr">
            @include('sections.chat-area')
        </template>
    </section>

    <!-- 3. Right Details Sidebar -->
    <div :class="mobileTab === 'details' ? 'block w-full pb-16' : 'hidden lg:block'" class="h-full">
        <template x-if="!hasSetupPnr">
            <aside class="w-full lg:w-[310px] xl:w-[330px] bg-white border-l border-slate-200 flex flex-col h-full items-center justify-center p-6 text-center text-xs text-slate-400 space-y-2.5 select-none">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-plane-slash"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-700 text-xs">Tidak Ada Data Tiket</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Informasi tidak dapat diambil. Cek ulang kode PNR Anda.</p>
                </div>
            </aside>
        </template>
        <template x-if="hasSetupPnr">
            @include('sections.sidebar')
        </template>
    </div>

</div>
@endsection
