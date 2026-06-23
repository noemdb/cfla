<div class="col-sm-6 col-md-4 col-lg-4 pl-1 pr-1">
    <div class="card h-100 {{$area_conocimiento->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$area_conocimiento->logo ?? ''}}" class="card-img-top"
      src="{{ (isset($area_conocimiento->logo)) ? asset($area_conocimiento->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
        <p class="align-text-bottom">Sección {{$area_conocimiento->name}}</p>
      </div>
      <div class="card-footer">
        <p class="card-text">
          <a class="btn btn-warning btn-sm btn-block"
            href="{{ route('administracion.configuraciones.area_conocimiento.edit',$area_conocimiento->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>
