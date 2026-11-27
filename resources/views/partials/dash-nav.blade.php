@foreach ($navItems as $item)
    @php $isActive = !empty($item['active']) && request()->routeIs($item['active']); @endphp
    <a href="{{ $item['url'] }}"
       @if (!empty($item['external'])) target="_blank" rel="noopener" @endif
       data-tip="{{ $item['label'] }}"
       class="nav-link relative flex items-center gap-3 h-11 px-3 rounded-xl text-sm font-medium transition-colors
              {{ $isActive ? 'bg-math-teal/10 text-math-teal' : 'text-on-surface-variant hover:bg-surface-container hover:text-navy-deep' }}">
        @if ($isActive)
            <span class="active-bar absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-math-teal"></span>
        @endif
        <span class="material-symbols-outlined text-[22px] shrink-0"
              style="font-variation-settings: 'FILL' {{ $isActive ? 1 : 0 }};">{{ $item['icon'] }}</span>
        <span class="nav-label truncate">{{ $item['label'] }}</span>
        @if (!empty($item['external']))
            <span class="nav-label material-symbols-outlined text-[14px] ml-auto opacity-50">open_in_new</span>
        @endif
    </a>
@endforeach