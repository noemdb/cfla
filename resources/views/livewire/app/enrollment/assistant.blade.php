@if ($census)
    @include('livewire.app.enrollment.modal.estudiant')
@else
    @include('livewire.app.enrollment.modal.empty')
@endif