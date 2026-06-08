@php $representant_id = (!empty($representant_id)) ? $representant_id : null ; @endphp
@php $help_representante = (!empty($help_representante)) ? $help_representante : null ; @endphp

{!! Form::open([
    'route' => 'administracion.collections.coll_promises.asistent',
    'method' => 'GET',
    'class' => 'pb-2',
    'role' => 'search',
]) !!}

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-10">
            <div class="form-group">
                <label for="coll_political_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['coll_political_id'] ?? '' }}</label>
                {!! Form::select('coll_political_id', $list_coll_politicals, $coll_political_id, [
                    'class' => 'form-control',
                    'id' => 'coll_political_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <br>
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
