<div class=" border rounded">

    <div class="form-group alert alert-primary font-weight-bold p-3">
        {{ $area_conocimiento->fullname ?? '' }}
    </div>

    <div class="container-fluid">

        @php $peducativo = (!empty($area_conocimiento->peducativo)) ? $area_conocimiento->peducativo : null; @endphp

        <div class="row">
            <div class="col">
                <div class="font-weight-bold">{{$peducativo->id ?? null}}.- {{$peducativo->name ?? null}}</div>                
            </div>
        </div>

        @php $pestudios = ( !empty($peducativo->pestudios) ) ? $peducativo->getPestudiosActive() : null; @endphp

        @if ($pestudios)

            {{-- <div>{{$pestudios ?? null}}</div> --}}

            @foreach ($pestudios as $pestudio)
                <div class="row">
                    <div class="col">
                        <div class="p-2">
                            {{$loop->iteration ?? null}}.- [{{$pestudio->code ?? null}}] {{$pestudio->name ?? null}}

                            @php $grados = $pestudio->getGradosActive(); @endphp
                            <div class="container-fluid">

                                <div class="row">

                                    @foreach ($grados as $grado)

                                        <div class="col-6">
                                            <div class="card pt-1 mt-1">
                                                <div class="card-header alert-light font-weight-bold text-uppercase">
                                                    {{ $grado->name ?? '' }}
                                                </div>
                                                <div class="card-body py-0">
                                                    <ul class="list-group list-group-flush">
                                                        @php $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;}); @endphp
                                                        @foreach ($pensums as $pensum)
                                                            @php $asignatura = $pensum->asignatura @endphp
                                                            <li class="list-group-item py-1 px-1 border-0">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            <input name="asignatura_id[]" value="{{$asignatura->id ?? ''}}" type="checkbox" {{ ( !empty($area_conocimiento) ) ? ($area_conocimiento->getCheckIn($asignatura->id)) ? 'checked':null : 'false' }} >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-control">
                                                                        <span class=" small" title="{{$asignatura->fullname ?? ''}}">
                                                                            {{ Str::limit($asignatura->fullname, 40) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    @endforeach

                                </div>

                            </div>

                            <hr class="mt-0 mb-1">

                        </div>
                    </div>
                </div>
            @endforeach
            
        @endif

        
        {{-- <div class="row">
            <div class="col">
                {{$pestudios ?? null}}
            </div>
        </div> --}}

        {{-- <div class="row">

            @php $pestudios = (!empty($peducativo->pestudios)) ? $peducativo->pestudios : null @endphp

            @foreach ($pestudios as $pestudio)

                <div class="col">

                    <div class="container">

                        <div class="row">

                            @foreach ($pestudio->grados as $grado)

                                <div class="col-6">

                                    <div class="card pt-1 mt-1">
                                        <div class="card-header alert-light font-weight-bold text-uppercase">
                                            {{ $grado->name ?? '' }}
                                        </div>
                                        <div class="card-body py-0">
                                            <ul class="list-group list-group-flush">
                                                @php $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;}); @endphp
                                                @foreach ($pensums as $pensum)
                                                    @php $asignatura = $pensum->asignatura @endphp
                                                    <li class="list-group-item py-1 px-1 border-0">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input name="asignatura_id[]" value="{{$asignatura->id ?? ''}}" type="checkbox" {{ ( !empty($area_conocimiento) ) ? ($area_conocimiento->getCheckIn($asignatura->id)) ? 'checked':null : 'false' }} >
                                                                </div>
                                                            </div>
                                                            <div class="form-control">
                                                                <span class=" small" title="{{$asignatura->fullname ?? ''}}">
                                                                    {{ Str::limit($asignatura->fullname, 40) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                @endforeach

                            </div>

                    </div>

                </div>                

            @endforeach

        </div> --}}

    </div>

</div>


