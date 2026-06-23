@isset($user->alerts)

    <div id="accordion">

        @foreach($user->alerts as $alert)

            <div class="card border-{{$alert->class ?? 'secondary'}} border-top-0 border-right-0 border-bottom-0">
                <div class="card-header" id="heading_task_{{ $alert->id ?? '' }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link btn-sm" data-toggle="collapse" data-target="#accordion_task_{{ $alert->id ?? '' }}" aria-expanded="true" aria-conttasks="accordion_task_{{ $alert->id ?? '' }}">
                            <span class="text text-{{ $alert->class ?? '' }}">{{ $alert->truncmensaje }}</span>
                        </button>
                    </h5>
                </div>

                <div id="accordion_task_{{ $alert->id ?? '' }}" class="collapse" aria-labelledby="heading_task_{{ $alert->id ?? '' }}" data-parent="#accordion">
                    <div class="card-body">
                        {{-- Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. --}}

                        @include('admin.alerts.show.alert')

                        <a class="btn btn-warning w-100" href="{{ route('alerts.edit',$alert->id)}}" taske="button">
                            Actualizar
                            <i class="{{$icon_menus['alert'] ?? ''}}"></i>
                        </a>

                    </div>
                </div>
            </div>

        @endforeach

    </div>

@endisset