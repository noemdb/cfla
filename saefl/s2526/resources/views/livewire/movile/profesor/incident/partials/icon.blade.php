<div class="d-flex justify-content-center rounded">
    @switch($item['type'])
        @case('registerIncident')
            <i class="{{ $icon_menus['incidents'] ?? '' }} fa-3x"></i>
        @break

        @case('sendNotifyIncident')
            <i class="{{ $icon_menus['incidents'] ?? '' }} fa-2x mr-1"></i>
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-2x"></i>
        @break

        @case('sendNotifyIncidentAgreement')
            <i class="{{ $icon_menus['incident_agreements'] ?? '' }} fa-2x mr-1"></i>
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-2x"></i>
        @break

        @case('sendNotifyIncidentInterview')
            <i class="{{ $icon_menus['queuing'] ?? '' }} fa-2x mr-1"></i>
        @break

        @case('closeIncidet')
            <i class="{{ $icon_menus['lock'] ?? '' }} fa-3x"></i>
        @break

        @case('sendNotifycloseIncidet')
            <i class="{{ $icon_menus['lock'] ?? '' }} fa-2x mr-1"></i>
            <i class="{{ $icon_menus['mail'] ?? '' }} fa-2x"></i>
        @break
    @endswitch
</div>
