<div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$mailer->id}}">
    @php $disabled = ($mailer->status_ready) ? ' disabled ' : null ; @endphp
    @php $status_date = ($mailer->date <= now()) ? ' disabled ' : null ; @endphp
    @php $status = ($mailer->status == "false") ? ' disabled ' : null ; @endphp
    <button class="btn btn-warning btn-sm" wire:click="edit({{$mailer->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" wire:key="{{$key}}-btn-mailer-edit-{{$mailer->id}}" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
    <button wire:click="preview({{$mailer->id}})" class="btn btn-info btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-mailer-preview-{{$mailer->id}}"><i class="{{ $icon_menus['eye'] ?? ''}} fa-1x" title="Vista previa"></i></button>
    <button wire:click="show({{$mailer->id}})" class="btn btn-dark btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-mailer-show-{{$mailer->id}}"><i class="{{ $icon_menus['info'] ?? ''}} fa-1x" title="Destinatario"></i></button>
    <button wire:click="alertQuestion({{$mailer->id}},'EmailForQueuing')" class="btn btn-success btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-mailer-queuing-{{$mailer->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Crear cola"><i class="{{ $icon_menus['sendmail'] ?? ''}} fa-1x"></i></button>                        
    <button wire:click="alertConfirm({{$mailer->id}})" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-mailer-delete-{{$mailer->id}}" {{ $disabled ?? null }} {{ $status ?? null }}><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x" title="Eliminar"></i></button>                        
</div>