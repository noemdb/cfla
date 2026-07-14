<div class="fade-in" x-data="{ formOpen: @entangle('modeForm'), deleteId: @entangle('confirmDeleteId') }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Gestión de Usuarios</h1>
            <p class="text-emerald-400 font-medium text-sm">Administra las cuentas, roles y accesos del sistema.</p>
        </div>
        <button wire:click="create"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 rounded-lg border border-emerald-500/30 transition-all duration-300 text-sm font-bold uppercase tracking-widest group">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Usuario
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Usuario, email, cédula..."
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Filtrar por Rol</label>
                <select wire:model.live="filter_role"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors">
                    @foreach($roleOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="$set('search', ''); $set('filter_role', '')"
                    class="px-4 py-2.5 text-sm text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th wire:click="sortBy('id')" class="px-4 py-3 text-left cursor-pointer group">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400 group-hover:text-emerald-400 transition-colors">
                                ID
                                @if($sortField === 'id')
                                    <svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </span>
                        </th>
                        <th wire:click="sortBy('username')" class="px-4 py-3 text-left cursor-pointer group">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400 group-hover:text-emerald-400 transition-colors">
                                Usuario
                                @if($sortField === 'username')
                                    <svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </span>
                        </th>
                        <th wire:click="sortBy('email')" class="px-4 py-3 text-left cursor-pointer group hidden md:table-cell">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400 group-hover:text-emerald-400 transition-colors">
                                Email
                                @if($sortField === 'email')
                                    <svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </span>
                        </th>
                        <th class="px-4 py-3 text-left hidden lg:table-cell">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Roles</span>
                        </th>
                        <th class="px-4 py-3 text-center">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Estado</span>
                        </th>
                        <th class="px-4 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors {{ $user->id === auth()->id() ? 'bg-emerald-500/[0.03]' : '' }}">
                            <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $user->id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 border border-white/10 flex items-center justify-center text-xs font-bold text-emerald-400 uppercase shrink-0">
                                        {{ substr($user->username, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $user->username }}</p>
                                        @if($user->profile)
                                            <p class="text-xs text-gray-500">{{ $user->profile->firstname }} {{ $user->profile->lastname }}</p>
                                        @endif
                                        @if($user->id === auth()->id())
                                            <span class="text-[10px] text-emerald-500 font-medium">(tú)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-400 hidden md:table-cell">{{ $user->email }}</td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Admin</span>
                                    @endif
                                    @if($user->is_planner)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-cyan-500/15 text-cyan-400 border border-cyan-500/20">Planif.</span>
                                    @endif
                                    @if($user->is_profesor)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-500/15 text-blue-400 border border-blue-500/20">Prof.</span>
                                    @endif
                                    @if($user->is_diagnostic)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-500/15 text-purple-400 border border-purple-500/20">Diag.</span>
                                    @endif
                                    @if(!$user->is_admin && !$user->is_planner && !$user->is_profesor && !$user->is_diagnostic)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-500/15 text-gray-400 border border-gray-500/20">Std.</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($user->is_active === 'enable' || $user->is_active === true)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-400">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-400">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $user->id }})"
                                        class="p-2 text-gray-400 hover:text-emerald-300 bg-white/5 hover:bg-emerald-500/20 rounded-lg border border-white/5 transition-all duration-300 group"
                                        title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="p-2 text-gray-400 hover:text-red-400 bg-white/5 hover:bg-red-500/20 rounded-lg border border-white/5 transition-all duration-300 group"
                                            title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No se encontraron usuarios</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-white/5">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="formOpen" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="formOpen = false">

        <div @click.away="formOpen = false"
            class="w-full max-w-2xl bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 overflow-hidden">

            <!-- Modal header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"></path>
                    </svg>
                    {{ $isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </h2>
                <button wire:click="$set('modeForm', false)"
                    class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save">
                <div class="px-6 py-5 space-y-6 max-h-[65vh] overflow-y-auto">

                    {{-- Sección: Cuenta --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400/60 mb-3">Datos de la Cuenta</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nombre de Usuario *</label>
                                <input type="text" wire:model="username"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                                @error('username') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Correo Electrónico *</label>
                                <input type="email" wire:model="email"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                    Contraseña {{ $isEditing ? '(dejar vacío para mantener)' : '*' }}
                                </label>
                                <input type="password" wire:model="password" autocomplete="new-password"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                                @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
                                <select wire:model="is_active"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Roles --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400/60 mb-3">Roles y Permisos</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center gap-3 px-4 py-3 bg-gray-800/30 border border-white/5 rounded-lg cursor-pointer hover:bg-gray-800/50 transition-colors">
                                <input type="checkbox" wire:model="is_admin" class="rounded bg-gray-700 border-gray-600 text-emerald-500 focus:ring-emerald-500/30">
                                <span class="text-sm text-gray-300">Administrador</span>
                            </label>
                            <label class="flex items-center gap-3 px-4 py-3 bg-gray-800/30 border border-white/5 rounded-lg cursor-pointer hover:bg-gray-800/50 transition-colors">
                                <input type="checkbox" wire:model="is_planner" class="rounded bg-gray-700 border-gray-600 text-cyan-500 focus:ring-cyan-500/30">
                                <span class="text-sm text-gray-300">Planificación</span>
                            </label>
                            <label class="flex items-center gap-3 px-4 py-3 bg-gray-800/30 border border-white/5 rounded-lg cursor-pointer hover:bg-gray-800/50 transition-colors">
                                <input type="checkbox" wire:model="is_profesor" class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500/30">
                                <span class="text-sm text-gray-300">Profesor</span>
                            </label>
                            <label class="flex items-center gap-3 px-4 py-3 bg-gray-800/30 border border-white/5 rounded-lg cursor-pointer hover:bg-gray-800/50 transition-colors">
                                <input type="checkbox" wire:model="is_diagnostic" class="rounded bg-gray-700 border-gray-600 text-purple-500 focus:ring-purple-500/30">
                                <span class="text-sm text-gray-300">Diagnóstico</span>
                            </label>
                        </div>
                    </div>

                    {{-- Sección: Perfil --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400/60 mb-3">Datos del Perfil (opcional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nombres</label>
                                <input type="text" wire:model="firstname"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Apellidos</label>
                                <input type="text" wire:model="lastname"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Cédula / Documento</label>
                                <input type="text" wire:model="card_number"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-white/5">
                    <button type="button" wire:click="$set('modeForm', false)"
                        class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-emerald-500/20 hover:bg-emerald-500/30 rounded-lg border border-emerald-500/30 transition-all duration-300">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Actualizar' : 'Crear Usuario' }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete confirmation modal -->
    <div x-show="deleteId" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="deleteId = null">

        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar usuario?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el usuario del sistema. No se podrá deshacer.</p>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="cancelDelete"
                    class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Cancelar
                </button>
                <button wire:click="destroy"
                    class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-red-500/20 hover:bg-red-500/30 rounded-lg border border-red-500/30 transition-all duration-300">
                    <span wire:loading.remove wire:target="destroy">Eliminar</span>
                    <span wire:loading wire:target="destroy">Eliminando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
