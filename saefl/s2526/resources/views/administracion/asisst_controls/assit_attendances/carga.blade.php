<h4 class=" alert alert-success card-title my-0">
    <a name="" id="" class="btn btn-light float-right" href="{{route('controls.estudiants.crud')}}" role="button"> <i class="{{$icon_menus['crud'] ?? ''}} text-dark" aria-hidden="true"></i> </a>
    <i class="{{ $icon_menus['sync'] ?? '' }} text-success" aria-hidden="true"></i>
    Sincronizar datos de <span class="font-weight-bold">Estudiantes</span>
    @if ($estudiantsXLS->isNotEmpty())
        <div class="small font-weight-bold text-right mr-2 float-lg-right p-2 border rounded bg-light">
            N°: {{ $estudiantsXLS->count() ?? ''}}
        </div>
    @endif
</h4>

<div class="card-body p-0 m-0">

    @include('controls.content.estudiants.form.upload')

    @include('elements.messeges.oper_ok')

    @include('controls.content.estudiants.table.crudXLS')

</div>
