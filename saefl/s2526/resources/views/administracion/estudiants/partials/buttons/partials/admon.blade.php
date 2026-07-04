
<a title="Registro de Pagos" class="btn btn-secondary btn-sm"
    href="{{ route('administracion.registropagos.representant.create',['id'=>$estudiant->representant->id]) }}" role="button">
    <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
    {{-- Registro de Pagos --}}
</a>
<a title="Registro de Pagos Parcial" class="btn btn-dark btn-sm disabled"
    href="{{ route('administracion.registropagos.parcial.create',['id'=>$estudiant->id]) }}" role="button">
    <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
    {{-- Registro de Pagos Parcial --}}
</a>
<a title="Histórico de pagos" class="btn btn-danger btn-sm"
    href="{{ route('administracion.estudiants.historico',['estudiant_id'=>$estudiant->id]) }}" role="button">
    <i class="{{ $icon_menus['historico'] ?? '' }} fa-1x"></i>
    {{-- Histórico de pagos --}}
</a>
{{-- Solvencia --}}
{{-- @if ($estudiant->ammount_expire_bill==0)
    <a title="Solvencia Administrativa" class="btn btn-info btn-sm {{ ($estudiant->ammount_expire_bill<>0) ? 'disabled':'' }}" target="_blank"
        href="{{ route('administracion.administrativas.solvencia.pdf',$estudiant->id) }}" role="button">
        <i class="{{ $icon_menus['documento'] }} fa-1x "></i>
    </a>
@endif --}}
