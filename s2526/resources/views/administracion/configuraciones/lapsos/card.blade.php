<div class="col-sm-6 col-md-4 col-lg-4 pl-1 pr-1">
    <div class="card h-100 {{$lapso->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$lapso->logo ?? ''}}" class="card-img-top"
      src="{{ (isset($lapso->logo)) ? asset($lapso->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
        <p class="align-text-bottom">Sección {{$lapso->name}}</p>
      </div>
      <div class="card-footer">
        <p class="card-text">
          <a class="btn btn-warning btn-sm btn-block"
            href="{{ route('administracion.configuraciones.lapso.edit',$lapso->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>
