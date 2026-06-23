<h6 class=" font-weight-bold text-dark pb-2">
    Resumen
</h6>
<div class=" font-weight-bold text-secondary pb-2">
    Nombre: {{ $estudiant->fullname ?? '' }}
</div>
<div class=" font-weight-bold text-secondary pb-2">
    Asignatura: {{ $pevaluacion->pemsum->asignatura->name ?? '' }}
</div>



@foreach ($ecualitativas as $ecualitativa)

    <div class=" d-block">
        <label for="lapso_id" class="font-weight-bold text-secondary m-0">{{$list_comment['lapso_id'] ?? ''}}</label>
        <div class="form-group">
            {{ $ecualitativa->id ?? '' }}
        </div>

        <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
        <div class="form-group">
            {{ $ecualitativa->id ?? '' }}
        </div>

        <label for="description" class="font-weight-bold text-secondary m-0">{{$list_comment['description'] ?? ''}}</label>
        <div class="form-group">
            {{ $ecualitativa->id ?? '' }}
        </div>

        <label for="observations" class="font-weight-bold text-secondary m-0">{{$list_comment['observations'] ?? ''}}</label>
        <div class="form-group">
            {{ $ecualitativa->id ?? '' }}
        </div>

    </div>

    <hr>

@endforeach
