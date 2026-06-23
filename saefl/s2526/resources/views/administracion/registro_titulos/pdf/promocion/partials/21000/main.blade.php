@php
    $pestudio_next = $estudiant->getPestudioNext($pestudio->id);
    $grado_next = $estudiant->getGradoNext($grado->id);
    $pestudio_next_name = ($pestudio_next) ? $pestudio_next->name : null ;
    $grado_next_name = ($grado_next) ? $grado_next->name : null ;
@endphp

<div class="section" style=" font-size:0.8rem; white-space: wrap; text-align:justify">
    Quien suscribe <span class="label">{{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</span> 
    titular de la Cédula de Identidad Nº V.-<span class="label">{{$autoridad1->ci ?? ''}}</span>,
    Director(a) de la Institución Educativa <span class="label">{{ $institucion->name ?? ''}}</span>,
    ubicada en el Municipio <span class="label">SAN FELIPE</span>
    de la Parroquia <span class="label">{{ $institucion->town_hall ?? ''}}</span>,
    adscrita a LA ZONA EDUCATIVA DEL ESTADO YARACUY <span class="label">Yaracuy</span>. 

    Por la presente certifica que 
    {{($estudiant->gender=="Femenino") ? 'la':''}}{{($estudiant->gender=="Masculino") ? 'el':''}} estudiante {{$estudiant->fullname}}, 
    titular de la cédula {{(strlen($estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}}
    N° <strong>{{$estudiant->ci_estudiant ?? ''}}</strong>
    {{($estudiant->gender=="Femenino") ? 'nacida':''}}{{($estudiant->gender=="Masculino") ? 'nacido':''}} 
    en el Municipio {{ $estudiant->town_hall_birth ?? '' }} 
    del Estado <span class="label">{{ $estudiant->state_birth ?? '' }}</span>, 
    en fecha <span class="label">{{ f_date($estudiant->date_birth) }}</span>, 
    cursó el 6to Grado correspondiéndole el literal <span class="label">"{{ $estudiant->literal ?? '' }}"</span>
    durante el periodo escolar <span class="label">{{ Session::get('pescolar_name') }}</span>,
    siendo promovido(a) al 1er Año del Nivel de Educación Media, previo cumplimiento de los
    requisitos establecidos en la normativa legal vigente.
</div>

