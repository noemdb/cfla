<!-- Example single danger button -->
<div class="btn-group dropleft">
    <button type="button" class="btn btn-ligth dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="{{ $icon_menus['options'] ?? ''}} fa-1x"></i>
    </button>
    <div class="dropdown-menu">
      @include('livewire.academico.mailer.table.partials.btnModeIndex',['key'=>'others'])
    </div>
</div>