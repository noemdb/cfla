@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_gsmail="d-none d-lg-table-cell";
    $photo_url="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Nombres</th>
                <th class="{{ $class_estudiant }}">Apellidos</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class['photo_url'] ?? ''}}">Foto</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_gsmail }}">GSEmail</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)
                @php
                    $status_active = ($estudiant->status_active=='true') ? true : false;
                    $grado = $estudiant->grado;
                    $seccion = $estudiant->seccion;
                    // $photo_url = $estudiant->photo_url;
                    $photo_url = $estudiant->url_img;

                    // $photo_url = ($enrollment->photo_url) ? $enrollment->photo_url : $estudiant->logo ;
                @endphp
                <tr data-estudiant_id="{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}" class="{{($status_active) ? '':'table->danger'}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->name}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->lastname}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{$estudiant->ci_estudiant}}
                    </td>

                    <td class="{{ $class['photo_url'] ?? ''}}">
                        <div class="d-flex justify-content-center">
                            @if ($photo_url)
                                @php
                                    $fileUrl = $photo_url;
                                    $ext = strtoupper(pathinfo($fileUrl, PATHINFO_EXTENSION));
                                    $modal_id = 'modal_image_'.$estudiant->id ;
                                @endphp
                                @includeWhen($fileUrl, 'elements.modals.show'.$ext,['fileUrl'=>$fileUrl,'modal_id'=>$modal_id,'preview'=>false])
                            @else
                                <span class="text-muted">Sin foto</span>
                            @endif
                        </div>
                    </td>

                    <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        <span class="{{$grado->class_text_color ?? 'default'}}">
                            {{$grado->name ?? ''}} {{$seccion->name ?? ''}}
                        </span> 
                    </td>

                    <td class="{{ $class_gsemail ?? '' }}" style="white-space: wrap !important">
                        {{ $estudiant->gsemail ?? ''}}</span>
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            <a title="PDF - Planilla del Estudiante" class="btn btn-dark btn-sm"  href="{{route('profesors.estudiant.enrollments.pdf',$estudiant->id)}}" role="button" target="_blank">
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')


