<div class="container">
    <div class="row">
        <div class="col-xs-6">
            <img alt="{{$inscripcion->logo ?? ''}}" class="card-img-top" src="{{ (isset($inscripcion->logo)) ? asset($inscripcion->estudiant->logo) : asset('images/avatar/user_default.png') }}">
            {{$inscripcion->name ?? ''}} {{$inscripcion->lastname ?? ''}}<br>
            CI: {{$inscripcion->ci_estudiant ?? ''}}<br>
            {{$inscripcion->grados_name ?? ''}} {{$inscripcion->seccions_name?? ''}}
        </div>

        <div class="col-xs-6">
            <div class="btn-group-vertical btn-block" role="group" aria-label="Basic example">
                <a title="Constancia Inscripción" class="btn btn-info btn-sm btn-block" target="_blank" href="{{ route('administracion.configuraciones.plan_beneficos.constancia.pdf',$inscripcion->estudiant_id) }}" role="button">
                    C. Inscripción
                </a>
                {{-- <a class="btn btn-info btn-sm btn-block" target="_blank" href="{{ route('administracion.configuraciones.plan_beneficos.constancia.estudio.pdf',$inscripcion->estudiant_id) }}" role="button">
                    Constancia Estudio
                </a> --}}
            </div>            
        </div>
        
    </div>
</div>

