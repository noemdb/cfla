<a title="Editar datos del estudiantes" class="btn btn-warning btn-sm"
    href="{{ route('administracion.estudiants.edit',['id'=>$estudiant->id]) }}" role="button">
    <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
    {{-- Editar datos del estudiantes --}}
</a>

{{-- Retirar --}}
{{-- @if ($estudiant->ammount_expire_bill==0)
    <a title="Retirar Estudiante" class="btn btn-danger btn-sm" href="#" data-id="{{$estudiant->id ?? ''}}">
        <i class="fas fa-sign-out-alt"></i>
    </a>
@endif --}}
