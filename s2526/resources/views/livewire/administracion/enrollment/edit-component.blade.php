<div>

    {{-- <div class="text-center">
        <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
    </div> --}}

    <h2 class="text-success text-center">Renovación de Matrícula</h2>

    <div class="font-weight-bold text-center h5 ">Asistente</div>

    <div class="alert alert-primary">
        REPRESENTANTE: <br><strong>{{$representant->name ?? null}}</strong>. CI: <i>{{$representant->ci_representant ?? null}}</i>
    </div>

    <div id="estudiant-selected">

        {{-- <div class="text-muted mb-2 border-bottom">Ingrese toda la información requerida</div> --}}

        @include('livewire.administracion.enrollment.elements.errors')

        @include('livewire.administracion.enrollment.form.main')

        @if($step==$limit)
        <hr>
        {!! Form::button('Guardar', ['class' => 'form-control btn pt-1 mt-1 btn-primary w-100', 'wire:click' => 'save()']) !!}
        @endif

    </div>

</div>



