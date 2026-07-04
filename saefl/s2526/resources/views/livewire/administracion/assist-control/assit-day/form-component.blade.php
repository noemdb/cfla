<div>
    <div class="card border-0">
        <div class="card-body">
            @include('livewire.elements.messeges.oper_ok')

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="name" class="font-weight-bold text-muted pb-0 mb-0">{{ $list_comment_assit_day['name'] ?? 'Nombre' }}</label>
                        <input type="text" wire:model.defer="name" class="form-control" placeholder="Nombre del día">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="number_day" class="font-weight-bold text-muted pb-0 mb-0">{{ $list_comment_assit_day['number_day'] ?? 'Número del día' }}</label>
                        <select wire:model.defer="number_day" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach(range(1, 7) as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('number_day') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <button wire:click="save" class="btn btn-primary btn-block">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
