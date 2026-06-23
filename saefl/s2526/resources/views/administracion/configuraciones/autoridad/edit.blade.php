@include('administracion.elements.forms.errors')

@include('administracion.elements.messeges.oper_ok')

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach($autoridads as $autoridad)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-asignacion-{{$autoridad->id}}"
                data-toggle="tab" href="#nav-content-asignacion-{{$autoridad->id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                {{-- {{$autoridad->tautoridad->name ?? ''}} --}}
                <span class="font-weight-bold">Autoridad {{ $loop->iteration ?? '' }}</span>
            </a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">

    @foreach($autoridads as $autoridad)

        <div class="tab-pane fade {{($loop->iteration==1) ? ' show active ':''}}" id="nav-content-asignacion-{{$autoridad->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$autoridad->id}}">
            <div class="p-2">

                {!! Form::model($autoridad,['route' => ['administracion.configuraciones.autoridadupdate', $autoridad->id], 'method' => 'PUT', 'id'=>'form-update-autoridad_'.$autoridad->id, 'role'=>'form']) !!}

                    @include('administracion.configuraciones.autoridad.form.fields',$autoridad)

                    @if (Auth::user()->isAdmin())
                        <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-autoridad-{{$autoridad->id}}">
                            <i class="far fa-save"></i>
                            Actualizar
                        </button>
                    @endif

                {!! Form::close() !!}

                @section('scripts')
                    @parent
                    <script type="text/javascript">
                        $(document).ready(function() {
                            if ( {{(Auth::user()->isAdmin()) ? 0:1}} ) {
                                $('#form-update-autoridad_'+{{ $autoridad->id ?? ''}}).find('input, textarea, button, select').attr('disabled','disabled');
                            }

                        });
                    </script>
                @endsection

            </div>
        </div>
    @endforeach
</div>
