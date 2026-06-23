<ol class="ml-1 pl-1 small text-muted">
    @foreach ($evaluacions as $evaluacion)
    <li>
        {{ $evaluacion->description ?? ''}} [{{ $evaluacion->escala->name ?? ''}} ]
        <a title="Editar" class="btn-link text-dark"  href="{{route('administracion.evaluacions.edit',$evaluacion->id)}}" role="button">
            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x small"></i>
        </a>
    </li>
    @endforeach
</ol>
