<!-- Sección de Oferta Académica - Pensum con Modal de Imagen -->
<section class="py-4">
    <div class="max-w-7xl mx-auto text-center">
        <!-- Text header removed as it is now in main -->

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Primer Año -->
            <div class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl overflow-hidden hover:border-emerald-500/80 transition-all duration-500 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/20 cursor-pointer"
                onclick="openModal('{{ asset('image/pensums/1.jpg') }}', 'Primer Año - Educación Media General en Ciencia y Tecnología')">
                <div class="relative overflow-hidden h-48">
                    <img src="{{ asset('image/pensums/1.jpg') }}"
                        alt="Primer año - Educación Media General en Ciencia y Tecnología" loading="lazy"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-300">
                    </div>

                    <!-- Badge overlay -->
                    <div
                        class="absolute top-2 right-2 bg-emerald-500/90 text-white text-xs font-bold px-2 py-1 rounded shadow-lg backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                        Ver Detalles
                    </div>
                </div>
                <div class="p-6 relative">
                    <h3 class="text-xl font-bold text-emerald-100 mb-2 group-hover:text-emerald-400 transition-colors">
                        Primer Año</h3>
                    <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">Iniciación en Ciencia y
                        Tecnología con formación integral en
                        valores y conocimientos básicos.</p>
                </div>
            </div>

            <!-- Card 2: Segundo y Tercer Año -->
            <div class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl overflow-hidden hover:border-emerald-500/80 transition-all duration-500 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/20 cursor-pointer"
                onclick="openModal('{{ asset('image/pensums/2.jpg') }}', 'Segundo y Tercer Año - Educación Media General en Ciencia y Tecnología')">
                <div class="relative overflow-hidden h-48">
                    <img src="{{ asset('image/pensums/2.jpg') }}"
                        alt="Segundo y Tercer año - Educación Media General en Ciencia y Tecnología" loading="lazy"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-300">
                    </div>
                    <!-- Badge overlay -->
                    <div
                        class="absolute top-2 right-2 bg-emerald-500/90 text-white text-xs font-bold px-2 py-1 rounded shadow-lg backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                        Ver Detalles
                    </div>
                </div>
                <div class="p-6 relative">
                    <h3 class="text-xl font-bold text-emerald-100 mb-2 group-hover:text-emerald-400 transition-colors">
                        Segundo y Tercer Año</h3>
                    <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">Profundización en
                        ciencias, matemáticas y tecnologías, con
                        enfoque práctico y analítico.</p>
                </div>
            </div>

            <!-- Card 3: Cuarto Año -->
            <div class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl overflow-hidden hover:border-emerald-500/80 transition-all duration-500 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/20 cursor-pointer"
                onclick="openModal('{{ asset('image/pensums/3.jpg') }}', 'Cuarto Año - Educación Media General')">
                <div class="relative overflow-hidden h-48">
                    <img src="{{ asset('image/pensums/3.jpg') }}" alt="Cuarto año - Educación Media General"
                        loading="lazy"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-300">
                    </div>
                    <!-- Badge overlay -->
                    <div
                        class="absolute top-2 right-2 bg-emerald-500/90 text-white text-xs font-bold px-2 py-1 rounded shadow-lg backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                        Ver Detalles
                    </div>
                </div>
                <div class="p-6 relative">
                    <h3 class="text-xl font-bold text-emerald-100 mb-2 group-hover:text-emerald-400 transition-colors">
                        Cuarto Año</h3>
                    <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">Fortalecimiento
                        académico y preparación para la culminación
                        del ciclo medio.</p>
                </div>
            </div>

            <!-- Card 4: Quinto Año -->
            <div class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl overflow-hidden hover:border-emerald-500/80 transition-all duration-500 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/20 cursor-pointer"
                onclick="openModal('{{ asset('image/pensums/4.jpg') }}', 'Quinto Año - Educación Media General')">
                <div class="relative overflow-hidden h-48">
                    <img src="{{ asset('image/pensums/4.jpg') }}" alt="Quinto año - Educación Media General"
                        loading="lazy"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-300">
                    </div>
                    <!-- Badge overlay -->
                    <div
                        class="absolute top-2 right-2 bg-emerald-500/90 text-white text-xs font-bold px-2 py-1 rounded shadow-lg backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                        Ver Detalles
                    </div>
                </div>
                <div class="p-6 relative">
                    <h3 class="text-xl font-bold text-emerald-100 mb-2 group-hover:text-emerald-400 transition-colors">
                        Quinto Año</h3>
                    <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">Cierre del plan
                        académico con proyectos integrales y
                        preparación para la educación superior.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal para mostrar imagen completa -->
<div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-80 p-4"
    onclick="closeModal()">
    <div class="relative max-w-4xl max-h-full">
        <!-- Botón de cerrar -->
        <button onclick="closeModal()"
            class="absolute -top-10 right-0 text-white text-3xl font-bold hover:text-gray-300 focus:outline-none">
            &times;
        </button>
        <!-- Imagen en grande -->
        <img id="modalImage" src="" alt=""
            class="w-full h-auto max-h-[90vh] object-contain rounded-lg shadow-2xl">
        <!-- Título opcional (puedes ocultarlo si no lo necesitas) -->
        <p id="modalCaption" class="text-white text-center mt-2 text-sm"></p>
    </div>
</div>

<!-- Script para manejar el modal -->
<script>
    function openModal(imageSrc, altText) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImage');
        const caption = document.getElementById('modalCaption');

        img.src = imageSrc;
        img.alt = altText;
        caption.textContent = altText;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Evita scroll del fondo
    }

    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = ''; // Restaura scroll
    }

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
