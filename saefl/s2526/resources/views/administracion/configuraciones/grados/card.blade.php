<div class="col-sm-4 col-md-3 col-lg-2 pl-1 pr-1">
    <div class="bd-callout bd-callout-{{$grado->color ?? 'default'}} h-100">
        <div class="card h-100 {{$grado->status_active=='false'  ? 'border-danger':''}}">
            {{-- <img alt="{{$grado->logo ?? ''}}" class="card-img-top"
            src="{{ (isset($grado->logo)) ? asset($grado->logo) : asset('images/avatar/user_default.png') }}"> --}}
            <div class="card-body p-1">
                <p class="align-text-bottom">{{$grado->name}}</p>
                {{-- <p class="align-text-bottom">{{$grado->name}} ({{$grado->description ?? ''}})</p> --}}
            </div>
            <div class="card-footer">
                <p class="card-text">
                    <a class="btn btn-warning btn-sm btn-block"
                        href="{{ route('administracion.configuraciones.grado.edit',$grado->id) }}"
                        role="button">Editar</a>
                </p>
            </div>
        </div>
    </div>
</div>