<nav>
    <div class="nav nav-tabs" id="nav-tab-pensums" role="tablist">
        @foreach($pensums as $pensum)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-pensum-{{$pensum->id}}" data-toggle="tab" href="#nav-content-pensum-{{$pensum->id}}" role="tab">
                {{$pensum->asignatura->code ?? ''}}
            </a>
        @endforeach
    </div>
</nav>

<div class="tab-content" id="nav-tabContent-pensums">

@foreach($pensums as $pensum)

    <div class="tab-pane fade {{($loop->iteration==1) ? ' show active ':''}}" id="nav-content-pensum-{{$pensum->id}}" role="tabpanel" aria-labelledby="nav-pensum-tab">

        <div class="p-2">

            <div class="row">

                <div class="col-12">

                    @php $grado = $pensum->grado @endphp
                    @include('profesors.boletins.partials.nav_tabs.seccions')
                    {{-- {{ $seccions ?? ''}} --}}

                </div>

            </div>

        </div>

    </div>

@endforeach

</div>
