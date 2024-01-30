@if ($census)
    {{-- @if ($status_enrollment_exists)
        @include('livewire.app.enrollment.modal.ready')
    @else
        @include('livewire.app.enrollment.modal.estudiant')
    @endif     --}}
@else
    @include('livewire.app.enrollment.modal.empty')
@endif