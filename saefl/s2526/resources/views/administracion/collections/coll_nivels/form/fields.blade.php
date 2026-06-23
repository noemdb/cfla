{{-- 'coll_political_id','name','order','weighing','description','status' --}}

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="coll_political_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['coll_political_id'] ?? '' }}</label>
                {!! Form::select('coll_political_id', $list_coll_politicals, old('coll_political_id'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="name"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
                {!! Form::text('name', old('name'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['name'],
                    'id' => 'name',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="weighing"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['weighing'] ?? '' }}</label>
                {!! Form::text('weighing', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['weighing'],
                    'id' => 'weighing',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="order"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['order'] ?? '' }}</label>
                {!! Form::selectRange('order', 1, 10, old('order'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="description"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
                {!! Form::text('description', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['description'],
                    'id' => 'description',
                    'required',
                ]) !!}
            </div>
        </div>
        @if (Request::is('*edit*'))
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="status"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status'] ?? '' }}</label>
                    {!! Form::select('status', ['true' => 'Activo', 'false' => 'Desactivo'], old('status'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
        @endif
    </div>

</div>
