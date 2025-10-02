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



<div class="border border-gray-200">
      <iframe
        src="https://makeform.ai/e/cjO2oCgV"
        width="100%"
        height="333"
        style="border: none; margin: 0; padding: 0;"
        title="Formulario de inscripción - Danzas Joropo Recio"
        loading="lazy"
      ></iframe>
</div>

@section('customScripts')
@parent
<script async src="https://www.makeform.ai/widgets/embed.js"></script>
@endsection
    

</x-card>
