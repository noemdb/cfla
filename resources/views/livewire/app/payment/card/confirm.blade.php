<ul class="text-xs">
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'ci_representant'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'type_pay'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    {{-- <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'phone'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li> --}}
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'comment'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'phone_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'number_i_pay_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'banco_id_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$list_bank[$payment->banco_id_1] ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'banco_emisor_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$banco_emisor_list[$payment->banco_emisor_1] ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'method_pay_id_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$method_pay_list[$payment->method_pay_id_1] ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'date_transaction_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">{{$payment->$name ?? null}}</span>        
    </li>
    <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-2 dark:border-opacity-50">
        @php $name = 'ammount_1'; $label = $list_comment[$name] @endphp
        <span class="text-gray-600">{{ $label ?? null}}:</span> <span class="text-gray-800 font-bold uppercase">Bs. {{ number_format($payment->ammount_1,2,',','.') ?? null}}</span>        
    </li>
</ul>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif