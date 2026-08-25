@props([
    'size' => 'md', // 'sm', 'md', 'lg'
    'showText' => true,
    'textClass' => 'text-slate-900'
])

@php
    $dim = match($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-10 h-10',
        default => 'w-8 h-8',
    };
    $svgDim = match($size) {
        'sm' => 'w-4 h-4',
        'lg' => 'w-6 h-6',
        default => 'w-5 h-5',
    };
    $fontSize = match($size) {
        'sm' => 'text-base',
        'lg' => 'text-2xl',
        default => 'text-lg',
    };
@endphp

<div class="inline-flex items-center gap-2 select-none">
    <!-- Rebound Airplane & Circular Loop Emblem -->
    <div class="{{ $dim }} rounded-lg bg-gradient-to-br from-brand-600 to-brand-700 text-white flex items-center justify-center shadow-xs border border-brand-500/30 shrink-0">
        <svg class="{{ $svgDim }}" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Smooth Circular Rebound Orbit / Turnaround Loop -->
            <path d="M16 4C9.37258 4 4 9.37258 4 16C4 22.6274 9.37258 28 16 28C22.0628 28 27.0703 23.5135 27.8764 17.65" 
                  stroke="white" 
                  stroke-width="2.5" 
                  stroke-linecap="round" 
                  stroke-dasharray="38 4"/>
            
            <!-- Arrow Head on the Rebound Loop Arc -->
            <path d="M19 1.5L16 4.5L19 7.5" 
                  stroke="white" 
                  stroke-width="2.5" 
                  stroke-linecap="round" 
                  stroke-linejoin="round"/>
            
            <!-- Sleek Modern Passenger Jet Airplane (Centered & Proportional) -->
            <g transform="translate(16, 16) rotate(-15) translate(-16, -16)">
                <path d="M16 7C15.4 7 15 7.6 15 8.4V14L8 18V20.2L15 17.8V23.2L12.5 25V26.5L16 25.5L19.5 26.5V25L17 23.2V17.8L24 20.2V18L17 14V8.4C17 7.6 16.6 7 16 7Z" 
                      fill="white"/>
            </g>
        </svg>
    </div>

    @if($showText)
        <span class="{{ $fontSize }} font-black tracking-tight {{ $textClass }}">REBOUND</span>
    @endif
</div>
