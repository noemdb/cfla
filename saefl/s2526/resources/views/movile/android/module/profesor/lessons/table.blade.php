<!-- resources/views/livewire/show-lessons.blade.php -->

<div>
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Objetivos</th>
                <!-- Agrega las demás columnas según tus necesidades -->
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                <tr>
                    <td>{{ $lesson->title }}</td>
                    <td>{{ $lesson->description }}</td>
                    <td>{{ $lesson->objectives }}</td>
                    <!-- Muestra las demás propiedades de la lección en las columnas correspondientes -->
                </tr>
            @empty

                <tr>
                    <td colspan="3">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>