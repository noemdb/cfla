<!-- Sección de Oferta Académica - Pensum con Modal de Imagen -->
<section class="py-12 px-6 bg-gray-50">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Nuestro Pensum Académico</h2>
        <p class="text-lg text-gray-600 mb-10">
            Conoce los años académicos de nuestro plan de Educación Media General en Ciencia y Tecnología.
        </p>

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Primer Año -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                <img src="{{ asset('image/pensums/1.jpg') }}"
                    alt="Primer año - Educación Media General en Ciencia y Tecnología" loading="lazy"
                    class="w-full h-48 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('image/pensums/1.jpg') }}', 'Primer Año - Educación Media General en Ciencia y Tecnología')">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800">Primer Año</h3>
                    <p class="text-gray-600 text-sm mt-1">Iniciación en Ciencia y Tecnología con formación integral en
                        valores y conocimientos básicos.</p>
                </div>
            </div>

            <!-- Card 2: Segundo y Tercer Año -->
            <div
                class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                <img src="{{ asset('image/pensums/2.jpg') }}"
                    alt="Segundo y Tercer año - Educación Media General en Ciencia y Tecnología" loading="lazy"
                    class="w-full h-48 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('image/pensums/2.jpg') }}', 'Segundo y Tercer Año - Educación Media General en Ciencia y Tecnología')">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800">Segundo y Tercer Año</h3>
                    <p class="text-gray-600 text-sm mt-1">Profundización en ciencias, matemáticas y tecnologías, con
                        enfoque práctico y analítico.</p>
                </div>
            </div>

            <!-- Card 3: Cuarto Año -->
            <div
                class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                <img src="{{ asset('image/pensums/3.jpg') }}" alt="Cuarto año - Educación Media General" loading="lazy"
                    class="w-full h-48 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('image/pensums/3.jpg') }}', 'Cuarto Año - Educación Media General')">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800">Cuarto Año</h3>
                    <p class="text-gray-600 text-sm mt-1">Fortalecimiento académico y preparación para la culminación
                        del ciclo medio.</p>
                </div>
            </div>

            <!-- Card 4: Quinto Año -->
            <div
                class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                <img src="{{ asset('image/pensums/4.jpg') }}" alt="Quinto año - Educación Media General" loading="lazy"
                    class="w-full h-48 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('image/pensums/4.jpg') }}', 'Quinto Año - Educación Media General')">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800">Quinto Año</h3>
                    <p class="text-gray-600 text-sm mt-1">Cierre del plan académico con proyectos integrales y
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
