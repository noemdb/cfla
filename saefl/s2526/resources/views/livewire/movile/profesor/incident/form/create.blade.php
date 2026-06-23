<div>

    <div class="p-1 m-1 border rounded shadow">
        <i class="bi bi-file-earmark-plus text-primary" style="font-size: 2rem"></i>
        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Registrar nueva <b>Incidencia</b>
        </h5>

        @if (Session::has('operp_ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ Session::get('operp_ok') }}
            </div>
        @endif

        <div class=" p-2 m-2">

            <div><span class="fw-bold">Estudiante:</span> Buscar por nombre o cédula</div>
            <div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person-fill" style="font-size: 1rem"></i> </span>
                    {!! Form::text('search', $search, [
                        'class' => 'form-control',
                        'wire:model.debounce.500ms' => 'search',
                        'placeholder' => 'Buscar Nombre o Cédula',
                    ]) !!}
                </div>
            </div>

            <div>
                {!! Form::select('estudiant_id', $list_estudiants, $estudiant_id, [
                    'wire:model' => 'estudiant_id',
                    'class' => 'form-select',
                    'id' => 'estudiant_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <hr>

            @if ($estudiant_id)
                <div class="text-start">
                    @include('livewire.movile.profesor.incident.form.fields')
                </div>
                <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                    {!! Form::button('Guardar', ['class' => 'form-control btn pt-1 mt-1 btn-primary', 'wire:click' => 'save()']) !!}
                </div>
            @endif

        </div>

    </div>

</div>
