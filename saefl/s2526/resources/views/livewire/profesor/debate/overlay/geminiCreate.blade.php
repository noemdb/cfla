<div class="">
    <div class="overlay border rounded m-4 p-0 shadow-lg ">

        <div class="d-flex justify-content-betwee table-secondary p-1 m-1 rounded">
            <div class="flex-grow-1 h6 p-1">
                <strong>Generador IA de una Competición</strong>
                <span class="input-text px-4 font-weight-bold" wire:loading>
                    <strong>Procesando...</strong>
                </span>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>

        <div class="px-1 mx-1 pt-2">

            <div class="container mt-1">
                <div class=" font-weight-bold h6">Listado de actividades por Grado/Año: </div>

                <div class="form-group">
                    <label for="grado_id" class=" font-weight-bold m-0 small">Grado/Año</label>
                    {!! Form::select('grado_id', $list_grado, old('grado_id'), [
                        'wire:model' => 'grado_id',
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('grado_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <ul class="list-group text-sm">
                    @forelse ($activities as $item)
                        @php $grado = $item->getGrado() @endphp
                        <li
                            class="list-group-item {{ $item->status ? 'font-weight-bold text-success list-group-item-success' : null }}">
                            <div class="d-flex justify-content-between align-items-center ">
                                <div class="text-center font-weight-bold px-2">{{ $loop->iteration }}</div>
                                <div class="flex-grow-1 px-2">
                                    <div>Grado/Año: {{ $grado->name ?? null }}</div>
                                    <div>Tema: {{ ucfirst_accents($item->topic) }}</div>
                                    <div>Tejido: {{ ucfirst_accents($item->thematic) }}</div>
                                    <div>{{ $item->status ? 'Aprobada' : 'En revisión' }}</div>
                                </div>
                                <div class="flex-shrink-1">
                                    <input wire:model.defer="checkboxes.{{ $item->id }}" type="checkbox"
                                        aria-label="Check 1" class="form-control">
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item disabled">
                            No hay actividades
                        </li>
                    @endforelse
                </ul>

                @error('checkboxes')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

            </div>

            <div class="input-group p-1 m-1">
                {{-- {!! Form::button('Generar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'generateCompetition()']) !!} --}}
                {!! Form::button('Generar', [
                    'class' => 'form-control btn btn-primary',
                    'wire:click' => 'aiGenerateCompetition()',
                ]) !!}
            </div>

        </div>

    </div>
</div>

{{-- <div class="overlay-background"></div> --}}
