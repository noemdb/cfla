<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Fecha Inicial --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['finicial'] ?? 'Fecha Inicial' }}</label>
        <input type="date" wire:model="activityForm.finicial"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
        @error('activityForm.finicial') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Fecha Final --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['ffinal'] ?? 'Fecha Final' }}</label>
        <input type="date" wire:model="activityForm.ffinal"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
        @error('activityForm.ffinal') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Actividad Evaluativa --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['description'] ?? 'Actividad Evaluativa' }}</label>
        <textarea wire:model="activityForm.description" rows="2"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['description'] ?? '' }}"></textarea>
        @error('activityForm.description') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Tema generador --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['topic'] ?? 'Tema Generador y Énfasis' }}</label>
        <textarea wire:model="activityForm.topic" rows="2"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['topic'] ?? '' }}"></textarea>
        @error('activityForm.topic') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Tejido temático --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['thematic'] ?? 'Tejido Temático' }}</label>
        <textarea wire:model="activityForm.thematic" rows="3"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['thematic'] ?? '' }}"></textarea>
        @error('activityForm.thematic') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Referentes --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['references'] ?? 'Referentes Teórico-Prácticos' }}</label>
        <textarea wire:model="activityForm.references" rows="3"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['references'] ?? '' }}"></textarea>
        @error('activityForm.references') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Enseñanza --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['teaching'] ?? 'Enseñanza / Actividad Globalizada' }}</label>
        <textarea wire:model="activityForm.teaching" rows="4"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['teaching'] ?? '' }}"></textarea>
        @error('activityForm.teaching') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Aprendizaje --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['learning'] ?? 'Aprendizaje' }}</label>
        <textarea wire:model="activityForm.learning" rows="4"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['learning'] ?? '' }}"></textarea>
        @error('activityForm.learning') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Observaciones --}}
    <div class="md:col-span-2">
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['observations'] ?? 'ODS / Sistematización' }}</label>
        <textarea wire:model="activityForm.observations" rows="2"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
            placeholder="{{ $list_comment['observations'] ?? '' }}"></textarea>
        @error('activityForm.observations') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

</div>
