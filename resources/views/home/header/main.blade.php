<nav class="bg-green-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Logo de tu aplicación -->
                    @include('home.header.icon')
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <!-- Elementos del menú -->
                        @include('home.header.menu.items')
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Botón para abrir el menú en dispositivos móviles -->
                <button type="button"
                    class=" bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-800 hover:text-gray-400 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false" @click="isOpen = !isOpen">
                    <span class="sr-only">Abrir menú principal</span>
                    <!-- Icono para abrir el menú (por ejemplo, un ícono de hamburguesa) -->
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>

    <!-- Menú desplegable en dispositivos móviles -->
    <div class="hidden" id="mobile-menu" x-show="isOpen">
        {{-- <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3"> --}}
        <div class="space-x-4 border-2 border-white rounded">
            <!-- Elementos del menú -->
            @include('home.header.menu.items')
        </div>
    </div>
</nav>

@section('scripts')
    @parent
    <script>
        // Agrega este script al final del archivo o en un archivo JavaScript separado
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
            const mobileMenu = document.querySelector('#mobile-menu');
        
            mobileMenuButton.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>     
@endsection

