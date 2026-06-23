@extends('administracion.layouts.dashboard.app')

@section('title') - Registro de Abonos @endsection

@section('main')

@php
    $representant = $estudiant->representant;
@endphp

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <h3>
                Datos para el registro del Abono<br>
                <small class="text-default">
                    {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                </small>

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}

            </h3>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            <div class="container">
                <h3 class="mb-4">Editar Abono</h3>

                <form action="{{ route('administracion.abonos.update', $abono->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="abono_description">Descripción del Abono</label>
                        <input type="text"
                            name="abono_description"
                            class="form-control {{ $errors->has('abono_description') ? 'is-invalid' : '' }}"
                            value="{{ old('abono_description', $abono->abono_description) }}">
                        @if($errors->has('abono_description'))
                            <div class="invalid-feedback">
                                {{ $errors->first('abono_description') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="status_matriculations">Estado de Matriculación</label>
                        <select name="status_matriculations"
                                class="form-control {{ $errors->has('status_matriculations') ? 'is-invalid' : '' }}">
                            <option value="1" {{ old('status_matriculations', $abono->status_matriculations) == 1 ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ old('status_matriculations', $abono->status_matriculations) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                        @if($errors->has('status_matriculations'))
                            <div class="invalid-feedback">
                                {{ $errors->first('status_matriculations') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="{{ route('administracion.abonos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
    $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                $('#'+name).val(checked); console.log($('#'.name).val());
            });
        });

        $(document).ready(function(){
            $("#cuentaxpagar_id").change(function(){
                var cuentaxpagar_id = $(this).val();console.log(cuentaxpagar_id);
                var url = "{{ route('administracion.abonos.create',[$estudiant->id,'']) }}/"+cuentaxpagar_id;
                window.open(url,'_self')
            });
        });
</script>
@endsection
