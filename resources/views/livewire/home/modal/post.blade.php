<x-modal-card title="{{ $post->title ?? null }}" blur wire:model="modalShow" align="center">

    <div class="h-full flex flex-col justify-center max-w-5xl mx-auto">
        <div
            class="bg-gray-900/95 backdrop-blur-xl border border-emerald-500/30 rounded-xl shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-green-600"></div>

            <div class="p-6 md:p-8">
                <!-- Header Info -->
                <div class="mb-6 bg-emerald-900/20 border border-emerald-500/20 rounded-lg p-4">
                    <h3 class="text-xl md:text-2xl font-bold text-emerald-100 mb-2">
                        {{ $post->description ?? null }}
                    </h3>
                    <div class="flex items-center text-xs text-emerald-400 font-mono">
                        <span class="bg-emerald-900/50 px-2 py-1 rounded border border-emerald-500/30 mr-3">
                            Creado: {{ $post->created_at->format('d M Y') ?? null }}
                        </span>
                        <span class="bg-emerald-900/50 px-2 py-1 rounded border border-emerald-500/30">
                            Actualizado: {{ $post->updated_at->format('d M Y') ?? null }}
                        </span>
                    </div>
                </div>

                <!-- Body Content -->
                <div
                    class="prose prose-invert max-w-none text-gray-300 text-base md:text-lg border-t border-emerald-500/20 pt-6">
                    {{ $post->body ?? null }}
                </div>

                @if ($post->insert)
                    <div
                        class="prose prose-invert max-w-none text-gray-300 mt-6 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                        {!! $post->insert !!}
                    </div>
                @endif

                <!-- Image -->
                @if ($post->saefl_image_url)
                    <div class="mt-8 flex justify-center">
                        <x-card class="bg-gray-800/50 border border-emerald-500/20 p-2 rounded-xl">
                            <img src="{{ asset($post->saefl_image_url) }}"
                                class="block w-full max-h-[60vh] object-contain rounded-lg shadow-lg"
                                alt="Post Image" />
                        </x-card>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-modal-card>
