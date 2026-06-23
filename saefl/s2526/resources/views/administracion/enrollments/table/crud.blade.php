@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['ci_estudiant']="d-none d-sm-table-cell";
    $class['fullname']="d-none d-sm-table-cell";
    $class['photo_url']="d-none d-sm-table-cell text-center";
    $class['pestudio_id']="d-none d-sm-table-cell";
    $class['grado_id']="d-none d-sm-table-cell";
    $class['grupo_estable_id']="d-none d-sm-table-cell";
    $class['ci_representant']="d-none d-sm-table-cell";
    $class['name_representant']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{--

    'ci_estudiant','lastname','name','cellphone','gender','date_birth',
    'age','town_hall_birth','state_birth','country_birth','dir_address','pestudio_id',
    'grado_id','pending_matter','blood_type','weight','height','laterality',
    'order_born','status_brother','literal','ci_representant','name_representant','lastname_representant',
    'relationship','profession_representant','phone_representant','cellphone_representant','email_representant','twitter','instagram'

--}}


<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['ci_estudiant'] ?? ''}}">{{$list_comment['ci_estudiant'] ?? ''}}</th>
            <th class="{{ $class['fullname'] ?? ''}}">{{$list_comment['fullname'] ?? ''}}</th>
            <th class="{{ $class['photo_url'] ?? ''}}">{{$list_comment['photo_url'] ?? ''}}</th>
            <th class="{{ $class['pestudio_id'] ?? ''}}">{{$list_comment['pestudio_id'] ?? ''}}</th>
            <th class="{{ $class['grado_id'] ?? ''}}">{{$list_comment['grado_id'] ?? ''}}</th>
            <th class="{{ $class['grupo_estable_id'] ?? ''}}">{{$list_comment['grupo_estable_id'] ?? ''}}</th>
            <th class="{{ $class['ci_representant'] ?? ''}}">{{$list_comment['ci_representant'] ?? ''}}</th>
            <th class="{{ $class['name_representant'] ?? ''}}">{{$list_comment['name_representant'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($enrollments as $enrollment)

        @php
            $grado = $enrollment->grado;
            $pestudio = ($grado) ? $grado->pestudio : null;
            $grupo_estable = $enrollment->grupo_estable;
            $photo_url = $enrollment->photo_url;
        @endphp

        <tr data-id="{{$enrollment->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['ci_estudiant'] ?? ''}}">{{ $enrollment->ci_estudiant ?? ''}}</td>
            <td class="{{ $class['fullname'] ?? ''}}">{{ $enrollment->fullname ?? ''}}</td>
            <td class="{{ $class['photo_url'] ?? ''}}">
                <div class="d-flex justify-content-center">
                    @if ($photo_url)
                        @php
                            $fileUrl = $photo_url;
                            $ext = strtoupper(pathinfo($fileUrl, PATHINFO_EXTENSION));
                            $modal_id = 'modal_image_'.$enrollment->id ;
                        @endphp
                        @includeWhen($fileUrl, 'elements.modals.show'.$ext,['fileUrl'=>$fileUrl,'modal_id'=>$modal_id,'preview'=>false])
                    @else
                        <span class="text-muted">Sin foto</span>
                    @endif
                </div>
            </td>
            <td class="{{ $class['pestudio_id'] ?? ''}}">{{ ($pestudio) ? $pestudio->name : null }}</td>
            <td class="{{ $class['grado_id'] ?? ''}}">{{ ($grado) ? $grado->name : null }}</td>
            <td class="{{ $class['grupo_estable_id'] ?? ''}}">{{ ($grupo_estable) ? $grupo_estable->name : null }}</td>
            <td class="{{ $class['ci_representant'] ?? ''}}">{{$enrollment->ci_representant ?? ''}}</td>
            <td class="{{ $class['name_representant'] ?? ''}}">{{$enrollment->name_representant ?? ''}}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        {{-- <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.enrollments.edit',$enrollment->id)}}" role="button"> --}}
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.enrollments.edit',$enrollment)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="PDF" class="btn btn-dark btn-sm"  href="{{route('administracion.enrollments.pdf.payroll',$enrollment->id)}}" role="button" target="_blank">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

