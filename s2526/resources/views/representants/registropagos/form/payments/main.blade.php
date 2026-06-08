@includeWhen($errors->any(),'representants.registropagos.form.modals.anyErrors')

{{-- /home/nuser/code/s2021/resources/views/representants/registropagos/form/payments/modals/anyErrors.blade.php --}}

{!!Form::open(['route'=>'representants.registropagos.payments.store','method'=>'POST','id'=>'form-create-payment','class'=>'form-signin shadow rounded mb-3 ','files'=>'true','enctype'=>"multipart/form-data"])!!}

    {{-- Representante --}}
    <fieldset class="form_fieldset border rounded pb-2" id="home">
        @php $i = 1 @endphp
        <h4 class="alert alert-light mb-2"><span class="text-dark"><i class="{{$icon_menus['representante'] ?? ''}} text-dark" aria-hidden="true"></i> Representante</span></h4>
        <div class="px-3"> @include('representants/registropagos/form/payments/representant') </div>
        {{-- /home/nuser/code/s2021/resources/views/representants/registropagos/form/payments/representant.blade.php --}}
        <button type="button" name="" id="" class="previous-form btn btn-ligh p-0 ml-3" style="width: 28% !important;" disabled><i class="{{$icon_menus['inicio'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Inicio</span> </button>
        <button type="button" name="" id="" class="next-form btn btn-info p-0" style="width: 28% !important;"><i class="{{$icon_menus['estudiante'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Estudiantes</span></button>
        <button type="button" name="" id="" class="transfer-form btn btn-success p-0 float-right mr-3" style="width: 28% !important;"><i class="{{$icon_menus['banco'] ?? ''}} fa-2x text-light" aria-hidden="true"></i> <span class="d-block small">Trasnferencias</span> </button>
    </fieldset>

    {{-- Estudiantes --}}
    <fieldset class="form_fieldset border rounded pb-2" id="studiant">
        @php $i = 1 @endphp
        <h4 class="alert alert-info"><span class=""> <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i> Estudiante {{$i}}</span></h4>
        <div class="px-3">
            @include('representants.registropagos.form.payments.fields.estudiant')
            {{-- /home/nuser/code/s2021/resources/views/representants/registropagos/form/payments/estudiants.blade.php --}}
        </div>
        <button type="button" name="" id="" class="home-form btn btn-ligh p-0 ml-3" style="width: 28% !important;"><i class="{{$icon_menus['inicio'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Representante</span></button>
        <button type="button" name="" id="" class="next-form btn btn-info p-0" style="width: 28% !important;"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i> <span class="d-block small">Agregar Estudiante</span></button>
        <button type="button" name="" id="" class="transfer-form btn btn-success p-0 float-right mr-3" style="width: 28% !important;"><i class="{{$icon_menus['banco'] ?? ''}} fa-2x text-light" aria-hidden="true"></i> <span class="d-block small">Trasnferencias</span></button>
    </fieldset>
    <fieldset class="form_fieldset border rounded pb-2">
        @php $i = 2 @endphp
        <h4 class="alert alert-info"><span class=""> <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i> Estudiante {{$i}}</span></h4>
        <div class="px-3">
            @include('representants.registropagos.form.payments.fields.estudiant')
        </div>
        <button type="button" name="" id="" class="previous-form btn btn-secondary p-0 ml-3" style="width: 28% !important;"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i> <span class="d-block small">Estudiante anterior</span></button>
        <button type="button" name="" id="" class="next-form btn btn-info p-0" style="width: 28% !important;"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i> <span class="d-block small">Agregar Estudiante</span></button>
        <button type="button" name="" id="" class="transfer-form btn btn-success p-0 float-right mr-3" style="width: 28% !important;"><i class="{{$icon_menus['banco'] ?? ''}} fa-2x text-light" aria-hidden="true"></i> <span class="d-block small">Trasnferencias</span></button>
    </fieldset>
    <fieldset class="form_fieldset border rounded pb-2">
        @php $i = 3 @endphp
        <h4 class="alert alert-info"><span class=""> <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i> Estudiante {{$i}}</span></h4>
        <div class="px-3">
            @include('representants.registropagos.form.payments.fields.estudiant')
        </div>
        <button type="button" name="" id="" class="previous-form btn btn-secondary p-0 ml-3" style="width: 28% !important;"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i> <span class="d-block small">Estudiante anterior</span></button>
        <button type="button" name="" id="" class="transfer-form btn btn-success p-0 float-right mx-3" style="width: 28% !important;"><i class="{{$icon_menus['banco'] ?? ''}} fa-2x text-light" aria-hidden="true"></i> <span class="d-block small">Trasnferencias</span></button>
    </fieldset>

    {{-- Transferencias --}}
    <fieldset class="form_fieldset border rounded pb-2" id="transfer">
        @php $i = 1 @endphp
        <h4 class="alert alert-success"><span class=""> <i class="{{$icon_menus['banco'] ?? ''}} text-success" aria-hidden="true"></i> Transferencia {{$i}}</span></h4>
        <div class="px-3">
            {{-- @include('welcome.payment.form.fields.prepago') --}}
            @include('representants.registropagos.form.payments.fields.prepago')
        </div>
        <button type="button" name="" id="" class="studiant-form btn btn-info p-0 ml-3 " style="width: 28% !important;"><i class="{{$icon_menus['estudiante'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Estudiantes</span></button>
        <button type="button" name="" id="" class="next-form btn btn-success p-0 mr-3 float-right" style="width: 28% !important;"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i> <span class="d-block small">Agregar Transferencia </span></button>
    </fieldset>
    <fieldset class="form_fieldset border rounded pb-2">
        @php $i = 2 @endphp
        <h4 class="alert alert-success"><span class=""> <i class="{{$icon_menus['banco'] ?? ''}} text-success" aria-hidden="true"></i> Transferencia {{$i}}</span></h4>
        <div class="px-3">
            {{-- @include('welcome.payment.form.fields.prepago') --}}
            @include('representants.registropagos.form.payments.fields.prepago')
        </div>
        <button type="button" name="" id="" class="previous-form btn btn-secondary p-0 ml-3" style="width: 28% !important;"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i> <span class="d-block small">Transferencia anterior</span></button>
        {{-- <button type="button" name="" id="" class="next-form btn btn-success p-0 mr-3 float-right" style="width: 28% !important;"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i> <span class="d-block small">Agregar Transferencia </span></button> --}}
        <button type="button" name="" id="" class="home-form btn btn-ligh p-0 ml-3 float-right" style="width: 28% !important;"><i class="{{$icon_menus['inicio'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Representante</span></button>
    </fieldset>
    {{-- <fieldset class="form_fieldset border rounded pb-2">
        @php $i = 3 @endphp
        <h4 class="alert alert-success"><span class=""> <i class="{{$icon_menus['banco'] ?? ''}} text-success" aria-hidden="true"></i> Transferencia {{$i}}</span></h4>
        <div class="px-3">
            @include('welcome.payment.form.fields.prepago')
        </div>
        <button type="button" name="" id="" class="previous-form btn btn-secondary p-0 ml-3" style="width: 28% !important;"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i> <span class="d-block small">Transferencia anterior</span></button>
        <button type="button" name="" id="" class="next-form btn btn-success p-0 mr-3 float-right" style="width: 28% !important;"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i> <span class="d-block small">Agregar Transferencia </span></button>
    </fieldset>
    <fieldset class="form_fieldset border rounded pb-2">
        @php $i = 4 @endphp
        <h4 class="alert alert-success"><span class=""> <i class="{{$icon_menus['banco'] ?? ''}} text-success" aria-hidden="true"></i> Transferencia {{$i}}</span></h4>
        <div class="px-3">
            @include('welcome.payment.form.fields.prepago')
        </div>
        <button type="button" name="" id="" class="previous-form btn btn-secondary p-0 ml-3" style="width: 28% !important;"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i> <span class="d-block small">Transferencia anterior</span></button>
        <button type="button" name="" id="" class="home-form btn btn-ligh p-0 mr-3 float-right" style="width: 28% !important;"><i class="{{$icon_menus['inicio'] ?? ''}} fa-2x" aria-hidden="true"></i> <span class="d-block small">Representante</span></button>
    </fieldset> --}}

    <div class="btn-group btn-block btn-group-sm py-2 px-3 my-2" role="group" aria-label="Basic example">
        {!! Form::submit('Registrar',['class' => 'form-control btn pt-1 mt-1 btn-primary font-weight-bold']) !!}
    </div>

{!! Form::close() !!}
