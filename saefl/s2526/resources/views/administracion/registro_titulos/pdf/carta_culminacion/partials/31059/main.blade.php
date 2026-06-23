<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
Quien suscribe,
<strong>{{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</strong>,
titular de la Cédula de Identidad Nº <strong>{{$autoridad1->ci ?? ''}}</strong>, <strong>{{$autoridad1->position ?? ''}}</strong>
de la {{$institucion->name ?? ''}},
hace constar que
{{($estudiant->gender=="Femenino") ? 'la':''}}{{($estudiant->gender=="Masculino") ? 'el':''}}
estudiante <strong>{{$estudiant->fullname}}</strong>,
Cédula {{(strlen($estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}}
N° <strong>{{$estudiant->ci_estudiant ?? ''}}</strong>, ha
cursado y aprobado todas las asignaturas de
<strong>{{$pestudio->name ?? ''}}</strong>,
encontrándose en la fase de tramitación legal para la emisión del titulo: <strong>{{$pestudio->title ?? ''}}</strong>.
</p>

<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
    Se expide la presente a petición de la parte interesada en SAN FELIPE a los
    {{$fecha_remision->format('d') ?? ''}} días del mes de {{$fecha_remision->format('F') ?? ''}} del año {{$fecha_remision->format('Y')}}.
</p>
