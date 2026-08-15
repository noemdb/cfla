<div x-cloak x-show="infografiaModalOpen" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-lg">
    <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col bg-white dark:bg-gray-800 rounded-lg shadow-xl">
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

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button type="button"
                    @click="configTab = 'estructura'"
                    :class="{
                        'flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors': true,
                        'border-emerald-500 text-emerald-600 dark:text-emerald-400': configTab === 'estructura',
                        'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': configTab !== 'estructura'
                    }">
                <span class="flex items-center justify-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" />
                    </svg>
                    <span>Estructura</span>
                </span>
            </button>
            <button type="button"
                    @click="configTab = 'estilo'"
                    :class="{
                        'flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors': true,
                        'border-emerald-500 text-emerald-600 dark:text-emerald-400': configTab === 'estilo',
                        'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': configTab !== 'estilo'
                    }">
                <span class="flex items-center justify-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0a4 4 0 004-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21H7" />
                    </svg>
                    <span>Estilo</span>
                </span>
            </button>
        </div>

        <!-- Body: scrollable -->
        <div class="flex-1 overflow-y-auto">
            <!-- Tab: Estructura -->
            <div x-show="configTab === 'estructura'" x-cloak class="p-6 space-y-6">
                <!-- Levels Selector -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Niveles de Jerarquía
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button"
                                @click="infografiaConfig.niveles = 4"
                                :class="{
                                    'flex items-center justify-center gap-3 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.niveles === 4,
                                    'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': infografiaConfig.niveles !== 4
                                }">
                            <span class="text-3xl font-bold leading-none" x-text="'4'"></span>
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-medium">Niveles</span>
                                <span class="flex items-end gap-1 h-6">
                                    <template x-for="(bar, i) in Array.from({ length: 4 })" :key="i">
                                        <span class="w-1.5 rounded-sm"
                                              :class="infografiaConfig.niveles === 4 ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-gray-300 dark:bg-gray-600'"
                                              :style="'height: ' + (6 + i * 4) + 'px'"></span>
                                    </template>
                                </span>
                            </div>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.niveles = 5"
                                :class="{
                                    'flex items-center justify-center gap-3 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.niveles === 5,
                                    'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': infografiaConfig.niveles !== 5
                                }">
                            <span class="text-3xl font-bold leading-none" x-text="'5'"></span>
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-medium">Niveles</span>
                                <span class="flex items-end gap-1 h-6">
                                    <template x-for="(bar, i) in Array.from({ length: 5 })" :key="i">
                                        <span class="w-1.5 rounded-sm"
                                              :class="infografiaConfig.niveles === 5 ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-gray-300 dark:bg-gray-600'"
                                              :style="'height: ' + (6 + i * 4) + 'px'"></span>
                                    </template>
                                </span>
                            </div>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.niveles = 6"
                                :class="{
                                    'flex items-center justify-center gap-3 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.niveles === 6,
                                    'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': infografiaConfig.niveles !== 6
                                }">
                            <span class="text-3xl font-bold leading-none" x-text="'6'"></span>
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-medium">Niveles</span>
                                <span class="flex items-end gap-1 h-6">
                                    <template x-for="(bar, i) in Array.from({ length: 6 })" :key="i">
                                        <span class="w-1.5 rounded-sm"
                                              :class="infografiaConfig.niveles === 6 ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-gray-300 dark:bg-gray-600'"
                                              :style="'height: ' + (6 + i * 4) + 'px'"></span>
                                    </template>
                                </span>
                            </div>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Elija cuántos niveles de profundidad tendrá la jerarquía
                    </p>
                </div>

                <!-- Structure Type Selector -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tipo de Estructura
                    </label>
                    <div class="grid grid-cols-4 gap-3">
                        <button type="button"
                                @click="infografiaConfig.tipoEstructura = 'jerarquica'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.tipoEstructura === 'jerarquica',
                                    'border-gray-300 bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300': infografiaConfig.tipoEstructura !== 'jerarquica'
                                }">
                            <svg class="w-10 h-10" viewBox="0 0 48 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="10" rx="2" />
                                <rect x="4" y="26" width="10" height="10" rx="2" />
                                <rect x="18" y="26" width="10" height="10" rx="2" />
                                <rect x="32" y="26" width="12" height="10" rx="2" />
                                <path d="M12 14v6M8 20v6M22 20v6M36 20v-6" />
                            </svg>
                            <span class="text-sm font-medium">Jerárquica</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.tipoEstructura = 'radial'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-sky-500 bg-sky-50 text-sky-700 dark:border-sky-400 dark:bg-sky-900/20 dark:text-sky-300': infografiaConfig.tipoEstructura === 'radial',
                                    'border-gray-300 bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300': infografiaConfig.tipoEstructura !== 'radial'
                                }">
                            <svg class="w-10 h-10" viewBox="0 0 48 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="24" cy="20" r="4" />
                                <circle cx="24" cy="20" r="12" stroke-dasharray="3 3" />
                                <circle cx="6" cy="9" r="3" />
                                <circle cx="42" cy="9" r="3" />
                                <circle cx="6" cy="31" r="3" />
                                <circle cx="42" cy="31" r="3" />
                                <path d="M24 16v4m0 0l-6 6m6-6l6 6m-12-6h12" />
                            </svg>
                            <span class="text-sm font-medium">Radial</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.tipoEstructura = 'flujo'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-amber-500 bg-amber-50 text-amber-700 dark:border-amber-400 dark:bg-amber-900/20 dark:text-amber-300': infografiaConfig.tipoEstructura === 'flujo',
                                    'border-gray-300 bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300': infografiaConfig.tipoEstructura !== 'flujo'
                                }">
                            <svg class="w-10 h-10" viewBox="0 0 48 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="15" width="10" height="10" rx="2" />
                                <rect x="20" y="15" width="10" height="10" rx="2" />
                                <rect x="36" y="15" width="8" height="10" rx="2" />
                                <path d="M14 20h6m10 0h6" />
                                <path d="M40 15l-2-5m2 5l-2 5m-28 0l-2 5m2-5l-2-5" />
                            </svg>
                            <span class="text-sm font-medium">Flujo</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.tipoEstructura = 'matriz'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-colors': true,
                                    'border-purple-500 bg-purple-50 text-purple-700 dark:border-purple-400 dark:bg-purple-900/20 dark:text-purple-300': infografiaConfig.tipoEstructura === 'matriz',
                                    'border-gray-300 bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300': infografiaConfig.tipoEstructura !== 'matriz'
                                }">
                            <svg class="w-10 h-10" viewBox="0 0 48 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="10" rx="2" />
                                <rect x="24" y="4" width="16" height="10" rx="2" />
                                <rect x="4" y="18" width="16" height="10" rx="2" />
                                <rect x="24" y="18" width="16" height="10" rx="2" />
                                <path d="M20 9h4m-16 4v5m16-5v5m16-5v5" opacity="0" />
                            </svg>
                            <span class="text-sm font-medium">Matriz</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab: Estilo -->
            <div x-show="configTab === 'estilo'" x-cloak class="p-6 space-y-6">
                <!-- Direction Selector -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dirección
                    </label>
                    <div class="flex items-center justify-center space-x-6">
                        <button type="button"
                                @click="infografiaConfig.direccion = 'horizontal'"
                                :class="{
                                    'flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-colors w-40': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.direccion === 'horizontal',
                                    'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': infografiaConfig.direccion !== 'horizontal'
                                }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M7 8l-4 4 4 4M17 8l4 4-4 4" />
                            </svg>
                            <span class="text-sm font-medium">Horizontal</span>
                        </button>
                        <button type="button"
                                @click="infografiaConfig.direccion = 'vertical'"
                                :class="{
                                    'flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-colors w-40': true,
                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/20 dark:text-emerald-300': infografiaConfig.direccion === 'vertical',
                                    'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': infografiaConfig.direccion !== 'vertical'
                                }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 7l4-4 4 4M8 17l4 4 4-4" />
                            </svg>
                            <span class="text-sm font-medium">Vertical</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                        Define la orientación principal de la estructura
                    </p>
                </div>

                <!-- Color Theme Selector -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tema de Color
                    </label>
                    <div class="grid grid-cols-6 gap-3">
                        <button type="button"
                                @click="infografiaConfig.temaColor = 'esafe'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-emerald-500 bg-emerald-50 dark:border-emerald-400 dark:bg-emerald-900/20': infografiaConfig.temaColor === 'esafe',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'esafe'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#f0fdf4]"></span>
                                <span class="flex-1 bg-[#a7f3d0]"></span>
                                <span class="flex-1 bg-[#34d399]"></span>
                                <span class="flex-1 bg-[#059669]"></span>
                                <span class="flex-1 bg-[#064e3b]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">ESAFE</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.temaColor = 'azul'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-900/20': infografiaConfig.temaColor === 'azul',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'azul'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#f0f9ff]"></span>
                                <span class="flex-1 bg-[#bae6fd]"></span>
                                <span class="flex-1 bg-[#38bdf8]"></span>
                                <span class="flex-1 bg-[#0284c7]"></span>
                                <span class="flex-1 bg-[#075985]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Azul</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.temaColor = 'amarillo'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-amber-500 bg-amber-50 dark:border-amber-400 dark:bg-amber-900/20': infografiaConfig.temaColor === 'amarillo',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'amarillo'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#fffbeb]"></span>
                                <span class="flex-1 bg-[#fde68a]"></span>
                                <span class="flex-1 bg-[#fbbf24]"></span>
                                <span class="flex-1 bg-[#d97706]"></span>
                                <span class="flex-1 bg-[#92400e]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Amarillo</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.temaColor = 'rosa'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-rose-500 bg-rose-50 dark:border-rose-400 dark:bg-rose-900/20': infografiaConfig.temaColor === 'rosa',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'rosa'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#fff1f2]"></span>
                                <span class="flex-1 bg-[#fecdd3]"></span>
                                <span class="flex-1 bg-[#fb7185]"></span>
                                <span class="flex-1 bg-[#e11d48]"></span>
                                <span class="flex-1 bg-[#9f1239]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Rosa</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.temaColor = 'purpura'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-purple-500 bg-purple-50 dark:border-purple-400 dark:bg-purple-900/20': infografiaConfig.temaColor === 'purpura',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'purpura'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#faf5ff]"></span>
                                <span class="flex-1 bg-[#e9d5ff]"></span>
                                <span class="flex-1 bg-[#c084fc]"></span>
                                <span class="flex-1 bg-[#7c3aed]"></span>
                                <span class="flex-1 bg-[#5b21b6]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Púrpura</span>
                        </button>

                        <button type="button"
                                @click="infografiaConfig.temaColor = 'gris'"
                                :class="{
                                    'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-colors': true,
                                    'border-gray-500 bg-gray-50 dark:border-gray-400 dark:bg-gray-900/20': infografiaConfig.temaColor === 'gris',
                                    'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700': infografiaConfig.temaColor !== 'gris'
                                }">
                            <span class="flex w-full h-6 rounded-lg overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                <span class="flex-1 bg-[#fafaf9]"></span>
                                <span class="flex-1 bg-[#e7e5e4]"></span>
                                <span class="flex-1 bg-[#a8a29e]"></span>
                                <span class="flex-1 bg-[#57534e]"></span>
                                <span class="flex-1 bg-[#292524]"></span>
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Gris</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <button @click="generarInfografia"
                    :disabled="generatingInfografia"
                    class="w-full px-6 py-3 rounded-lg font-medium transition-colors"
                    :class="{
                        'bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50': !generatingInfografia,
                        'bg-gray-400': generatingInfografia
                    }">
                <template x-if="!generatingInfografia">
                    <span class="flex items-center justify-center">
                        Generar Vista Previa
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </template>
                <template x-if="generatingInfografia">
                    <span class="flex items-center justify-center">
                        Generando...
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l4 4" />
                        </svg>
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>