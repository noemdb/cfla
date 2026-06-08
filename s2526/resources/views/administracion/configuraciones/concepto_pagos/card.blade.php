<div class="col-sm-4 col-md-3 col-lg-2 pl-1 pr-1">
    <div class="card h-100 {{$cuentaxpagar->status_active_bank=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$cuentaxpagar->logo ?? ''}}" class="card-img-top" src="{{ (isset($cuentaxpagar->logo)) ? asset($cuentaxpagar->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
          <span class="align-text-bottom">{{$cuentaxpagar->name}} {{$cuentaxpagar->description}}</span><br>
          <span class=" small">Vencimiento: {{f_date($cuentaxpagar->date_expiration)}}</span>
          @if ($cuentaxpagar->type == 'INDIVIDUAL')
            <br>
            <span class=" small">Estudiante: {{$cuentaxpagar->estudiant->fullname ?? ''}}</span>              
          @endif
      </div>
      <div class="card-footer p-1" title="Asignar cuentas de cobro">
          <a class="btn btn-info btn-sm btn-block" href="{{ route('administracion.configuraciones.concepto_pagos.create.concept',$cuentaxpagar->id) }}" role="button">Asignar</a>
      </div>
    </div>
</div>





