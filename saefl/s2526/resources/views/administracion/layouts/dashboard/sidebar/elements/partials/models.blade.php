{{-- @if(Auth::user()->getSetting('sidebar_models_'.$models)=='true') --}}
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fa fa-{{$icon ?? 'asterisk'}} text-{{$class ?? 'default'}}" aria-hidden="true"> </i>
            {{$titulo ?? 'default'}} <i class="fas fa-caret-right"></i>
        </a>
        <ul class="nav nav-third-level">
            {{-- @if(Auth::user()->getSetting('sidebar_models_'.$models.'_crud')=='true') --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ url ('admin/models/crud/'.$models) }}">
                        <i class="fas fa-list text-purple" aria-hidden="true"> </i>
                        Listado
                    </a>
                </li>
            {{-- @endif --}}
            {{-- @if(Auth::user()->getSetting('sidebar_models_'.$models.'_chart')=='true') --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ url ('admin/models/charts/'.$models) }}">
                    <i class="fas fa-chart-pie text-teal"></i>
                    Gráficas
                </a>
            </li>
            {{-- @endif --}}
        </ul>
        <!-- /.nav-third-level -->
    </li>
{{-- @endif --}}

<a class="accordion nav-link" href="#">
<span data-feather="home"></span>
Tasks {{-- <span class="sr-only">(current)</span> --}}
</a>
<div class="accordion_panel">
  <ul class="nav flex-column">
      <li class="nav-item">
          <a class="nav-link" href="{{ route('tasks.index') }}">
            <span data-feather="home"></span>
            Listado {{-- <span class="sr-only">(current)</span> --}}
          </a>                                                        
      </li>
      <li class="nav-item">
          <a class="nav-link" href="#">
            <span data-feather="home"></span>
            Gráficas {{-- <span class="sr-only">(current)</span> --}}
          </a>                                                        
      </li>                                            
  </ul>
</div> 