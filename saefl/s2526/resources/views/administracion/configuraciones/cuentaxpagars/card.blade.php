<div class="col-sm-4 col-md-3 col-lg-3 pl-1 pr-1 pb-2">
    <div class="card h-100 {{$cuentaxpagar->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$cuentaxpagar->logo ?? ''}}" class="card-img-top" src="{{ (isset($cuentaxpagar->logo)) ? asset($cuentaxpagar->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
          <span class="align-text-bottom">{{$cuentaxpagar->name}} {{$cuentaxpagar->description}}</span><br>
          <span class=" small">Vencimiento: {{f_date($cuentaxpagar->date_expiration)}}</span>
          @if ($cuentaxpagar->type == 'INDIVIDUAL')
            <br>
            <span class=" small">Estudiante: {{$cuentaxpagar->estudiant->fullname ?? ''}}</span>              
          @endif
      </div>
      <div class="card-footer p-1 text-center">    
          {{-- <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">     --}}
            <div class="btn-group btn-group-sm text-center" role="group" aria-label="Basic example">
              <a title="Editar Cuenta por Pagar" class="btn btn-warning btn-sm" href="{{ route('administracion.configuraciones.cuentaxpagars.edit',$cuentaxpagar->id) }}" role="button">
                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                {{-- Editar --}}
              </a>
              <a title="Asignar Cuentas" class="btn btn-info btn-sm" href="{{ route('administracion.configuraciones.concepto_pagos.create.concept',$cuentaxpagar->id) }}" role="button">
                <i class="{{ $icon_menus['cuentas_cobrar'] ?? '' }} fa-1x"></i>
                {{-- Asign. Conceptos --}}
              </a>
            </div>    
          {{-- </div> --}}
      </div>
    </div>
</div>