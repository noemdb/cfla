@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_ci="";
    $class_gsuite="";
    $class_phone="";
    $class_username="";
    $class_action="nosort";
    $user = Auth::user();
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
        <thead>
            <tr class="table-secondary">
                <th class="{{ $class_N }}">N</th>
                
                <th class="{{ $class_profesor }}">Nombre</th>
                <th class="{{ $class_ci }} text-center">
                    <span title="Carga académica">C.Académica</span><br>
                    @foreach ($lapsos as $lapso)
                        <span class="badge badge-{{ $lapso->class ?? secondary }}" style="font-size: 0.6rem" title="{{$lapso->name}}">
                            {{ $lapso->code_sm }}
                        </span>
                    @endforeach
                </th>
                <th class="{{ $class_profesor }} text-center">
                    <span title="Plan de Actividades">P.Actividades</span><br>
                    @foreach ($lapsos as $lapso)
                        <span class="badge badge-{{ $lapso->class ?? secondary }}" style="font-size: 0.6rem" title="{{$lapso->name}}">
                            {{ $lapso->code_sm }}
                        </span>
                    @endforeach
                </th>
                @if ($user->group == 'imployeds')
                <th class="{{ $class_username }}">N.Usuario</th>
                <th class="{{ $class_gsuite }}">Email/Teléfono</th>
                <th class="{{ $class_gsuite }}">GSuite</th>
                @endif 

            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($profesors as $profesor)

            @php $pevaluacions = $profesor->pevaluacions; $activities = $profesor->activities; $pevaluacions_count = (!empty($profesor->pevaluacions->count())) ? $profesor->pevaluacions->count():null; @endphp

            <tr data-id="{{$profesor->id}}">

                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td class="{{ $class_profesor  ?? ''}}">
                    {{$profesor->fullname}} <br> <span class="text-muted font-weight-bold">{{ $profesor->ci_profesor ?? ''}}</span>
                </td>

                <td class="{{ $class_ci ?? '' }} text-center">
                    @php $n= 0; @endphp
                    @foreach ($lapsos as $lapso)  
                        @php $count = ( $pevaluacions->isNotEmpty()) ?$pevaluacions->where('lapso_id',$lapso->id)->count() : null  @endphp                  
                        <span class="badge badge-{{ $lapso->class ?? secondary }}" style="font-size: 0.6rem">
                            {{ str_pad($count,2,'0',STR_PAD_LEFT) ?? '00' }}
                        </span>
                    @endforeach
                </td>

                <td class="{{ $class_ci ?? '' }} text-center">
                    @php $n= 0; @endphp
                    @foreach ($lapsos as $lapso)  
                        @php $activities = $profesor->getActivitiesForLapso($lapso->id); $count = $activities->count() @endphp                  
                        <span class="badge badge-{{ $lapso->class ?? secondary }}" style="font-size: 0.6rem">
                            {{ str_pad($count,2,'0',STR_PAD_LEFT) ?? '00' }}
                        </span>
                    @endforeach
                </td>
                @if ($user->group == 'imployeds')

                <td class="{{ $class_username ?? '' }}">
                    {{ ($profesor->user) ? $profesor->user->username : null }}
                </td>
                
                <td class="{{ $class_gsuite ?? '' }}">
                    {{ mb_strtolower($profesor->email) ?? ''}}<br>{{$profesor->phone ?? ''}}
                </td>  

                <td class="{{ $class_gsuite ?? '' }}">
                    {{ $profesor->gsemail ?? ''}}
                </td>
                @endif

            </tr>

        @endforeach

        </tbody>

    </table>

    {{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.particulars.representans.exportBootstrap') --}}
