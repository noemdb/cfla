@extends('plannings.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    {{-- @include('evaluacions.pases.menus.index') --}}
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{ $icon_menus['crud'] ?? '' }} text-info" aria-hidden="true"></i>
                    <span class=" font-weight-bold">Gestionamiento de Competiciones</span>
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ $autoridad->fullname ?? null }}
                </span>

                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2 small">{{ $pestudio->name ?? null }}</span>
                        @if (!$loop->last)
                            ||
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="card-body p-1 m-1">
                <ul class="nav nav-tabs nav-fill" id="competitionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab"
                            aria-controls="list" aria-selected="true">
                            <i class="fas fa-list"></i> Listado de Competiciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab"
                            aria-controls="calendar" aria-selected="false">
                            <i class="fas fa-chart-bar"></i> Indicadores de la Competición Académica
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="competitionTabsContent">
                    <div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
                        <div class="p-2">
                            <livewire:planning.competition.index-component />
                        </div>

                    </div>
                    <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                        <div class="p-2">
                            <livewire:planning.competition.debate-indicators :competitionId="1" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('closeModal', function(e) {
            $('#cloneModal').modal('hide');
        });

        window.addEventListener('swal:confirm', function(e) {
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
                        window.livewire.emit('remove', e.detail.id);
                    }
                });
        });

        window.addEventListener('swal:question', function(e) {
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
                        window.livewire.emit(e.detail.method, e.detail.id);
                    }
                });
        });
    </script>
@endsection
