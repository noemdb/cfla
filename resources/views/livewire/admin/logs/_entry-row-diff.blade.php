<div class="rounded-lg border border-white/5 bg-gray-900/40 overflow-hidden" x-data="{ open: false }">
    <button type="button"
        @click="open = !open"
        class="w-full text-left px-3 py-2 flex items-start gap-2 hover:bg-white/[0.03] transition-colors group"
        :aria-expanded="open ? 'true' : 'false'"
        aria-controls="diff-ctx-{{ $entry['hash'] }}">
        <span class="mt-0.5 shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ match($tone) { 'emerald' => 'bg-emerald-500/15 text-emerald-300', 'amber' => 'bg-amber-500/15 text-amber-300', default => 'bg-white/10 text-gray-400' } }}">
            {{ $entry['level'] }}
        </span>
        <span class="flex-1 min-w-0">
            <span class="block font-mono text-[11px] text-gray-500 leading-tight">{{ $entry['date'] }}</span>
            <span class="block text-xs text-gray-200 leading-snug break-words line-clamp-2 group-[.open]:line-clamp-none">{{ $entry['message'] }}</span>
        </span>
        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-1 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    @if(! empty($entry['context']) || ! empty($entry['stack']))
        <div x-show="open" x-collapse
            id="diff-ctx-{{ $entry['hash'] }}"
            x-cloak
            class="px-3 pb-3 space-y-2 border-t border-white/5 bg-black/20">
            @if(! empty($entry['context']))
                <pre class="mt-2 text-[11px] font-mono text-violet-300 whitespace-pre-wrap break-words rounded bg-gray-900/60 p-2 overflow-x-auto">{{ $entry['context'] }}</pre>
            @endif
            @if(! empty($entry['stack']))
                <pre class="text-[11px] font-mono text-gray-400 whitespace-pre-wrap break-words rounded bg-gray-900/60 p-2 overflow-x-auto">{{ $entry['stack'] }}</pre>
            @endif
        </div>
    @endif
</div>
