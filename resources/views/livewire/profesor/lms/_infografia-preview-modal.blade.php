<div x-cloak x-show="infografiaPreviewOpen" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-lg">
    <div class="relative w-full max-w-xl max-h-[90vh] overflow-hidden bg-white dark:bg-gray-800 rounded-lg shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Vista Previa de Infografía</h2>
            <div class="flex items-center space-x-2">
                <button @click="closeInfografiaPreview"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <button @click="insertInfografiaEnEditor"
                        :disabled="infografiaError || !infografiaPreviewSvg"
                        class="px-3 py-1 rounded-lg font-medium transition-colors
                               :class="{
                                   'bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50': !infografiaError && infografiaPreviewSvg,
                                   'bg-gray-400': infografiaError || !infografiaPreviewSvg
                               }">
                    Insertar en Lección
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Error Message -->
            <template x-if="infografiaError">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 100-2 1 1 0 000 2z"
                                      clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                Error al generar la infografía
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                {{ infografiaError }}
                            </p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Loading State -->
            <template x-if="!infografiaError && !infografiaPreviewSvg">
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l4 4" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Generando vista previa...
                    </p>
                </div>
            </template>

            <!-- SVG Preview -->
            <template x-if="!infografiaError && infografiaPreviewSvg">
                <div class="relative">
                    <!-- SVG Container -->
                    <div class="w-full h-[400px] flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg mb-4"
                         x-html="infografiaPreviewSvg"></div>

                    <!-- Info Bar -->
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>
                            Niveles: {{ infografiaConfig.niveles }} |
                            Tipo: {{ infografiaConfig.tipoEstructura | capitalize }} |
                            Dirección: {{ infografiaConfig.direccion | capitalize }} |
                            Tema: {{ infografiaConfig.temaColor | capitalize }}
                        </span>
                        <button @click="downloadInfografiaSvg"
                                class="flex items-center space-x-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                            Descargar SVG
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M5 10l5 5 5-5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>