<div class="card">
    <i class="bi bi-clock text-info" style="font-size: 2rem"></i>
    <h5 class="card-title text-info">Agenda de Reuniones</h5>
    <div class="card-body">
        @forelse ($incidents_announcements as $incident)

            @php
                $estudiant = $incident->estudiant;
                $representant = $estudiant->representant;
                $now = Carbon\Carbon::now();
            @endphp

            <div class="" x-data="{ open: false }">

                <div class="text-secondary text-center" @click="open = ! open" role="button">
                    <div class="accordion-header border rounded">
                        <button type="button" class="accordion-button collapsed p-1">
                            <div class="d-flex justify-content-between">
                                <div class="ms-2 me-auto small">
                                    <div class="fw-bold {{ ($incident->time_announcement > $now) ? : null}}text-success">{{$loop->remaining + 1}}. {{$representant->name ?? null}}</div>
                                    <span class="small text-muted ms-2">{{ ($incident->time_announcement) ? $incident->time_announcement->format('j \d\e M \d\e Y h.i a') : null}}</span>
                                    <div class="small text-muted ms-2">Est. {{ $estudiant->fullname }}</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition>
                    <div class="card card-body">
                        <div class="text-muted pb-2">
                            @include('livewire.movile.profesor.incident.partials.incident_ul')
                        </div>
                    </div>
                </div>

            </div>

        @empty

            <div>No hay datos</div>

        @endforelse
    </div>
</div>




{{--

    <div x-data="{ open: false }">
        <button @click="open = ! open">Toggle</button>

        <span x-show="open" x-transition>
            Hello 👋
        </span>
    </div>

--}}
