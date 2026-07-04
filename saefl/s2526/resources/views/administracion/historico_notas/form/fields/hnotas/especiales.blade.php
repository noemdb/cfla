<span class="font-weight-bold">{{$grado->name ?? ''}}</span>
<div class="px-2">

    <table class="table table-sm">
        <thead>
            <tr>
                <th class="align-top">Áreas de Formación</th>
                <th class="align-top">Literal</th>
                <th class="align-top">
                    Grupo
                    <a class="btn btn-primary btn-sm float-right" title="Crear un nuevo Grupo Estable" href="{{route('administracion.configuraciones.grupo_estables.create')}}" role="button" target="_blank">
                        <i class="{{ $icon_menus['nuevo'] ?? ''}}"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grado->getAsignaturas('false') as $pensum)
            @php $asignatura = $pensum->asignatura; @endphp
            @php $hnota = (empty($historico_nota->id)) ? null : $historico_nota->getHNota($pensum->id); @endphp
                <tr>
                    <td>
                        {{$asignatura->name}}
                        @php $name = 'asignatura_id['.$pensum->id.']'; @endphp
                        {{ Form::hidden($name, $asignatura->id) }}
                    </td>
                    <td>
                        @php $name = 'literal['.$pensum->id.']'; @endphp
                        @php $value = ($hnota) ? $hnota->literal: null; @endphp
                        {!! Form::select($name,$list_baremo,$value,['class' =>'form-control','placeholder' =>'']) !!}
                        {{-- {!! Form::select($name,$list_baremo,random_int ( 7, 11 ),['class' =>'form-control','placeholder' =>'']) !!} --}}
                    </td>
                    <td>
                        @if ($asignatura->enable_grupo_estable == 'true')
                            @php $name = 'grupo_estable_id['.$pensum->id.']'; @endphp
                            @php $value = ($hnota) ? $hnota->grupo_estable_id: null; @endphp
                            {!! Form::select($name,$list_grupo_estables,$value,['class' =>'form-control','placeholder' =>'']) !!}
                            {{-- {!! Form::select($name,$list_grupo_estables,random_int (1,3),['class' =>'form-control','placeholder' =>'']) !!} --}}

                        @endif
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

</div>
