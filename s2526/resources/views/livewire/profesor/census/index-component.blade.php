<div>
    @if (Session::has('operp_ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {!! Session::get('operp_ok') !!}
        </div>
    @endif

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-7 pt-1">
                @if ($status_active_lapso_censu)
                    <div class="border rounded">
                        <h4 class="alert alert-secondary mb-0">Registro de participantes <span class="small float-right">[{{$lapso->name ?? null}}]</span></h4>
                        <div class="mt-0 pl-2 small text-right">
                            <span>Programado desde el {{ ($date_start_census) ? $date_start_census->format('d-m-Y') : '---' }} hasta el {{ ($date_end_census) ? $date_end_census->format('d-m-Y') : '---' }}</span>
                            <span>de {{$time_start_census ?? null}} a {{$time_end_census ?? null}}</span>
                            <i>Sólo días hábiles (Lun. a Vie.)</i>
                        </div>

                        <div class="px-1 mx-1 pt-2">

                            @include('livewire.profesor.census.form.fields')

                            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                                {!! Form::button('Guardar', ['class' => 'form-control btn pt-1 mt-1 btn-primary', 'wire:click' => 'save()']) !!}
                            </div>

                        </div>

                    </div>

                @else

                    <div class="border rounded">
                        <h4 class="alert alert-secondary ">El registro de participantes no esta habilitado <span class="small float-right">[{{$lapso->name ?? null}}]</span></h4>

                        <div class="px-1 mx-1">

                            <span class=" font-weight-bold text-muted">
                                El período establecido para el resgistro de participantes es el siguinte:
                            </span>

                            <div class="pl-2">
                                <span>Desde el {{ ($date_start_census) ? $date_start_census->format('d-m-Y') : '---' }} hasta el {{ ($date_end_census) ? $date_end_census->format('d-m-Y') : '---' }}</span>
                                <span>de {{ $time_start_census ?? null}} a {{$time_end_census ?? null}}</span>
                                <div><i>Sólo días hábiles (Lun. a Vie.)</i></div>
                            </div>

                        </div>

                    </div>
                @endif
            </div>


            <div class="col-lg-5 pt-1">
                <div class="border rounded">
                    <h4 class="alert alert-secondary mb-0">
                        Listado de participantes registrados
                    </h4>
                    <div class="small text-muted text-right">{{ Auth::user()->profesor->fullname }}</div>

                    <div>
                        <ul class="list-group list-group-flush">
                            @foreach ($censuses as $census)
                                <li class="list-group-item">
                                    <div>
                                        <span>{{ $loop->iteration }}.-</span>
                                        <span>{{ $census->ci_estudiant }}</span>
                                        <span>{{ $census->lastname }}</span>
                                        <span>{{ $census->name }}</span>
                                    </div>

                                    <div class="text-muted text-right">
                                        {{ $census->grado->name }} ||

                                        <span>{{ $census->ci_representant }}</span> <span>{{ $census->name_representant }}</span>
                                        <div>{{ $census->institution }}</div>

                                    </div>

                                </li>
                            @endforeach
                        </ul>

                    </div>
                    {{ $censuses->links() }}
                    {{-- @include('livewire.academico.mailer.table.mailer',['mailers'=>$mailers]) --}}
                </div>
            </div>


        </div>

    </div>
</div>
