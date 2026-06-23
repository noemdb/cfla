@php
    $class['index']="";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['ammount']="d-none d-lg-table-cell";
    $class['ammount']="d-none d-lg-table-cell";
    $class['oepration']="d-none d-lg-table-cell";
    $class['bank_acronym']="d-none d-lg-table-cell";
    $class['created_at']="d-none d-lg-table-cell";
    $class['status']="";
    $class['action']="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class['index'] ?? ''}}">N</th>
                <th class="{{ $class['representant_id'] ?? ''}}">{{$list_comment['representant_id'] ?? ''}}</th>
                <th class="{{ $class['oepration'] ?? ''}}">{{$list_comment['oepration'] ?? ''}}</th>
                <th class="{{ $class['bank_acronym'] ?? ''}}">{{$list_comment['bank_acronym'] ?? ''}}</th>
                <th class="{{ $class['ammount'] ?? ''}}">{{$list_comment['ammount'] ?? ''}}</th>
                <th class="{{ $class['created_at'] ?? ''}}">{{$list_comment['created_at'] ?? ''}}</th>
                <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
                {{-- <th class="{{ $class['json'] ?? ''}}">{{$list_comment['json'] ?? ''}}</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($transactions as $transaction)

            <tr data-id="{{$transaction->id}}" class="{{ ($transaction->status) ? 'table-success' : 'text-danger' }}">
                <td id="td-count" class="{{ $class['index'] ?? ''}}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class['representant_id'] ?? ''}}">
                    {{ $transaction->representant->name}} <br> {{ $transaction->representant->ci_representant}}
                </td>
                <td class=" text-uppercase {{ $class['oepration'] ?? ''}}">
                    approval: {{ $transaction->approval }} || sequence: {{ $transaction->sequence }}
                </td>
                <td class="{{ $class['bank_acronym'] ?? ''}}">
                    {{ $transaction->bank_acronym ?? null }}
                </td>
                <td class="{{ $class['ammount'] ?? ''}}">
                    {{f_float($transaction->ammount) }}
                </td>
                <td class="{{ $class['created_at'] ?? ''}}">
                    {{$transaction->created_at->format('d-m-Y h:i:s') ?? null}}
                </td>
                <td class=" text-uppercase {{ $class['status'] ?? ''}}">
                    {{ ($transaction->status) ? 'SI' : 'NO' }} APROBADO
                </td>
                {{-- <td class="{{ $class['json'] ?? ''}}">
                    {{ $transaction->data ?? null }}
                </td> --}}
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
