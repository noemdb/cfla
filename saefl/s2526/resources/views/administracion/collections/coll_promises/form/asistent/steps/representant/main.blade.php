@php
    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);

    $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    $total_credito_exchange = $representant->total_credito_exchange ;
    $total_abono_exchange = $representant->total_abono_exchange ;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

    //..unexpired_paid
    $ammount_unexpired_bill_paid = $representant->ammount_unexpired_bill_paid;
    $exchange_ammount_unexpired_bill_paid = $representant->exchange_ammount_unexpired_bill_paid;

    //..unexpired bill
    $exchange_ammount_unexpired_bill = $representant->exchange_ammount_unexpired_bill;

    $ammount_expire_bill = $representant->ammount_expire_bill ;
    $ammount_no_expire_bill = ($representant->ammount_no_expire_bill>0) ? $representant->ammount_no_expire_bill : null ;
    $total_credito = $representant->total_credito ;
    $total_abono = $representant->total_abono ;
    $saldo_a_favor = $total_credito+$total_abono;
    $border_class = ($ammount_expire_bill>0) ? 'danger' : 'success' ;
    $border_class = "border border-".$border_class." rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0";
    $color = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->seccion->grado->color : 'default' ;
    $class_callout = "bd-callout bd-callout bd-callout-".$color;

    $estudiants = $representant->estudiants_formaly;
    $status_phone = $representant->status_phone;
@endphp

@include('administracion.collections.coll_promises.form.asistent.steps.representant.fields.personal')
@include('administracion.collections.coll_promises.form.asistent.steps.representant.fields.contact')
@include('administracion.collections.coll_promises.form.asistent.steps.representant.fields.ident1')
@include('administracion.collections.coll_promises.form.asistent.steps.representant.fields.ident2')
@include('administracion.collections.coll_promises.form.asistent.steps.representant.fields.bills')
