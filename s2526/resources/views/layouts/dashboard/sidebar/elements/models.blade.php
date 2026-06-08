@php ($model = strstr($nrcrud,'.',true))
<li class="nav-item" title="{{ isset($title) ?  $title:$nombre}}">
  <a class="accordion nav-link  {{ (Request::is('*'.$model.'*') ? ' accordion_active' : '') }}" href="#">
    {{-- <span data-feather="home"></span> --}}
    <i class="{{ $icon ?? '' }} "></i>
    {{$nombre ?? 'default'}} 
    {{-- <span class="sr-only">(current)</span> --}}
  </a>
  <div class="accordion_panel" style="display: {{ (Request::is('*'.$model.'*') ? ' block' : 'none') }}">
      <ul class="nav flex-column">
          @isset($nrcrud)
            <li class="nav-item">
                <a class="nav-link {{ (Request::is('*crud/'.$model.'*') ? ' active' : '') }}" href="{{route($nrcrud)}}">
                  {{-- <span data-feather="home"></span> --}}
                  CRUD {{-- <span class="sr-only">(current)</span> --}}
                </a>                                                        
            </li>
          @endisset
          @isset($nrchart)
            <li class="nav-item">
                <a class="nav-link {{ (Request::is('*charts/'.$model.'*') ? ' active' : '') }}" href="{{route($nrchart)}}">
                  {{-- <span data-feather="home"></span> --}}
                  Gráficas {{-- <span class="sr-only">(current)</span> --}}
                </a>                                                        
            </li>
          @endisset                                            
      </ul>
  </div> 
</li>