<div>

    <span class="p-2">
        <i class="{{ $icon_menus['assit_weeks'] ?? ''}} fa-1x text-dakr"></i>
        <b>Semanas Registradas</b>
    </span>

    <livewire:administracion.assist-control.assit-week.nav-bar-component :id="$assit_schedule->id" :key="'assit-week-main-nav-bar-'.$assit_schedule->id"/>

    <livewire:administracion.assist-control.assit-week.nav-content-component :id="$assit_schedule->id" :key="'assit-week-main-nav-content-'.$assit_schedule->id"/>

</div>
