@include('administracion.elements.forms.errors')

@include('administracion.elements.messeges.oper_ok')

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach ($bancos as $banco)
            <a class="nav-item nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                id="nav-header-tab-banco-{{ $banco->id }}" title="{{ $banco->name ?? '' }}" data-toggle="tab"
                href="#nav-content-banco-{{ $banco->id }}" role="tab" aria-controls="nav-home" aria-selected="true">
                {{-- {{$banco->abbreviation ?? ''}} --}}
                <span class="font-weight-bold">
                    Banco {{ empty($banco->abbreviation) ? $loop->iteration : $banco->abbreviation }}
                </span>
            </a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">

    @foreach ($bancos as $banco)
        <div class="tab-pane fade {{ $loop->iteration == 1 ? ' show active ' : '' }}"
            id="nav-content-banco-{{ $banco->id }}" role="tabpanel"
            aria-labelledby="nav-header-home-tab-{{ $banco->id }}">
            <div class="p-2">

                {!! Form::model($banco, [
                    'route' => ['administracion.configuraciones.bancoupdate', $banco->id],
                    'method' => 'PUT',
                    'id' => 'form-update-autoridad_' . $banco->id,
                    'role' => 'form',
                ]) !!}

                @include('administracion.configuraciones.banco.form.fields')

                {{-- @if (Auth::user()->isAdmin()) --}}
                <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update"
                    id="btn-update-banco-{{ $banco->id }}">
                    <i class="far fa-save"></i>
                    Actualizar
                </button>
                {{-- @endif --}}

                {!! Form::close() !!}

                {{-- 
                @section('scripts')
                    @parent
                    <script type="text/javascript">
                        $(document).ready(function() {
                            if ( {{(Auth::user()->isAdmin()) ? 0:1}} ) {
                                $('#form-update-autoridad_'+{{ $banco->id ?? ''}}).find('input, textarea, button, select').attr('disabled','disabled');
                            }
                        });
                    </script>
                @endsection 
                --}}

            </div>
        </div>
    @endforeach
</div>
