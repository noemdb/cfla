<div class="d-flex align-content-center bg-white p-2 rounded shadow-sm">
    @switch($item['type'])
        @case('registerIncident')
            {{-- <div class="d-flex justify-content-center align-items-center timeline-circle  shadow-sm bg-white text-{{ $item['class'] }}"> --}}
                <i class="{{ $icon_menus['incidents'] ?? '' }} fa-3x"></i>
            {{-- </div> --}}
        @break

        @case('sendNotifyIncident')
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-3x"></i>
        @break

        @case('sendNotifyIncidentAgreement')
            <i class="{{ $icon_menus['incident_agreements'] ?? '' }} fa-2x mr-1"></i>
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-2x"></i>
        @break

        @case('sendNotifyIncidentInterview')
            <i class="{{ $icon_menus['queuing'] ?? '' }} fa-2x mr-1"></i>
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-2x"></i>
        @break
    @endswitch
</div>
