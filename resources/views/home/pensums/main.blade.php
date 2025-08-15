<x-card>

    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            Descubre Nuestro Pensum Académico: Formación Integral para el Futuro
        </h3>
        <small>

            En el C.E Colegio Fray Luis Amigó, ofrecemos una educación de excelencia en Educación Inicial, Educación Primaria y Media General en Ciencia y Tecnología, diseñada para desarrollar habilidades académicas, personales y sociales en cada estudiante.
        </small>
    @endslot

    <div class=" lg:p-8">
        @include('home.pensums.items')
    </div>
</x-card>
