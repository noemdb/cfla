<div class="col-sm-4 col-md-3 col-lg-2 pl-1 pr-1">
    <div class="card h-100 {{$banco->status_active_bank=='false'  ? 'border-danger':''}}">
      <img alt="{{$banco->logo ?? ''}}" class="card-img-top" src="{{ (isset($banco->logo)) ? asset($banco->logo) : asset('images/avatar/user_default.png') }}">
      <div class="card-body p-1">
          <p class="align-text-bottom">{{$banco->name}} ({{$banco->number_id_bank}})</p>
      </div>
      <div class="card-footer">
            <p class="card-text"><a class="btn btn-warning btn-sm btn-block" href="{{ route('administracion.configuraciones.bancoedit',$banco->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>





