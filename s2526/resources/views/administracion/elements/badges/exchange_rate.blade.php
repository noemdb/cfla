@php $title = ($exchange_rate_ammount) ? 'TDC: '.f_float($exchange_rate_ammount) : null @endphp
<span title="{{$title ?? ''}}" id="ingreso_ammount" class=" table-secondary text-dark px-1 mx-1 border border-dark rounded shadow-sm">$ {{ f_float($exchange_ammount) }}</span>
