@extends('layouts.app')

@section('content')
<div class="flex-1 flex w-full h-full overflow-hidden">
    
    <!-- 1. Left Sidebar: Riwayat Tiket ala ChatGPT (Figma / Custom Feature) -->
    @include('sections.left-sidebar')

    <!-- 2. Main Center Chat & Assistant Workspace Area (Figma Node 3:139, 3:140) -->
    <section class="flex-1 flex flex-col h-full overflow-y-auto px-3 sm:px-6 lg:px-8 pt-4 relative bg-[#F8FAFC]">
        
        <!-- Mobile/Tablet Sidebar Toggles -->
        <div class="lg:hidden flex items-center justify-between mb-3">
            <button @click="leftSidebarOpen = !leftSidebarOpen"
                    class="px-2.5 py-1 bg-slate-900 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-ticket text-[11px]"></i>
                <span x-text="lang === 'id' ? 'Riwayat Tiket' : 'Ticket History'"></span>
            </button>

            <button @click="sidebarOpen = !sidebarOpen"
                    class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-brand-600 text-[11px]"></i>
                <span x-text="sidebarOpen ? (lang === 'id' ? 'Sembunyikan Rincian' : 'Hide Details') : (lang === 'id' ? 'Lihat Rincian' : 'View Details')"></span>
            </button>
        </div>

        <!-- Chat Stream & Conversation Area -->
        @include('sections.chat-area')
    </section>

    <!-- 3. Right Details Sidebar (Zoom-out & Compact Edition, Figma Node 3:342, 3:198) -->
    <div :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="h-full">
        @include('sections.sidebar')
    </div>

</div>
@endsection
