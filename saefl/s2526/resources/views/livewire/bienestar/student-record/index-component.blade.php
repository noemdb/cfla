<div>

        <div class="card-header  alert-dark mt-2">

            <h3 class="mb-0 pb-0">

                <div class="btn-group float-right pt-0 pb-2">
                    @include('livewire.bienestar.student-record.menu.index')
                </div>

                <div class="float-right ">
                    <small wire:loading.delay.shortest class="text-muted small px-2">
                        Procesando...
                    </small>
                </div>

                <i class="{{$icon_menus['options'] ?? ''}} text-info" aria-hidden="true"></i>
                <span class=" font-weight-bold">Fichas de Estudiantes</span>

            </h3>

            {{-- @include('livewire.bienestars.bienestar.modals.helps') --}}

        </div>

        <div class="card-body p-1 m-1">

            @if (Session::has('operp_ok'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {!! Session::get('operp_ok') !!}
                </div>
            @endif

            <div class="container-fluid mx-0 px-0">

                <div class="row">

                    <div class="col-{{ $modeIndex ? '12' : '5' }}" style="{{ ( ! $modeIndex ) ? 'opacity: 0.6;' : null}}" wire:key="{{Str::random()}}">
                        <div class="border rounded" style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                            <h5 class="p-2 m-2 font-weight-bold">
                                Listado de estudiantes con inscripción formalizada
                            </h5>
                            {{-- {{$estudiants }} --}}
                            @include('livewire.bienestar.student-record.table.index')
                            {{-- /home/nuser/code/s2223/resources/views/livewire/bienestar/student-record/table/index.blade.php --}}
                        </div>
                    </div>

                    @if ($modeCreate)
                        <div class="col-7" >
                            <div class="" wire:key="{{Str::random()}}">
                                @include('livewire.bienestar.student-record.form.create')
                            </div>
                        </div>
                    @endif

                    @if ($modeEdit)
                        <div class="col-7">
                            <div class="" wire:key="{{Str::random()}}">
                                @include('livewire.bienestar.student-record.form.edit')
                            </div>
                        </div>
                    @endif


                </div>

            </div>

        </div>

    </div>
