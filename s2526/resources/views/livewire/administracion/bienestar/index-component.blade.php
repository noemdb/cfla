<div>

    <div class="card-header  alert-dark mt-2">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">                
                {{-- @include('livewire.administracion.bienestar.menu.index')                   --}}
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small> 
            </div>            

            <i class="{{$icon_menus['options'] ?? ''}} text-info" aria-hidden="true"></i>
            <span class=" font-weight-bold">Fichas de Estudiantes</span>

        </h3>

        {{-- @include('livewire.administracion.bienestar.modals.helps') --}}

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

        <div class="container-fluid">

            <div class="row">

                <div class="col-{{ ($modeIndex) ? '12' : '5'}}">
                    <div class="border rounded" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                        <h4 class="alert alert-secondary ">
                            Listado de estudiantes registrados
                        </h4>
                        {{-- {{$estudiants }} --}}
                        @include('livewire.administracion.bienestar.table.index')
                    </div>
                </div>
                
                @if ($modeCreate)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.bienestar.form.create')
                        </div>
                    </div>
                @endif
                
                @if ($modeEdit)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.bienestar.form.edit')
                        </div>
                    </div>
                @endif
                

            </div>

        </div>

    </div>

</div>
