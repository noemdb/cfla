<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
    La {{ $institucion->name ?? ''}},
    ubicada en {{ $institucion->city ?? ''}}, {{ $institucion->state ?? ''}},
    por medio de la presente, hace constar que {{($estudiant->gender=="Femenino") ? 'la ciudadana':''}}{{($estudiant->gender=="Masculino") ? 'el ciudadano':''}} :
    <strong>{{$estudiant->fullname}}</strong>,
    Cédula {{(strlen($estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}} N° <strong>V-{{$estudiant->ci_estudiant ?? ''}}</strong>,
    de {{$estudiant->age ?? ''}} años de edad,
    {{($estudiant->gender=="Femenino") ? 'nacida':''}}{{($estudiant->gender=="Masculino") ? 'nacido':''}} en {{$estudiant->city_birth ?? ''}}, {{$estudiant->state_birth ?? ''}},
    ha demostrado BUENA CONDUCTA en su desempeño como estudiante de nuestra institución.

</p>

<p style=" font-size:0.8rem; white-space: wrap; text-align:justify">
    Se expide la presente a petición de la parte interesada en SAN FELIPE a los {{Carbon\Carbon::now()->day}}  días del mes
    de {{Carbon\Carbon::now()->monthName}} de {{Carbon\Carbon::now()->year}}.
</p>
