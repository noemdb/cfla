<div style="white-space:normal !important; margin-top:0.3rem; text-align: justify; text-justify: inter-word;">

    <p style=" font-size:0.6rem !important; background-color:#eee;">
        {{ $edescriptiva->description ?? ''}}
    </p>

    <p style=" font-size:0.6rem !important; background-color:#ddd;">
        {{ $edescriptiva->observations ?? ''}}
    </p>

    <p style=" font-size:0.76rem !important; ">
        En base a lo antes expuesto el alumno alcanzó la expresión literal "{{ $estudiant->literal ?? '' }}", @admin {{ $estudiant->id ?? '' }} @endadmin
        siendo promovid{{ ($estudiant->gender == 'Femenino') ? 'a':'o' }} al <b>{{ $estudiant->grado_promocion->name ?? '' }} de {{ $estudiant->grado_promocion->pestudio->name ?? '' }}</b>.
    </p>

</div>

