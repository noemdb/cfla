<tr data-id="{{$estudiant->id ?? ''}}">

    <td id="td-count" class="{{ $class_N }}">
        {{$loop->iteration}}
    </td>

    <td id="td-movement" class="{{ $class_movement }}">
       <span>{{$movement}}</span>
    </td>

    <td id="td-institucion_name" class="{{ $class_institution }}">
        {{$institucion_name}}
    </td>

    <td id="td-gradoSeccion" class="{{ $class_estudiant }}">
        {{$gradoSeccion}}
    </td>

    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
        {{$estudiant->lastname ?? ''}}
    </td>
    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
        {{$estudiant->name ?? ''}}
    </td>

    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant ?? '' }}">
        {{ ($date_birth) ? f_date($date_birth) : ''}}
    </td>

    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant ?? '' }}">
        
        @if ( ! ( ($estudiant->ci_estudiant >= 30000000 && $estudiant->ci_estudiant <= 30000100) || ($estudiant->ci_estudiant >= 40000000 && $estudiant->ci_estudiant <= 40001000)) )            
            {{ ($estudiant->type_ci_id == 1) ? 'V-' : 'C.E-' }}{{ $estudiant->ci_estudiant }}
        @else
            -NO TIENE-
        @endif

    </td>

    <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
        {{ $estudiant->gsemail ?? '' }}
    </td>

    <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
        {{ $representant->email ?? '' }}
    </td>

    <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
        {{ $representant->ci_representant ?? '' }} || {{ $representant->name ?? '' }}
    </td>

</tr>