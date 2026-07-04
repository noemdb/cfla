<div class="col-sm-4 col-md-3 col-lg-2 pl-1 pr-1">
    <div class="card h-100 {{$pestudio->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$pestudio->logo ?? ''}}" class="card-img-top" src="{{ (isset($pestudios->logo)) ? asset($pestudios->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
          <p class="align-text-bottom">{{$pestudio->name}} ({{$pestudio->code ?? ''}})</p>
      </div>
      <div class="card-footer">
            <p class="card-text"><a class="btn btn-warning btn-sm btn-block" href="{{ route('administracion.configuraciones.pestudio.edit',$pestudio->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>





