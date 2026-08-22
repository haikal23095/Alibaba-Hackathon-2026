@extends('layouts.app')

@section('content')
<div class="flex-1 flex w-full h-full overflow-hidden relative">
    
    <!-- 1. Left Sidebar: Riwayat Tiket ala ChatGPT -->
    <!-- Desktop: always on left (if leftSidebarOpen). Mobile: shown when mobileTab === 'tickets' -->
    <div :class="mobileTab === 'tickets' ? 'block w-full' : 'hidden lg:block'" class="h-full">
        @include('sections.left-sidebar')
    </div>

    <!-- 2. Main Center Chat & Assistant Workspace Area (Figma Node 3:139, 3:140) -->
    <!-- Desktop: flex-1. Mobile: shown when mobileTab === 'assistant' -->
    <section :class="mobileTab === 'assistant' ? 'flex' : 'hidden lg:flex'" 
             class="flex-1 flex-col h-full overflow-y-auto px-3 sm:px-6 lg:px-8 pt-3 pb-20 lg:pb-3 relative bg-[#F8FAFC]">
        
        <!-- Chat Stream & Conversation Area -->
        @include('sections.chat-area')
    </section>

    <!-- 3. Right Details Sidebar (Zoom-out & Compact Edition, Figma Node 3:342, 3:198) -->
    <!-- Desktop: lg:block on right. Mobile: shown when mobileTab === 'details' -->
    <div :class="mobileTab === 'details' ? 'block w-full pb-16' : 'hidden lg:block'" class="h-full">
        @include('sections.sidebar')
    </div>

</div>
@endsection
