<div class="btn-group">
    <button class="btn btn-success p-0 dropdown-toggle p-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{-- <i class="fa fa-bars" aria-hidden="true"></i> --}}
        <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a title="Histórico de pagos" class="dropdown-item p-2"
            href="{{ route('administracion.estudiants.historico',['estudiant_id'=>$estudiant->id]) }}" role="button">
            <i class="{{ $icon_menus['historico'] ?? '' }} fa-1x btn btn-light"></i>
            Histórico de pagos
        </a>
        {{-- <a title="Registro de Pagos" class="dropdown-item p-2"
            href="{{ route('administracion.registropagos.representant.create',['id'=>$estudiant->representant->id]) }}" role="button">
            <i class="{{ $icon_menus['registro_pagos'] }}  btn btn-secondary fa-1x"></i>
            Registro de Pagos
        </a> --}}
        <a title="Iniciar Asistente de Registro de Pagos.." class="dropdown-item p-2"
            {{-- href="{{ route('administracion.registropagos.asistent.representant.create',$estudiant->representant->id) }}" --}}
            href="{{ route('administracion.registropagos.asistent.representant.create',['id'=>$representant->id]) }}"
            role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} btn btn-light text-success fa-1x"></i>
            Asistente de Registro de Pagos
        </a>

        {{-- <a title="Iniciar Asistente de Registro de Pagos" class="btn btn-success btn-sm" href="{{ route('administracion.registropagos.asistent.representant.create',$representant->id) }}" role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
        </a> --}}

        @if ($estudiant->ammount_expire_bill==0)
            <a title="Solvencia Administrativa" class="dropdown-item {{ ($estudiant->ammount_expire_bill<>0) ? 'disabled':'' }} p-2" target="_blank"
                href="{{ route('administracion.administrativas.solvencia.pdf',$estudiant->id) }}" role="button">
                <i class="{{ $icon_menus['documento'] }} fa-1x btn btn-light"></i>
                Solvencia
            </a>
            {{-- <a title="Retirar Estudiante" class="dropdown-item btn-retirar" href="#" data-id="{{$estudiant->id ?? ''}}">
                <i class="fas fa-sign-out-alt btn btn-danger"></i>
                Retirar
            </a> --}}
            {{-- @if (!empty($estudiant->administrativa->id))
                <a title="Constancia de Inscripción Administrativa" class="dropdown-item {{ (empty($estudiant->administrativa->id)) ? 'disabled':'' }}" target="_blank"
                    href="{{ route('administracion.administrativas.constancia.pdf',$estudiant->id) }}" role="button">
                    <i class="{{ $icon_menus['documento'] }} fa-1x btn btn-info"></i>
                    Constancia de Insc.
                </a>
            @endif --}}
        @endif

        <a title="Retiros Administrativos" class="dropdown-item p-2"
            href="{{ route('administracion.administrativas.retiro',['search'=>$estudiant->ci_estudiant]) }}" role="button">
            <i class="{{ $icon_menus['retiro'] }} btn btn-light fa-1x"></i>
            R. Administrativos
        </a>
    </div>
</div>
