<div>

    <span class="p-2">
        <i class="{{ $icon_menus['assit_weeks'] ?? ''}} fa-1x text-dakr"></i>
        <b>Días Registrados</b>
    </span>

    <livewire:administracion.assist-control.assit-day.nav-bar-component :id="$assit_week->id" :key="'assit-day-main-nav-bar-'.$assit_week->id"/>

    <livewire:administracion.assist-control.assit-day.nav-content-component :id="$assit_week->id" :key="'assit-day-main-nav-content-'.$assit_week->id"/>

</div>
