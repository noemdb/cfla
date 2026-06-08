<div class="row">
    <div class="col-md-2">
        {!! Form::select('cuentaxpagar_id', $list_cuentaxpagar, $cuentaxpagar_id, ['class'=>'form-control form-control-sm','placeholder'=>'Concepto de Cobro','id'=>'cuentaxpagar_id']) !!}
    </div>
    
    <div class="col-md-2">
        {!! Form::date('finicial', $finicial, ['class'=>'form-control form-control-sm','placeholder'=>'Fecha Inicial','id'=>'finicial']) !!}
    </div>
    
    <div class="col-md-2">
        {!! Form::date('ffinal', $ffinal, ['class'=>'form-control form-control-sm','placeholder'=>'Fecha Final','id'=>'ffinal']) !!}
    </div>
    
    <div class="col-md-2">
        {!! Form::text('ci', $ci, ['class'=>'form-control form-control-sm','placeholder'=>'Cédula Est./Rep.','id'=>'ci']) !!}
    </div>

    <div class="col-md-2">
        {!! Form::select('cancellation_status', [
            '' => 'Todos los estados',
            'active' => 'Activos',
            'cancelled' => 'Anulados',
            'pending_approval' => 'Pendiente Aprobación'
        ], $cancellation_status, ['class'=>'form-control form-control-sm','id'=>'cancellation_status']) !!}
    </div>
    
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-search"></i> Buscar
        </button>
        <button type="button" class="btn btn-success btn-sm" id="btn_xls">
            <i class="fas fa-file-excel"></i> XLS
        </button>
    </div>
</div>
