{{-- Dropdown «Formatos» del asistente light. Dos variantes según el contexto: --}}
{{--  - $withCalendar === true (hay calendario abierto): PDF P.Estudio del --}}
{{--    calendario abierto y reportes acotados a él. --}}
{{--  - $withCalendar === false (paso 1): exportaciones globales del lapso. --}}
@props(['withCalendar' => false])
@if ($withCalendar)
    <x-dropdown position="bottom-end" width="7xl" height="auto">
        <x-slot name="trigger">
            <x-button
                label="Formatos"
                icon="document-text"
                right-icon="chevron-down"
                color="base"
                variant="outline"
                class="font-bold" />
        </x-slot>

        @if ($pestudioExportUrl)
            <x-dropdown.item
                href="#"
                x-on:click.prevent="$dispatch('open-pestudio-prev-export')"
                icon="folder"
                label="PDF P.Estudio…" />
        @endif

        <x-dropdown.item
            href="{{ route($moduleRoutePrefix.'.timetable.pdf.teachers', ['calendar' => (int) $calendarId]) }}"
            target="_blank"
            rel="noopener"
            icon="document-arrow-down"
            label="PDF profesores" />

        <x-dropdown.item
            href="#"
            x-on:click.prevent="$dispatch('open-teachers-pdf')"
            icon="users"
            label="Consolidado de docentes" />

        <x-dropdown.item
            href="#"
            x-on:click.prevent="$dispatch('open-teacher-totals')"
            icon="table-cells"
            label="Totalización por docente"
            separator />
    </x-dropdown>
@else
    <x-dropdown position="bottom-end" width="5xl" height="auto">
        <x-slot name="trigger">
            <x-button
                label="Formatos"
                icon="document-text"
                right-icon="chevron-down"
                color="base"
                variant="outline"
                class="font-bold" />
        </x-slot>

        <x-dropdown.item
            href="#"
            x-on:click.prevent="$dispatch('open-all-pestudios-export')"
            icon="document-text"
            label="Exportar todos los P.Estudios…" />

        <x-dropdown.item
            href="#"
            x-on:click.prevent="$dispatch('open-teachers-pdf')"
            icon="document-arrow-down"
            label="Consolidado de docentes" />

        <x-dropdown.item
            href="#"
            x-on:click.prevent="$dispatch('open-teacher-totals')"
            icon="table-cells"
            label="Totalización por docente"
            separator />
    </x-dropdown>
@endif
