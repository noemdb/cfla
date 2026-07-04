@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','filters')
    @slot('title','Opciones')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')

    <div class="container-fluid">

        <div class="row">
            <div class="col-6">
                <div class="col-6">Fecha Inicial</div>
                {!! Form::date('finicial', $finicial,['class'=>'form-control','id'=>'finicial']) !!}
            </div>
            <div class="col-6">
                <div class="col-6">Fecha Final</div>
                {!! Form::date('ffinal', $ffinal,['class'=>'form-control','id'=>'ffinal']) !!}
            </div>
        </div>

    </div>



    {{-- <a name="" id="" class="btn btn-primary" href="#" role="button"></a> --}}
        {{-- @include('administracion.registropagos.form.modal.adelantados.fields') --}}
    @endslot
@endcomponent
