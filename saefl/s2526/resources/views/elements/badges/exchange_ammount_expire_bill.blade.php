@if ($exchange_ammount_expire_bill==0)
    @if ($estudiant->administrativa)
        <h6><span class="badge badge-success"> SOLVENTE </span> </h6>
    @else
        <h6><span class="badge badge-secondary" title="Sin inscripción administrativa"> S.I.ADM</span> </h6>
    @endif
@else
    <h6><span class="badge badge-danger"> $ {{ f_float($exchange_ammount_expire_bill) }} </span> </h6>
@endif
