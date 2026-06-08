@php $user = $representant->user @endphp

@includeWhen(!$user->status_update, 'representants.home.modals.updateUser')
{{-- @includeWhen(!$user->status_update, 'representants.home.modals.updateUserEmail') --}}
{{-- @includeWhen(!$user->status_update, 'representants.home.modals.info') --}}
{{-- @includeWhen(!$representant->status_update, 'representants.modals.home.updateProfile') --}}

{{-- @includeWhen(true, 'representants.modals.home.updateUser') --}}
{{-- @includeWhen(true, 'representants.modals.home.updateProfile') --}}
