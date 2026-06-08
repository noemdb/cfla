<div class="form-group pt-2">
    <label for="representant_id" class="m-0">{{ $list_comment['representant_id'] }}</label>
    <div class="form-group">
        <div class="container-fluid">
            <div class="row">

                <div class="col-3 px-1">
                    <div class="input-group-append pb-1" style="z-index: 0;">
                        {!! Form::text('name', old('name'), [
                            'wire:model' => 'name',
                            'class' => 'form-control',
                            'placeholder' => 'nombre',
                            'id' => 'name',
                        ]) !!}
                    </div>
                </div>

                <div class="col-6 px-1">
                    {!! Form::select('representant_id', $list_representant, old('representant_id'), [
                        'wire:model.defer' => 'representant_id',
                        'class' => 'form-control',
                        'id' => 'representant_id',
                    ]) !!}
                </div>

                <div class="col-3 px-1">
                    {!! Form::button('Selecionar', ['class' => 'btn btn-primary w-100', 'wire:click' => 'loadData()']) !!}
                </div>

            </div>

        </div>

    </div>
</div>

@if ($statusLoad)

    <nav>
        <div class="nav nav-tabs nav-fill flex-column flex-sm-row" id="nav-tab" role="tablist">
            <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                aria-controls="nav-home" aria-selected="true">Generales</a>
            <a class="nav-link {{ $registro_pago_combinado_id ? null : 'disabled' }}" id="nav-profile-tab"
                data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile"
                aria-selected="false">Datos de la operación</a>
        </div>
    </nav>

    <div class="tab-content border border-top-0 rounded" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="p-2">
                @include('livewire.administracion.refund.form.partials.general')
            </div>
        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="p-2">
                @include('livewire.administracion.refund.form.partials.ingresos')
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger fade show shadow-sm py-1 my-2" role="alert">
            <button type="button" class="close p-1 m-1 float-right" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="font-weight-bold text-danger">Errores encontrados, revise detalladamente</div>
            <hr>
            <ul class="px-1">
                @foreach ($errors->all() as $error)
                    <li class="small font-weight-bold">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@endif
