<a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
    <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
        <b>{{$estudiant->fullname}}</b>
    </span>
</a>
