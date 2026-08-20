<div class="max-w-6xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-white">Moderación de Comentarios</h1>
            <p class="text-sm text-gray-400 mt-1">
                Revisa y aprueba los comentarios de los estudiantes en tus actividades
            </p>
        </div>
        @if($pendingCount > 0)
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                {{ $pendingCount }} pendientes
            </span>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-white/5">
        <button wire:click="$set('tab', 'pending')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'pending' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Pendientes @if($pendingCount > 0)({{ $pendingCount }})@endif
        </button>
        <button wire:click="$set('tab', 'approved')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'approved' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Aprobados
        </button>
        <button wire:click="$set('tab', 'rejected')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'rejected' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Rechazados
        </button>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Buscar</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar en comentarios, estudiantes o actividades…"
                   class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm
                          focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none
                          placeholder:text-gray-600 transition-all">
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Actividad</label>
            <select wire:model.live="activityFilter"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                <option value="">Todas las actividades</option>
                @foreach($activities as $id => $topic)
                    <option value="{{ $id }}">{{ \Illuminate\Support\Str::limit($topic, 50) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Bulk actions bar --}}
    @if(count($selected) > 0 && $tab === 'pending')
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
        <span class="text-xs text-emerald-400 font-medium">{{ count($selected) }} seleccionados</span>
        <button wire:click="approveSelected"
                class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg transition-colors">
            Aprobar seleccionados
        </button>
        <button wire:click="rejectSelected"
                class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-lg transition-colors">
            Rechazar seleccionados
        </button>
        <button wire:click="$set('selected', [])"
                class="text-xs text-gray-500 hover:text-gray-300 ml-auto">
            Limpiar
        </button>
    </div>
    @endif

    {{-- Lista de comentarios --}}
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div wire:key="comment-{{ $comment->id }}"
                 class="bg-white/5 border border-white/10 rounded-lg p-4 space-y-3 transition-all
                        {{ $tab === 'pending' ? 'border-l-4 border-l-amber-500/50' : '' }}
                        {{ $tab === 'rejected' ? 'border-l-4 border-l-red-500/50 opacity-70' : '' }}">

                @if($tab === 'pending')
                <div class="flex items-start gap-3">
                    <input type="checkbox" wire:model.live="selected" value="{{ $comment->id }}"
                           class="mt-1 rounded border-gray-600 text-emerald-600 focus:ring-emerald-500
                                  bg-white/5">
                @endif

                    <div class="flex gap-3 flex-1 min-w-0">
                        {{-- Avatar --}}
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-gray-400">
                                {{ strtoupper(substr($comment->user?->profile?->firstname ?? $comment->user?->name ?? '?', 0, 1)) }}
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Metadata --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium text-white">
                                    {{ $comment->user?->profile?->firstname ?? $comment->user?->name ?? '—' }}
                                    {{ $comment->user?->profile?->lastname ?? '' }}
                                </span>
                                <span class="text-[10px] text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                <span class="text-[10px] text-gray-400 px-1.5 py-0.5 rounded bg-white/5">
                                    {{ $comment->activity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                </span>
                            </div>

                            {{-- Actividad --}}
                            <p class="text-[10px] text-gray-500 mt-0.5 truncate">
                                En: <span class="text-gray-400">{{ \Illuminate\Support\Str::limit($comment->activity?->topic ?? 'Actividad', 60) }}</span>
                            </p>

                            {{-- Cuerpo --}}
                            <p class="text-sm text-gray-300 mt-2">{{ $comment->body }}</p>

                            {{-- Moderation metadata --}}
                            @if($comment->approved_at)
                                <p class="text-[10px] text-emerald-500 mt-1">
                                    Aprobado {{ $comment->approved_at->diffForHumans() }}
                                </p>
                            @endif
                            @if($comment->rejected_at)
                                <p class="text-[10px] text-red-400 mt-1">
                                    Rechazado {{ $comment->rejected_at->diffForHumans() }}
                                    @if($comment->rejected_reason)
                                        · Motivo: {{ $comment->rejected_reason }}
                                    @endif
                                </p>
                            @endif

                            {{-- Réplicas del profesor (contexto del hilo) --}}
                            @if($comment->replies->isNotEmpty())
                                <div class="mt-2 ml-2 pl-3 border-l-2 border-emerald-500/30 space-y-2">
                                    @foreach($comment->replies as $reply)
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-[10px] font-semibold text-emerald-300">
                                                    {{ $reply->user?->profile?->firstname ?? '—' }}
                                                </span>
                                                <span class="text-[9px] font-bold uppercase text-emerald-400/70 bg-emerald-500/10 px-1 rounded">Profesor</span>
                                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs text-gray-300 mt-0.5">{{ $reply->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Botón Responder (solo en comentarios raíz no rechazados) --}}
                            @unless($comment->rejected_at)
                            <button wire:click="openReply({{ $comment->id }})"
                                    class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                Responder
                            </button>
                            @endunless
                        </div>
                    </div>

                    {{-- Acciones (solo en pending) --}}
                    @if($tab === 'pending')
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="approveComment({{ $comment->id }})"
                                class="px-3 py-1.5 text-xs font-bold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-lg transition-all"
                                wire:loading.attr="disabled">
                            Aprobar
                        </button>
                        <button wire:click="confirmReject({{ $comment->id }})"
                                class="px-3 py-1.5 text-xs font-bold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-lg transition-all">
                            Rechazar
                        </button>
                    </div>
                    @endif
                @if($tab === 'pending')
                </div>
                @endif

                {{-- Form de réplica inline --}}
                @if($replyToCommentId === $comment->id)
                <div wire:key="reply-form-{{ $comment->id }}" class="space-y-1">
                    <textarea wire:model="replyBody" rows="2" maxlength="1000"
                              placeholder="Escribe tu respuesta…"
                              class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm
                                     focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none
                                     placeholder:text-gray-600 resize-none transition-all"></textarea>
                    @error('replyBody') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    <div class="flex gap-2 justify-end">
                        <button wire:click="$set('replyToCommentId', null)"
                                class="px-3 py-1 text-[11px] text-gray-400 hover:text-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="saveReply" wire:loading.attr="disabled"
                                class="px-3 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg transition-colors">
                            Enviar réplica
                        </button>
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="text-center py-16">
                <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-400 font-medium">
                    @if($tab === 'pending') No hay comentarios pendientes
                    @elseif($tab === 'approved') No hay comentarios aprobados
                    @else No hay comentarios rechazados
                    @endif
                </p>
                <p class="text-xs text-gray-600 mt-1">
                    @if($tab === 'pending') Los comentarios de estudiantes aparecerán aquí.
                    @else Los comentarios moderados aparecerán aquí.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    @if($comments->hasPages())
        <div class="pt-4">{{ $comments->links('vendor.livewire.custom-tailwind') }}</div>
    @endif

    {{-- Modal de rechazo --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" wire:click.self="$set('showRejectModal', false)">
        <div class="bg-gray-900 border border-white/10 rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 space-y-4" wire:click.stop>
            <h2 class="text-sm font-bold text-white">Rechazar comentario</h2>
            <p class="text-xs text-gray-400">
                Opcional: indica el motivo del rechazo.
            </p>
            <textarea wire:model="rejectReason" rows="3"
                      placeholder="Motivo del rechazo (opcional)…"
                      class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg px-4 py-2 text-sm
                             focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none
                             placeholder:text-gray-600 resize-none transition-all"
                      maxlength="500"></textarea>
            @error('rejectReason') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            <div class="flex justify-end gap-2">
                <button wire:click="$set('showRejectModal', false)"
                        class="px-4 py-2 text-xs font-medium text-gray-400 hover:text-gray-300 transition-colors">
                    Cancelar
                </button>
                <button wire:click="rejectComment"
                        class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-lg transition-colors">
                    Rechazar comentario
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
