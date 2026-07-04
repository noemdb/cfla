<div class="container-fluid">
    <h5 class="alert alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
        Registrar nueva <b>Post</b>
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>
    @include('livewire.administracion.blog.form.fields')
</div>