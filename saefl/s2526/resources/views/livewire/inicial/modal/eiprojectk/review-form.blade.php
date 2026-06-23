<form wire:submit.prevent="saveReview">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_review['posibles_temas_interes'] }} *</label>
                <input type="text"
                    class="form-control @error('eiprojectreview.posibles_temas_interes') is-invalid @enderror"
                    wire:model.defer="eiprojectreview.posibles_temas_interes"
                    placeholder="Enumere los posibles temas de interés...">
                @error('eiprojectreview.posibles_temas_interes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_review['order'] }}</label>
                <input type="number" min="1"
                    class="form-control @error('eiprojectreview.order') is-invalid @enderror"
                    wire:model.defer="eiprojectreview.order" placeholder="Orden de presentación">
                @error('eiprojectreview.order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_review['eleccion_tema_nombre'] }} *</label>
        <input type="text" class="form-control @error('eiprojectreview.eleccion_tema_nombre') is-invalid @enderror"
            wire:model.defer="eiprojectreview.eleccion_tema_nombre" placeholder="Nombre del tema seleccionado">
        @error('eiprojectreview.eleccion_tema_nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_review['que_sabe'] }} *</label>
        <textarea class="form-control @error('eiprojectreview.que_sabe') is-invalid @enderror"
            wire:model.defer="eiprojectreview.que_sabe" rows="3" rows="3"
            placeholder="Describa lo que los estudiantes ya conocen sobre el tema..."></textarea>
        @error('eiprojectreview.que_sabe')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_review['que_desean_aprender'] }} *</label>
        <textarea class="form-control @error('eiprojectreview.que_desean_aprender') is-invalid @enderror"
            wire:model.defer="eiprojectreview.que_desean_aprender" rows="3"
            placeholder="Describa lo que los estudiantes desean aprender..."></textarea>
        @error('eiprojectreview.que_desean_aprender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_review['que_necesitamos'] }} *</label>
        <textarea class="form-control @error('eiprojectreview.que_necesitamos') is-invalid @enderror"
            wire:model.defer="eiprojectreview.que_necesitamos" rows="3"
            placeholder="Liste los recursos y materiales necesarios..."></textarea>
        @error('eiprojectreview.que_necesitamos')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_review['quienes_nos_pueden_apoyar'] }} *</label>
        <textarea class="form-control @error('eiprojectreview.quienes_nos_pueden_apoyar') is-invalid @enderror"
            wire:model.defer="eiprojectreview.quienes_nos_pueden_apoyar" rows="3"
            placeholder="Identifique las personas que pueden colaborar..."></textarea>
        @error('eiprojectreview.quienes_nos_pueden_apoyar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'estrategias';
            $model = 'eiprojectreview.' . $name;
        @endphp
        <label for="{{ $model }}"
            class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
        {!! Form::textarea($model, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_review[$name],
            'rows' => '4',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>
            {{ $eiprojectreview_id ? 'Actualizar' : 'Guardar' }} Revisión
        </button>
    </div>

</form>
