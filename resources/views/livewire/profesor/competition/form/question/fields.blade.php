{{-- Debate select --}}
<div>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Debate</label>
    <select wire:model="questionForm.debate_id"
        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50">
        <option value="">Seleccione un debate</option>
        @foreach($debates as $debate)
            <option value="{{ $debate->id }}">{{ $debate->name }}</option>
        @endforeach
    </select>
    @error('questionForm.debate_id') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
</div>

{{-- Category --}}
<div>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Categoría</label>
    <select wire:model="questionForm.category"
        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50">
        <option value="">Seleccione una categoría</option>
        @foreach($categories as $fullKey => $shortName)
            <option value="{{ $fullKey }}">{{ $shortName }}</option>
        @endforeach
    </select>
    @error('questionForm.category') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
</div>

{{-- Question text --}}
<div>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Texto de la pregunta</label>
    <textarea wire:model="questionForm.text" rows="3"
        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 resize-y"
        placeholder="Escriba la pregunta aquí..."></textarea>
    @error('questionForm.text') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
</div>

{{-- Observation --}}
<div>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Observación</label>
    <textarea wire:model="questionForm.observation" rows="2"
        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 resize-y"
        placeholder="Observación adicional (opcional)"></textarea>
    @error('questionForm.observation') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    {{-- Time --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Tiempo (segundos)</label>
        <input type="number" wire:model="questionForm.time" min="0"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50"
            placeholder="30">
        @error('questionForm.time') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Weighting --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Ponderación</label>
        <input type="number" wire:model="questionForm.weighting" min="0"
            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50"
            placeholder="1">
        @error('questionForm.weighting') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    </div>
</div>

{{-- Status active --}}
<div class="flex items-center gap-2">
    <input type="checkbox" wire:model="questionForm.status_active" value="1"
        class="rounded border-white/10 bg-gray-800/50 text-emerald-500 focus:ring-emerald-500/20">
    <span class="text-[11px] text-gray-400">Activo</span>
</div>

{{-- Attachment --}}
<div>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Archivo adjunto (opcional, máx 1MB)</label>
    <input type="file" wire:model="attachmentUpload" accept="image/*"
        class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 file:cursor-pointer">
    @error('attachmentUpload') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
    @if($attachmentUpload)
        <div class="mt-2">
            <img src="{{ $attachmentUpload->temporaryUrl() }}" class="h-20 rounded-lg border border-white/10">
        </div>
    @elseif($existingAttachment)
        <div class="mt-2">
            <a href="{{ asset('storage/educationals/' . $existingAttachment) }}" target="_blank"
                class="text-[10px] text-emerald-400 hover:text-emerald-300 underline">
                📎 Ver adjunto actual
            </a>
        </div>
    @endif
</div>
