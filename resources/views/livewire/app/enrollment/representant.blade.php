<x-modal-card title="Datos del Representante" blur wire:model.live="modalAssistent">

    <div>Nombre: {{$representant->name ?? null}}</div>

    @php $estudiants = $representant->estudiants_formaly @endphp
    <div>Estudiantes:</div>
    @forelse ($estudiants as $estudiant)
        <div>{{$estudiant->name ?? null}} {{$estudiant->name ?? null}} {{$estudiant->full_inscripcion ?? null}}</div>
    @empty
        <div class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"> No hay estudiantes </div>
    @endforelse


</x-modal-card>