{!! Form::open(['route'=>'administracion.asisst_controls.assit_attendances.format','method'=>'GET','class'=>'p-1 m-1', 'role'=>'search','files'=>'true']) !!}

<div class="alert border rounded py-2 my-2">

            <div class="row font-weight-bold">
                <div class="col-10">
                    <i class="{{ $icon_menus['date'] ?? '' }} text-primary" aria-hidden="true"></i>
                    Rango de Fechas
                </div>
                <div class="col-2">&nbsp;</div>
            </div>

            <div class="row">

                <div class="col-2">
                    <div class="form-group">
                        <label class="font-weight-bold text-secondary m-0" for="finicial">Fecha Inicial</label>
                        {!! Form::date('finicial', $finicial,['class'=>'form-control','id'=>'finicial','placeholder'=>'Fecha Inicial','required'=>'required']) !!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label class="font-weight-bold text-secondary m-0" for="ffinal">Fecha Final</label>
                        {!! Form::date('ffinal', $ffinal,['class'=>'form-control','id'=>'ffinal','placeholder'=>'Fecha Final','required'=>'required']) !!}
                    </div>
                </div>

                {{--  --}}
                <div class="col-3">
                    <div class="form-group">
                        <label class="font-weight-bold text-secondary m-0" for="assit_schedule_id">Horarios</label>
                        {!! Form::select('assit_schedule_id',$list_assit_schedule,$assit_schedule_id,['class'=>'form-control','id'=>'assit_schedule_id','placeholder'=>'Seleccione']);!!}
                    </div>
                </div>

                <div class="col-3">
                    <div class="form-group">
                        <label class="font-weight-bold text-secondary m-0" for="cargo_id">Cargo</label>
                        {!! Form::select('cargo_id',$list_cargos,$cargo_id,['class'=>'form-control','id'=>'cargo_id','placeholder'=>'Seleccione']);!!}
                    </div>
                </div>

                <div class="col-2">
                    <label class="font-weight-bold text-secondary m-0" for="rol_id">&nbsp;</label>
                    <div class="btn-group btn-group btn-block">
                        <button class="btn btn-info my-2 my-sm-0 btn-block" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            Buscar
                        </button>
                        <button class="btn btn-dark my-2 my-sm-0" id="btn_toprint" type="button">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

            </div>

    </div>

{!! Form::close() !!}


@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#btn_toprint').click(function (e) {
                e.preventDefault();
                var cargo_id = $('#cargo_id').val();	//console.log(ci_estudiant);
                var assit_schedule_id = $('#assit_schedule_id').val();  //console.log(ci_estudiant);
                var finicial = $('#finicial').val();	//console.log(ci_estudiant);
                var ffinal = $('#ffinal').val();	//console.log(ci_estudiant);
                var dataString = '?cargo_id='+cargo_id+'&assit_schedule_id='+assit_schedule_id+'&finicial='+finicial+'&ffinal='+ffinal; console.log(dataString);
                var url = "{{ route('administracion.asisst_controls.assit_attendances.formato') }}"+dataString;
                window.open(url,'_blank');
            });
        });
    </script>
@endsection
