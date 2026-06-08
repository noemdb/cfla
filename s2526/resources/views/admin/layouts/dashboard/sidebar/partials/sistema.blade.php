{{-- INI User --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Usuarios')
    @slot('nrcrud', 'users.index')
    @slot('nrchart', 'users.chart')
    @slot('icon', $icon_menus['user'])
@endcomponent
{{-- FIN user --}}


{{-- INI profiles --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Perfiles')
    @slot('nrcrud', 'profiles.index')
    @slot('nrchart', 'profiles.chart')
    @slot('icon', $icon_menus['profile'])
@endcomponent
{{-- FIN profiles --}}


{{-- INI Rols --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Roles')
    @slot('nrcrud', 'rols.index')
    @slot('nrchart', 'rols.chart')
    @slot('icon', $icon_menus['rol'])
@endcomponent
{{-- FIN Rols --}}

{{-- INI Tasks --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Tasks')
    @slot('nrcrud', 'tasks.index')
    @slot('nrchart', 'tasks.chart')
    @slot('icon', $icon_menus['task'])
@endcomponent
{{-- FIN Tasks --}}

{{-- INI Alertas --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Alertas')
    @slot('nrcrud', 'alerts.index')
    @slot('nrchart', 'alerts.chart')
    @slot('icon', $icon_menus['alert'])
@endcomponent
{{-- FIN Alertas --}}

{{-- INI Mensajes --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Mensajes')
    @slot('nrcrud', 'messeges.index')
    @slot('nrchart', 'messeges.chart')
    @slot('icon', $icon_menus['messege'])
@endcomponent
{{-- FIN Mensajes --}}

{{-- INI settings --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Settings')
    @slot('nrcrud', 'settings.index')
    {{-- @slot('nrchart', 'settings.chart') --}}
    @slot('icon', $icon_menus['setting'])
@endcomponent
{{-- FIN settings --}}

{{-- INI SelectOpts --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'SelectOpts')
    @slot('nrcrud', 'selectopts.index')
    {{-- @slot('nrchart', 'selectopts.chart') --}}
    @slot('icon', $icon_menus['selectopt'])
@endcomponent
{{-- FIN SelectOpts --}}

{{-- INI Loginout --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Loginout')
    @slot('nrcrud', 'loginouts.index')
    @slot('nrchart', 'loginouts.chart')
    @slot('icon', $icon_menus['loginout'])
@endcomponent
{{-- FIN Loginout --}}

{{-- INI Logdb --}}
@component('admin.layouts.dashboard.sidebar.elements.models')
    @slot('nombre', 'Logdb')
    @slot('nrcrud', 'logdbs.index')
    @slot('nrchart', 'logdbs.chart')
    @slot('icon', $icon_menus['logdb'])
@endcomponent
{{-- FIN Logdb --}}