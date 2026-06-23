<div class="col-sm-6 col-md-6 col-lg-4 pl-1 pr-1">
  <div class="card h-100" style="max-width: 540px;">
    <div class="row no-gutters">
      <div class="col-md-2">
          <img alt="{{$padre->logo ?? ''}}" class="card-img-top" src="{{ (isset($padre->logo)) ? asset($padre->logo) : asset('images/avatar/user_default.png') }}">
      </div>
      <div class="col-md-8">
        <div class="card-body">
            <small class="align-text-bottom text-mute">
                {{$representant->name}}<br>
                CI: {{$representant->ci_representant}}<br>
            </small>
        </div>        
      </div>
      <div class="col-md-2 pt-1 pr-1 pb-1">
          @include('administracion.representants.button.card') 
      </div>
    </div>
    @if (Auth::user()->IsAdmin())    
      <div class="card-footer p-1">
        falta hacer el resumen             
      </div>
    @endif
  </div>
</div>