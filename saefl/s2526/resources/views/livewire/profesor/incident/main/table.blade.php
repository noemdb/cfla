@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<div class="px-2">

    <hr>

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default" wire:key="table-data-estudiants">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">N. incidencias registradas</th>
                <th class="{{ $class_action }} text-left {{ $displaModeIndex }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)
                @php
                    $status_active = ($estudiant->status_active=='true') ? true : false;
                    $incidents = $estudiant->incidents;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                @endphp
                <tr data-estudiant_id="{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}" class="{{($estudiant->id == $estudiant_id ) ? 'table-secondary':null}}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{$estudiant->fullname ?? null}}</div>
                        <div class=" text-sm text-muted">{{$estudiant->ci_estudiant}} <span class="text-muted">[Edad: {{$estudiant->age ?? null}}]</span></div>
                        <div class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                        </div>
                        <hr class="my-0 py-0">
                        <div class="ml-2">
                            <div class="text-secondary">
                                <div class="">Representante: {{$representant->name ?? null}} <span class="text-muted">{{$representant->ci_representant ?? null}}</span></div>
                            </div>
                            @if ($brothers->count())
                                <div class=" text-secondary">Hermano(s):</div>
                                @foreach($brothers as $brother)
                                    <div class="text-muted">-. {{$brother->fullname ?? null}} <span class="text-muted">{{$brother->ci_estudiant ?? null}}</span> <span class="text-muted">[Edad: {{$brother->age ?? null}}]</span></div>
                                @endforeach
                            @endif
                        </div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }} {{ $displaModeIndex }}">
                        {{ $incidents->count() ?? null}}
                    </td>

                    <td class="{{ $class_action ?? '' }} {{ $displaModeIndex }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="d-flex justify-content-start" wire:key="action-flex-btn-{{$estudiant->id}}">

                            <a title="Registrar incidente" class="btn btn-primary btn-sm mr-1" href="#" wire:click="create({{$estudiant->id}})" role="button">
                                <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                            </a>

                            @if($incidents->isNotEmpty())

                                {{-- <a title="Línea de tiempo" class="btn btn-info btn-sm mr-1" href="#" wire:click="tline({{$estudiant->id}})" role="button">
                                    <i class="{{ $icon_menus['tline'] ?? '' }} fa-1x"></i>
                                </a> --}}

                                <div class="dropdown dropleft">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{$estudiant->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="{{ $icon_menus['incident_setting'] ?? '' }} fa-1x"></i>
                                    </button>

                                    <div class="dropdown-menu px-1 mx-1" aria-labelledby="dropdownMenuButton{{$estudiant->id}}">
                                        @foreach ($incidents as $incident)
                                            @php
                                                $estudiant = $incident->estudiant;
                                                $status_profesor_guia = $estudiant->isProfesorGuia($profesor->id);
                                                $description = ($incident->description) ? $incident->description : 'Prof: '.$incident->description_profesor;
                                            @endphp
                                            <div class="dropdown-item px-1 mx-1" href="#">
                                                <div class="d-flex justify-content-between">
                                                    <div class="small text-dark font-weight-bold">
                                                        <span title="{{$description ?? null}}">{{$loop->iteration}}. {{Str::limit($description,15,'...')}}</span>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" >

                                                        {{-- <a title="Agregar Correctivo" class="btn btn-secondary btn-sm" href="#" wire:click="createdActions({{$incident->id}})" role="button">
                                                            <i class="fas fa-archive"></i>
                                                        </a> --}}

                                                        <a title="Ver vista previa de la notificación" class="btn btn-info btn-sm" href="#" wire:click="viewMail({{$incident->id}})" role="button">
                                                            <i class="{{ $icon_menus['eye'] ?? '' }} fa-1x"></i>
                                                        </a>
                                                        <a title="Generar ficha de la Incidencia" target="_BLANK" class="btn btn-dark btn-sm" href="{{route('profesors.incidents.ficha.pdf',$incident->id)}}"  role="button">
                                                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                                                        </a>
                                                        <a title="Mostrar datos de la incidencia" class="btn btn-light btn-sm " href="#" wire:click="show({{$incident->id}})" role="button">
                                                            <i class="{{ $icon_menus['show'] ?? '' }} fa-1x"></i>
                                                        </a>
                                                        @if ($status_profesor_guia)

                                                            @if ($incident->status_notify)
                                                                <a title="Notificación enviada" class="btn btn-success btn-sm disabled" href="#" role="button">
                                                                    <i class="{{ $icon_menus['check'] ?? '' }} fa-1x"></i>
                                                                </a>
                                                            @else
                                                                <a title="Enviar por correo una notificación de la incidencia" class="btn btn-success btn-sm {{ ($incident->status_notify) ? 'disabled':null}}" href="#" wire:click="sendMail({{$incident->id}})" role="button">
                                                                    <i class="{{ $icon_menus['mail'] ?? '' }} fa-1x"></i>
                                                                </a>
                                                            @endif

                                                            @php $disabled = ($incident->status_notify || $incident->status_close) ? true:false @endphp
                                                            <a title="Editar datos de la incidencia" class="btn btn-warning btn-sm {{ ($disabled) ? 'disabled' : null }} " href="#" wire:click="edit({{$incident->id}})" role="button">
                                                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                                                            </a>
                                                            

                                                            @php $disabled = ($incident->status_notify) ? true:false @endphp
                                                            <a title="Eliminar datos de la incidencia" class="btn btn-danger btn-sm {{ ($disabled) ? 'disabled' : null}}" href="#" wire:click="destroy({{$incident->id}})" role="button">
                                                                <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                                                            </a>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a title="Generar ficha Estuidiante" target="_BLANK" class="btn btn-danger btn-sm" href="{{route('profesors.incidents.pdf.ficha.estudiant',$estudiant->id)}}"  role="button">
                                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                                    </a>
                                </div>

                            @endif

                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    @if($estudiants->isNotEmpty()) {{ $estudiants->links() }} @endif


</div>

