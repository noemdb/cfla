<nav>
    <div class="nav nav-tabs" id="nav-tab-seccions" role="tablist">
        @foreach($seccions as $seccion)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}} text-uppercase" id="nav-header-tab-seccion-{{$seccion->id}}" data-toggle="tab" href="#nav-content-seccion-{{$seccion->id}}" role="tab">
                {{$seccion->grado->name?? ''}} {{$seccion->name?? ''}}
            </a>
        @endforeach
    </div>
</nav>

<div class="tab-content" id="nav-tabContent-seccions">

@foreach($seccions as $seccion)

    <div class="tab-pane fade {{($loop->iteration==1) ? ' show active ':''}}" id="nav-content-seccion-{{$seccion->id}}" role="tabpanel" aria-labelledby="nav-seccion-tab">

        <div class="p-2">

            <div class="row">

                <div class="col-12">

                @include('profesors.boletins.table.sabanafull')

                </div>

            </div>

        </div>

    </div>

@endforeach

</div>
