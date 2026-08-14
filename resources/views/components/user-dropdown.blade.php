@props(['user' => null, 'align' => 'right', 'width' => '56'])

@php
    $user = $user ?? Auth::user();
    
    // Si no hay usuario autenticado, no renderizar nada
    if (!$user) {
        return '';
    }
    
    switch ($align) {
        case 'left':
            $alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
            break;
        case 'top':
            $alignmentClasses = 'origin-top';
            break;
        case 'right':
        default:
            $alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
            break;
    }

    switch ($width) {
        case '48':
            $widthClass = 'w-48';
            break;
        case '56':
            $widthClass = 'w-56';
            break;
        case '64':
            $widthClass = 'w-64';
            break;
        default:
            $widthClass = 'w-56';
            break;
    }

    // Avatar: iniciales del profile (firstname + lastname) o de username.
    // Sin imagen por defecto: siempre se muestran las iniciales.
    $firstName = trim((string) ($user->profile?->firstname ?? ''));
    $lastName = trim((string) ($user->profile?->lastname ?? ''));

    $initials = '';
    if ($firstName !== '' || $lastName !== '') {
        $initials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
    }
    if ($initials === '') {
        $initials = mb_strtoupper(mb_substr(trim((string) ($user->username ?? '')), 0, 2));
    }
    if ($initials === '') {
        $initials = 'U';
    }

    $avatarUrl = $user->profile?->url_img ?? null;

    // Si url_img está vacío o apunta a un placeholder por defecto, se tratan
    // como "sin imagen" y el avatar muestra las iniciales del profile.
    if ($avatarUrl !== null && ($avatarUrl === '' || str_contains($avatarUrl, 'user_default') || str_contains($avatarUrl, 'default_user_admin'))) {
        $avatarUrl = null;
    }

    $fullName = $user->full_name ?? $user->username;
    $email = $user->email;
    $roleLabel = $user->role_label;
    $isPlanner = $user->is_planner ?? false;
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <!-- Trigger -->
    <div @click="open = ! open" class="flex items-center space-x-2 px-2 py-1.5 bg-gray-100/80 dark:bg-gray-900/30 backdrop-blur-md rounded-lg cursor-pointer hover:bg-gray-200/50 dark:hover:bg-white/5 transition-colors">
        <!-- Avatar -->
        <div class="flex-shrink-0">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $fullName }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500/30">
            @else
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center ring-2 ring-emerald-500/30">
                    <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ $initials }}</span>
                </div>
            @endif
        </div>

        <!-- Username + Role (desktop only) -->
        <div class="hidden sm:block text-left">
            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[140px]">{{ $user->username }}</p>
            <p class="text-xs text-emerald-500 truncate max-w-[140px]">{{ $roleLabel }}</p>
        </div>

        <!-- Chevron -->
        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    <!-- Dropdown Panel -->
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }}"
         style="display: none;"
         @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

            <!-- Header: User Info -->
            <div class="px-4 py-3">
                <div class="flex items-center space-x-3">
                    <!-- Avatar grande -->
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $fullName }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-emerald-500/30">
                    @else
                        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center ring-2 ring-emerald-500/30">
                            <span class="text-xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $initials }}</span>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $fullName }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $email }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 mt-1">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="py-1">
                <!-- Ver/Editar Perfil -->
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                    <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="whitespace-nowrap">Ver / Editar Perfil</span>
                </a>

                @if($isPlanner)
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                    <!-- Gestionar Usuarios/Roles (solo planners) -->
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                        <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="whitespace-nowrap">Gestionar Usuarios / Roles</span>
                    </a>
                @endif

                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                <!-- Cerrar Sesión -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="whitespace-nowrap">Cerrar Sesión</span>
                    </button>
                </form>
            </nav>
        </div>
    </div>
</div>