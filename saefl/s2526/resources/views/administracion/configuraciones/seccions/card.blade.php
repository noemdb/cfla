<div class="col-sm-6 col-md-4 col-lg-4 pl-1 pr-1">
    <div class="card h-100 {{$seccion->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$seccion->logo ?? ''}}" class="card-img-top"
      src="{{ (isset($seccion->logo)) ? asset($seccion->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
        <p class="align-text-bottom">Sección {{$seccion->name}}</p>
      </div>
      <div class="card-footer">
        <p class="card-text">
          <a class="btn btn-warning btn-sm btn-block"
            href="{{ route('administracion.configuraciones.seccion.edit',$seccion->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>