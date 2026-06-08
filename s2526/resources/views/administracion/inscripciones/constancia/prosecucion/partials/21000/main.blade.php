@php
    $pestudio_next = $estudiant->getPestudioNext($pestudio->id);
    $grado_next = $estudiant->getGradoNext($grado->id);
    $pestudio_next_name = ($pestudio_next) ? $pestudio_next->name : null ;
    $grado_next_name = ($grado_next) ? $grado_next->name : null ;
@endphp

<div class="section" style=" font-size:0.8rem; white-space: wrap; text-align:justify">

    <p>
        Quien suscribe <strong class="tstrong">{{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</strong> titular de la Cédula de Identidad Nº V.-{{$autoridad1->ci ?? ''}} 
        Director(a) de la Institución Educativa {{ $institucion->name ?? ''}}, ubicada en el Municipio {{ $institucion->town_hall ?? ''}} de la Parroquia San Felipe
        adscrita a LA ZONA EDUCATIVA DEL ESTADO YARACUY. 
        Por la presente hace constar que 
        {{($estudiant->gender=="Femenino") ? 'la':''}}{{($estudiant->gender=="Masculino") ? 'el':''}} estudiante {{$estudiant->fullname}}, 
        titular de la Cédula {{(strlen($estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}} N° <strong class="tstrong">{{$estudiant->ci_estudiant ?? ''}}</strong>, 
        {{($estudiant->gender=="Femenino") ? 'nacida':''}}{{($estudiant->gender=="Masculino") ? 'nacido':''}}
        en el Municipio {{ $estudiant->town_hall_birth ?? '' }} del Estado {{ $estudiant->state_birth ?? '' }}, en fecha {{ f_date($estudiant->date_birth) }} , cursó el {{$estudiant->grado->name ?? ''}}, 
        correspondiéndole el literal <strong>{{ $estudiant->literal ?? '' }}</strong> durante el periodo escolar {{ Session::get('pescolar_name') }}, 
        siendo {{($estudiant->gender=="Femenino") ? 'promovida':''}}{{($estudiant->gender=="Masculino") ? 'promovido':''}}
        al {{ $grado_next_name ?? '' }} del Nivel de Educación Primaria, previo cumplimiento de los requisitos establecidos en la normativa legal vigente.
    </p>
    <p>Constancia que se expide en SAN FELIPE, a los {{ ($fecha_remision) ? $fecha_remision->format('d') : null}} días del mes de {{ ($fecha_remision) ? $fecha_remision->format('F') : null}} de {{ ($fecha_remision) ? $fecha_remision->format('Y') : null}}</p>

</div>


