<div class="d-flex justify-content-center text-start">
    <div class="card h-100 bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} p-2 m-2" style="max-width: 20rem">

        <img class=" img-fluid" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

        <div class="card-body p-1">

            <small class="align-text-bottom text-mute d-block">
                {{$estudiant->name ?? ''}} {{$estudiant->lastname ?? ''}}<br>
                {{$estudiant->ci_estudiant ?? ''}}
            </small>

            @isset ($estudiant->getInscripcion()->id)
                <span class="text-mute text-center">
                    {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                </span>
            @endisset

            @if (!empty($estudiant->retiro->id))
                <span class=" d-block">Retiro (Administrativo/Académico) {{$estudiant->created_ap ?? ''}}</span>
            @endif

        </div>

    </div>
</div>


@section('stylesheets')
	@parent
	<link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
@endsection
