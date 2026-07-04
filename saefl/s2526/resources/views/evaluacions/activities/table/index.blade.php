@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover small p-1" id="table-data-default">
        <caption style="caption-side: top-right">Listado de Áreas de Formación...</caption>
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}">Actividades</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php $pensum = $pevaluacion->pensum; $grado = $pevaluacion->pensum->grado; $pensum = $pevaluacion->pensum; @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
            class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }} {{$grado->class_text_color}}">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>

                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    @php $activities = $pevaluacion->activities; @endphp
                    <ul class="list-group">
                        @forelse ($activities as $item)
                            <li class="list-group-item list-group-flush px-1">
                                <div class="d-flex justify-content-between">
                                    <div class="align-self-center px-2 font-weight-bold">{{$loop->iteration}}</div>
                                    <div class="flex-grow-1">
                                        <div class="">
                                            <div class=""><strong>T.Generador/Énfasis:</strong> {{$item->topic}}</div>
                                            <div class=""><strong>T.Temático/T.Indispensable:</strong> {{$item->thematic}}</div>
                                            <div class=""><strong>Referentes/Ético</strong> {{$item->references}}</div>
                                            <div class=""><strong>Comentario:</strong> {{$item->comments}}</div>
                                        </div>
                                    </div>
                                    <div class="align-self-center px-2">
                                        <div class="btn-group">
                                            <button wire:click="createComent({{$item->id}})" type="button" class="btn btn-info btn-sm">
                                                <i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i>
                                            </button>
                                            <button wire:click="editComent({{$item->id}})" type="button" class="btn btn-warning btn-sm">
                                                <i class="{{ $icon_menus['edit'] ?? ''}} fa-1x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item disabled">No hay actividades</li>
                        @endforelse
                    </ul>
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group">

                        <a title="Plan de Actividades" class="btn btn-dark btn-sm" href="{{route('evaluacions.activities.format',$pevaluacion->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>

                        <a wire:click="createObservation({{$pevaluacion->id}})" title="Registrar Comentarios" class="btn btn-primary btn-sm" href="#" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('evaluacions.datatables.default')
