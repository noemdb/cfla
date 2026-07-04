@isset($user->rols)

    <div id="accordion">

        @foreach($user->rols as $rol)

            <div class="card">
                <div class="card-header" id="heading_rol_{{ $rol->id ?? '' }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link btn-sm" data-toggle="collapse" data-target="#accordion_rol_{{ $rol->id ?? '' }}" aria-expanded="true" aria-controls="accordion_rol_{{ $rol->id ?? '' }}">
                            {{ $rol->rol }}
                        </button>
                    </h5>
                </div>

                <div id="accordion_rol_{{ $rol->id ?? '' }}" class="collapse" aria-labelledby="heading_rol_{{ $rol->id ?? '' }}" data-parent="#accordion">
                    <div class="card-body">
                        {{-- Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. --}}

                        @include('admin.rols.forms.update')

                    </div>
                </div>
            </div>

        @endforeach

    </div>

@endisset