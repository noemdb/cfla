<div class="col-sm-6 col-md-4 col-lg-4 pl-1 pr-1">
    <div class="card h-100 {{$edescriptiva->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$edescriptiva->logo ?? ''}}" class="card-img-top"
      src="{{ (isset($edescriptiva->logo)) ? asset($edescriptiva->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
        <p class="align-text-bottom">Sección {{$edescriptiva->name}}</p>
      </div>
      <div class="card-footer">
        <p class="card-text">
          <a class="btn btn-warning btn-sm btn-block"
            href="{{ route('administracion.configuraciones.seccion.edit',$edescriptiva->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>
