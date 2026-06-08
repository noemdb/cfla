<div class="p-1 m-1">

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="coll_political_id">{{ __('Política de Cobranza') }}</label>
        {!! Form::select('coll_political_id', $list_coll_politicals, old('coll_political_id'), [
            'wire:model' => 'coll_political_id',
            'class' => 'form-control',
            'required',
            'placeholder' => 'Seleccione',
        ]) !!}
        @error('coll_political_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="name">{{ __('Nombre') }}</label>
        <input type="text" class="form-control" id="name" wire:model.defer="name">
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="description">{{ __('Descripción') }}</label>
        <textarea class="form-control" id="description" wire:model.defer="description"></textarea>
        @error('description')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="date">{{ __('Fecha') }}</label>
        <input type="date" class="form-control" id="date" wire:model.defer="date">
        @error('date')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="time">{{ __('Hora') }}</label>
        <input type="time" class="form-control" id="time" wire:model.defer="time">
        @error('time')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <div class="">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        @php
                            $name = 'status_email';
                            $model = '' . $name;
                        @endphp {{-- <input type="checkbox" wire:model.defer="{{$model ?? null}}" /> --}}

                        <input type="checkbox" wire:model.defer="{{ $model ?? null }}" />
                    </div>
                </div>

                <div class="form-control small">@include('svg.mail', ['svg_class' => 'text-danger']) Correo Electrónico (Google Gmail)</div>

                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    @php
                        $name = 'status_whatsapp';
                        $model = '' . $name;
                    @endphp
                    {{-- <input type="checkbox" wire:model.defer="{{ $model ?? null }}" disabled> --}}
                </div>
            </div>
            <div class="form-control small">@include('svg.whatsapp', ['svg_class' => 'text-success']) Mensajería WAB (Meta - WhatsApp Business)</div>
        </div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="mb-0 pb-0 font-weight-bold" for="status_active">{{ __('Estado') }}</label>
        <select class="form-control" id="status_active" wire:model.defer="status_active">
            <option>Seleccione</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
        @error('status_active')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
