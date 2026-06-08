@php
    $pestudio_next = $estudiant->getPestudioNext($pestudio->id);
    $grado_next = $estudiant->getGradoNext($grado->id);
    $pestudio_next_name = ($pestudio_next) ? $pestudio_next->name : null ;
    $grado_next_name = ($grado_next) ? $grado_next->name : null ;
@endphp

<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
Quien suscribe <span>{{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</span>
titular de la cédula de identidad N° {{$autoridad1->ci ?? ''}} en su condición de {{ $autoridad1->position ?? ''}}
del {{ $institucion->name ?? ''}}
ubicada en el Municipio SAN FELIPE, parroquia SAN FELIPE, adscrito a la Zona Educativa del Estado Yaracuy,
certifica por medio de la presente que
{{($estudiant->gender=="Femenino") ? 'la':''}}{{($estudiant->gender=="Masculino") ? 'el':''}}
estudiante <strong>{{$estudiant->fullname}}</strong>,
titular de la cédula {{(strlen($estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}} N° <strong>{{$estudiant->ci_estudiant ?? ''}}</strong>,
{{($estudiant->gender=="Femenino") ? 'nacida':''}}{{($estudiant->gender=="Masculino") ? 'nacido':''}}
en fecha {{ f_date($estudiant->date_birth) }}, cursó el {{$estudiant->grado->name ?? ''}},
correspondiéndole el literal "{{ $estudiant->literal ?? '' }}", durante el periodo escolar {{ Session::get('pescolar_name') }},
siendo {{($estudiant->gender=="Femenino") ? 'promovida':''}}{{($estudiant->gender=="Masculino") ? 'promovido':''}}
al {{ $grado_next_name ?? '' }} del Nivel de {{ $pestudio->name ?? '' }}, previo cumplimiento de los requisitos exigidos en la normativa legal vigente.
</p>

<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
Constancia que se expide en SAN FELIPE, a los {{Carbon\Carbon::now()->day}} días del mes de {{Carbon\Carbon::now()->monthName}} de {{Carbon\Carbon::now()->year}}
</p>

