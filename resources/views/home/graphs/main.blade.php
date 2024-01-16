<x-card>

    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            Nuestra gestión en gráficos
        </h3>
    @endslot

    <div class="grid md:grid-cols-2 gap-4">
        <div>@include('home.graphs.charts')</div>
        <div>@include('home.graphs.charts')</div>
    </div>

</x-card>