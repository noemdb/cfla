<div class="container text-decoration-none">
    @foreach ($pestudios as $pestudio)

        @php $area_conocimientos = $pestudio->area_conocimientos @endphp

        @if ( !empty($area_conocimientos->count()) )

            <div class="card-header alert-{{$pestudio->color ?? '' }} font-weight-bolder" role="alert">
                PLAN DE ESTUDIO
                <span class="font-weight-normal">
                    {{ $pestudio->fullname ?? '' }}
                </span>
            </div>

            <div class=" border rounded p-3 mb-2">

                <div class="row">

                    @foreach ($area_conocimientos as $area_conocimiento)

                        <div class="col px-1">

                            <ul class="list-group">

                                <li class="list-group-item list-group-item-secondary px-2 small font-weight-bold"><span>{{ $area_conocimiento->name ?? '' }}</span></li>

                                @foreach ($area_conocimiento->campo_conocimientos as $campo_conocimiento)

                                    @php $asignatura = $campo_conocimiento->asignatura @endphp
                                    @php $grado = $asignatura->pensum->grado @endphp

                                    <li class="list-group-item small">
                                    <span class=" text-dark" title="{{ $asignatura->fullname ?? ''}}">
                                            {{ Str::limit($asignatura->name, 30) }}
                                            <span title="{{$grado->name ?? ''}}" class=" float-right font-weight-bold {{ $grado->class_text_color ?? '' }}">
                                                [ {{ $grado->code_sm ?? ''}} ]
                                            </span>
                                        </span>
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endforeach

                </div>

            </div>
        @endif


    @endforeach

</div>

