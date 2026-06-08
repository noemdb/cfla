<div>

    <div class="p-1 m-1 border rounded shadow">

        <div class="m-1 small">

            @php $incidents_announcements =  $profesor->incidents_announcements->sortByDesc('time_announcement') @endphp

            @include('livewire.movile.profesor.incident.partials.incident')

        </div>

    </div>

</div>
