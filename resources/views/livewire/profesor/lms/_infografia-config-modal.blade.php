<div x-cloak x-show="infografiaModalOpen" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-lg">
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-hidden bg-white dark:bg-gray-800 rounded-lg shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Configurar Infografía</h2>
            <button @click="closeInfografiaModal"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Levels Selector -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Niveles de Jerarquía
                </label>
                <div class="flex items-center space-x-3">
                    <button type="button"
                            @click="infografiaConfig.niveles = Math.max(4, infografiaConfig.niveles - 1)"
                            :disabled="infografiaConfig.niveles <= 4"
                            class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276a1 1 0 011.447 0L20 14v4a1 1 0 01-.553.894l-2.406 1.203a1 1 0 01-1.447 0L15 18h-6V10z" />
                        </svg>
                    </button>
                    <span class="w-16 text-center font-mono text-gray-900 dark:text-gray-100">
                        @{{ infografiaConfig.niveles }}
                    </span>
                    <button type="button"
                            @click="infografiaConfig.niveles = Math.min(6, infografiaConfig.niveles + 1)"
                            :disabled="infografiaConfig.niveles >= 6"
                            class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l-1.406.703a1 1 0 00-1.414 0l-.854-.427V6a1 1 0 011.414 0l1.406.703V14h6z" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Seleccione entre 4 y 6 niveles para la estructura jerárquica
                </p>
            </div>

            <!-- Structure Type Selector -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tipo de Estructura
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button"
                            @click="infografiaConfig.tipoEstructura = 'jerarquica'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-emerald-500 bg-emerald-50 text-emerald-700': infografiaConfig.tipoEstructura === 'jerarquica',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.tipoEstructura !== 'jerarquica',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.tipoEstructura === 'jerarquica'
                            }">
                        <div class="flex items-center justify-center space-x-2 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" />
                            </svg>
                            Jerárquica
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.tipoEstructura = 'radial'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-sky-500 bg-sky-50 text-sky-700': infografiaConfig.tipoEstructura === 'radial',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.tipoEstructura !== 'radial',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-sky-400 dark:bg-sky-900/20 dark:text-sky-300': infografiaConfig.tipoEstructura === 'radial'
                            }">
                        <div class="flex items-center justify-center space-x-2 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a9 9 0 110 18 9 9 0 010-18zm0-2a11 11 0 100 22 11 11 0 000-22z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
                            </svg>
                            Radial
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.tipoEstructura = 'flujo'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-amber-500 bg-amber-50 text-amber-700': infografiaConfig.tipoEstructura === 'flujo',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.tipoEstructura !== 'flujo',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-amber-400 dark:bg-amber-900/20 dark:text-amber-300': infografiaConfig.tipoEstructura === 'flujo'
                            }">
                        <div class="flex items-center justify-center space-x-2 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 1.79 4 4 4h4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v10" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7v10c0 2.21-1.79 4-4 4h-4" />
                            </svg>
                            Flujo
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.tipoEstructura = 'matriz'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-purple-500 bg-purple-50 text-purple-700': infografiaConfig.tipoEstructura === 'matriz',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.tipoEstructura !== 'matriz',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-purple-400 dark:bg-purple-900/20 dark:text-purple-300': infografiaConfig.tipoEstructura === 'matriz'
                            }">
                        <div class="flex items-center justify-center space-x-2 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2a1 1 0 011 1v2a1 1 0 01-1 1H3a1 1 0 01-1-1V4a1 1 0 011-1h2zm0 10h2a1 1 0 011 1v2a1 1 0 01-1 1H3a1 1 0 01-1-1v-2a1 1 0 011-1h2zm10 0h2a1 1 0 011 1v2a1 1 0 01-1 1h-2a1 1 0 01-1-1v-2a1 1 0 011-1h2zm0-10h2a1 1 0 011 1v2a1 1 0 01-1 1h-2a1 1 0 01-1-1v-2a1 1 0 011-1h2z" />
                            </svg>
                            Matriz
                        </div>
                    </button>
                </div>
            </div>

            <!-- Direction Selector -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Dirección
                </label>
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dirección Principal
                        </label>
                        <div class="flex items-center space-x-2">
                            <button type="button"
                                    @click="infografiaConfig.direccion = infografiaConfig.direccion === 'horizontal' ? 'vertical' : 'horizontal'"
                                    class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          :d="infografiaConfig.direccion === 'horizontal' ? 'M4 12h16M8 8v8' : 'M12 4v16M8 12h16'"/>
                                </svg>
                            </button>
                            <span class="text-sm font-mono text-gray-900 dark:text-gray-100 capitalize">
                                @{{ infografiaConfig.direccion }}
                            </span>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Define la orientación principal de la estructura
                </p>
            </div>

            <!-- Color Theme Selector -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tema de Color
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button"
                            @click="infografiaConfig.temaColor = 'esafe'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-emerald-500 bg-emerald-50 text-emerald-700': infografiaConfig.temaColor === 'esafe',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'esafe',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.temaColor === 'esafe'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-emerald-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">
                                ESAFE
                            </span>
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.temaColor = 'azul'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-blue-500 bg-blue-50 text-blue-700': infografiaConfig.temaColor === 'azul',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'azul',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-blue-400 dark:bg-blue-900/20 dark:text-blue-300': infografiaConfig.temaColor === 'azul'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-blue-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-300">
                                Azul
                            </span>
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.temaColor = 'amarillo'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-amber-500 bg-amber-50 text-amber-700': infografiaConfig.temaColor === 'amarillo',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'amarillo',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-amber-400 dark:bg-amber-900/20 dark:text-amber-300': infografiaConfig.temaColor === 'amarillo'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-amber-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-amber-700 dark:text-amber-300">
                                Amarillo
                            </span>
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.temaColor = 'rosa'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-rose-500 bg-rose-50 text-rose-700': infografiaConfig.temaColor === 'rosa',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'rosa',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-rose-400 dark:bg-rose-900/20 dark:text-rose-300': infografiaConfig.temaColor === 'rosa'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-rose-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-rose-700 dark:text-rose-300">
                                Rosa
                            </span>
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.temaColor = 'purpura'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-purple-500 bg-purple-50 text-purple-700': infografiaConfig.temaColor === 'purpura',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'purpura',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-purple-400 dark:bg-purple-900/20 dark:text-purple-300': infografiaConfig.temaColor === 'purpura'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-purple-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                                Púrpura
                            </span>
                        </div>
                    </button>

                    <button type="button"
                            @click="infografiaConfig.temaColor = 'gris'"
                            :class="{
                                'w-full p-3 rounded-lg border-2': true,
                                'border-gray-500 bg-gray-50 text-gray-700': infografiaConfig.temaColor === 'gris',
                                'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100': infografiaConfig.temaColor !== 'gris',
                                'dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': true,
                                'dark:border-gray-400 dark:bg-gray-900/20 dark:text-gray-300': infografiaConfig.temaColor === 'gris'
                            }">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <div class="w-8 h-8 rounded bg-gray-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v2c0 2.21 1.79 4 4 4s4-1.79 4-4v-2c0-2.21-1.79-4-4-4zm0 4c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                Gris
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Preview Button -->
            <div class="flex justify-center">
                <button @click="generarInfografia"
                        :disabled="generatingInfografia"
                        class="w-full px-6 py-3 rounded-lg font-medium transition-colors"
                        :class="{
                            'bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50': !generatingInfografia,
                            'bg-gray-400': generatingInfografia
                        }">
                    <template x-if="!generatingInfografia">
                        Generar Vista Previa
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </template>
                    <template x-if="generatingInfografia">
                        Generando...
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l4 4" />
                        </svg>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>