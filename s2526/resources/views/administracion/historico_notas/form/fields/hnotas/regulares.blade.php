{{-- <span class="font-weight-bold">{{$grado->name ?? ''}}</span> --}}

<div class="px-2 py-2">
    <table class="table table-sm px-4">
        <thead>
            <tr>
                <th class="align-top">Áreas de Formación</th>
                <th class="align-top">Nota</th>
                <th class="align-top">T.Evaluación</th>
                <th class="align-top">Fecha</th>
                <th class="align-top">
                    Institución
                    <a class="btn btn-primary btn-sm float-right" title="Crear una nueva institución"
                        href="{{ route('administracion.configuraciones.oinstitucions.create') }}" role="button"
                        target="_blank">
                        <i class="{{ $icon_menus['nuevo'] ?? '' }}"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $fecha = empty($finicial) ? $estudiant->hnotas->first()->fecha : $finicial;
                $fecha = add_year($fecha, $loop->index);
            @endphp
            @foreach ($grado->getAsignaturas('true') as $pensum)
                @php $asignatura = $pensum->asignatura; @endphp
                @php $hnota = (empty($historico_nota->id)) ? null : $historico_nota->getHNota($pensum->id); @endphp
                <tr>
                    <td class="text-nowrap">
                        {{ $asignatura->name }}
                        @php $name = 'asignatura_id['.$pensum->id.']'; @endphp
                        {{ Form::hidden($name, $asignatura->id) }}
                    </td>
                    <td>
                        @php $name = 'valor['.$pensum->id.']'; @endphp
                        @php $value = ($hnota) ? $hnota->valor: null; @endphp
                        {!! Form::select($name, $list_nota, $value, ['class' => 'form-control w-auto', 'placeholder' => '']) !!}
                    </td>

                    <td>
                        @php $name = 'tipo['.$pensum->id.']'; @endphp
                        @php $value = ($hnota) ? $hnota->tevaluacion_id: null; @endphp
                        {!! Form::select($name, $list_tevaluacion, $value, ['class' => 'form-control']) !!}
                    </td>
                    <td>
                        @php $name = 'fecha['.$pensum->id.']'; @endphp
                        @php $value = (!empty($hnota->fecha)) ? $hnota->fecha:$fecha; @endphp
                        {!! Form::text($name, $value, [
                            'class' => 'form-control datepicker',
                            'placeholder' => 'Fecha',
                            'required',
                            'readonly',
                            'maxlength' => '10',
                        ]) !!}
                    </td>
                    <td>
                        @php $name = 'institucion['.$pensum->id.']'; @endphp
                        @php $value = (!empty($hnota->institucion_id)) ? $hnota->institucion_id:null; @endphp
                        {!! Form::select($name, $list_institucion, $value, ['class' => 'form-control']) !!}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
