<div class="col-sm-4 col-md-3 col-lg-2 pl-1 pr-1">
    <div class="card h-100 {{$peducativo->status_active=='false'  ? 'border-danger':''}}">
      {{-- <img alt="{{$peducativo->logo ?? ''}}" class="card-img-top" src="{{ (isset($peducativo->logo)) ? asset($peducativo->logo) : asset('images/avatar/user_default.png') }}"> --}}
      <div class="card-body p-1">
          <p class="align-text-bottom">{{$peducativo->name}}</p>
      </div>
      <div class="card-footer">
            <p class="card-text"><a class="btn btn-warning btn-sm btn-block" href="{{ route('administracion.configuraciones.peducativo.edit',$peducativo->id) }}" role="button">Editar</a></p>
      </div>
    </div>
</div>





