<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="pescolar_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['pescolar_id'] ?? '' }}</label>
                {!! Form::select('pescolar_id', $pescolar_list, old('pescolar_id'), [
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
        <div class="col-sm-3">
            <div class="form-group">
                <label for="code"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['code'] ?? '' }}</label>
                {!! Form::text(
                    'code',
                    old('code'),
                    ['class' => 'form-control', 'placeholder' => $list_comment['code'], 'id' => 'code'],
                    'required',
                ) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="status_debts"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_debts'] ?? '' }}</label>
                {!! Form::select('status_debts', ['true' => 'Si', 'false' => 'No'], old('status_debts'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="numbers_bills"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['numbers_bills'] ?? '' }}</label>
                {!! Form::selectRange('numbers_bills', 1, 20, old('numbers_bills'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="canon"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['canon'] ?? '' }}</label>
                {!! Form::select('canon', $list_coll_politicals_canon, old('canon'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
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
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="finicial"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['finicial'] ?? '' }}</label>
                {{-- @php $finicial = (empty($coll_political)) ? $coll_political->finicial->format('Y-m-d'): null ; @endphp --}}
                {!! Form::date('finicial', old('finicial'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['finicial'],
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="ffinal"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['ffinal'] ?? '' }}</label>
                {{-- @php $finicial = (old('finicial')) ? $exchange_rate->date : null ; @endphp --}}
                {!! Form::date('ffinal', old('ffinal'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['ffinal'],
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        {{-- <div class="col-sm-4">
            <div class="form-group">
                <label for="canon" class="font-weight-bold text-secondary m-0">{{$list_comment['canon'] ?? ''}}</label>
                {!! Form::select('canon',$list_coll_politicals_canon,old('canon'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
            </div>
        </div> --}}
    </div>

    @if (Request::is('*edit*'))
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="status"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status'] ?? '' }}</label>
                    {!! Form::select('status', ['true' => 'Activa', 'false' => 'Desactiva'], old('status'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="status_approved"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_approved'] ?? '' }}</label>
                    {!! Form::select('status_approved', ['true' => 'Aprobada', 'false' => 'Desaprobada'], old('status_approved'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
        </div>
    @endif

</div>
