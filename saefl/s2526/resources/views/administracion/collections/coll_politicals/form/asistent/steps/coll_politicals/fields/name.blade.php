<div class="first-of-type" id="step-coll_politicals-name">
    <div class="alert alert-secondary rounded flex-center px-4 py-2" style="min-height: 25rem;">
        <div>
            <h4>Ingrese el nombre, un código de 5 caracterés y una descripción, con los cuales se identificará la
                notificación</h4>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name"
                                class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
                            {!! Form::text('name', old('name'), [
                                'class' => 'form-control',
                                'placeholder' => $list_comment['name'],
                                'id' => 'name',
                            ]) !!}
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="code"
                                class="font-weight-bold text-secondary m-0">{{ $list_comment['code'] ?? '' }}</label>
                            {!! Form::text('code', old('code'), [
                                'class' => 'form-control',
                                'placeholder' => $list_comment['code'],
                                'id' => 'code',
                            ]) !!}
                            @error('code')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="description"
                                class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
                            {!! Form::textarea('description', old('description'), [
                                'class' => 'form-control',
                                'placeholder' => $list_comment['description'],
                                'id' => 'description',
                                'rows' => '4',
                            ]) !!}
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_politicals-pescolar_id" data-step-hide="step-coll_politicals-name"
            data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-coll_politicals-group"
            data-step-hide="step-coll_politicals-name" data-direction="up" />
    </div>
</div>
