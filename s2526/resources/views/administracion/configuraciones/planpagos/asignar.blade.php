@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header secondary">
                <h4>
                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                    Inscripciones Administrativa para el período escolar {{ Session::get('pescolar_name') }}
                    {{-- <small class="small text-dark float-right">
                        <strong><span id="user_estudiant">{{$estudiants->count()}}</span> Estudiantes</strong>
                    </small> --}}
                    
                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}

                        {{-- @include('admin.users.menus.index') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                </h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok') 

                {!! Form::open(['route' => 'administracion.configuraciones.planpagos.set.plan', 'method' => 'POST', 'id'=>'form-planpagos-set', 'class'=>'form-signin']) !!}
                    <div class="form-group pt-2">
                        <label for="planpago_id" class="m-0 font-weight-bold">Plan de Pago</label>
                        {!!Form::select('planpago_id',$planpago_list,$planpago->id,['class'=>'form-control','id'=>'planpago_id','required'=>'required']);!!}
                    </div>
                    {{-- Partial con el listado --}}
                    @include('administracion.configuraciones.planpagos.table.asignar')
                {!! Form::close() !!}
                
            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script  type="text/javascript">
        $(document).ready(function(){
            $("#planpago_id").change(function(){
                var planpago_id = $(this).val();console.log(planpago_id);
                var url = "{{ route('administracion.configuraciones.planpagos.asignar','') }}/"+planpago_id;
                window.open(url,'_self')
            });
        });
    </script>

@endsection