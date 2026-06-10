@extends('plannings.layouts.dashboard.app')

@section('main')

<main role="main">

    <div class="card card-primary mt-2 bd-callout bd-callout-success">

        <div class="card-header alert-success py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 font-weight-bold">
                    <i class="{{$icon_menus['activities'] ?? 'fas fa-tasks'}} text-success pr-1" aria-hidden="true"></i>
                    Módulo Planificación Académica
                    <span class="badge badge-light text-success ml-2 align-middle" style="font-size:0.7rem;">
                        <i class="fa fa-graduation-cap mr-1"></i>{{ count($pestudios) }} Plan(es) Educativo(s)
                    </span>
                </h4>
                <div class="d-flex align-items-center">
                    <a href="{{ route('plannings.activities.index') }}" class="btn btn-sm btn-outline-success mr-2" title="Refrescar">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                    <small class="text-muted font-italic" style="opacity:0.7;">
                        <i class="fa fa-user-tie mr-1"></i>Diseñado por: Prof. Carmin Cortez
                    </small>
                </div>
            </div>

            <div class="mt-1 small text-muted">
                <i class="fa fa-book-open mr-1"></i>
                <span class="font-weight-bold">Planes Educativos: </span>
                @forelse ($pestudios as $pestudio)
                    <span class="badge badge-light border ml-1 px-2 py-1">{{$pestudio->name ?? null}}</span>
                @empty
                    <span class="text-warning">Sin planes educativos asignados</span>
                @endforelse
            </div>
        </div>

        <div class="card-body p-2 m-1">

            {{-- Indicadores rápidos --}}
            <div class="row mb-2">
                <div class="col-md-4 col-sm-6 mb-1">
                    <div class="small bg-light p-2 rounded border-left border-success d-flex align-items-center">
                        <i class="fas fa-chalkboard-teacher text-success mr-2 fa-lg"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size:0.65rem;">PROFESORES</span>
                            <span class="font-weight-bold">{{ $profesors->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-1">
                    <div class="small bg-light p-2 rounded border-left border-info d-flex align-items-center">
                        <i class="fas fa-calendar-alt text-info mr-2 fa-lg"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size:0.65rem;">LAPSO ACTIVO</span>
                            <span class="font-weight-bold">{{ $lapso_active->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-1">
                    <div class="small bg-light p-2 rounded border-left border-secondary d-flex align-items-center">
                        <i class="fas fa-layer-group text-secondary mr-2 fa-lg"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size:0.65rem;">LAPSOS REGISTRADOS</span>
                            <span class="font-weight-bold">{{ $lapsos->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <livewire:planning.activity.index-component/>

        </div>

    </div>

</main>

@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                console.log('event');
                if (result.value) {
                    window.livewire.emit('remove',e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                toast: e.detail.toast,
                position: e.detail.position,
            })
            .then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection