@foreach ($estudiant->expire_bills as $expire_bill)

    <dl class="mb-1">
        <dt>{{$expire_bill->name}}</dt>
        <dd>

            <h6 class="pt-0 mt-0">
                <span class="badge badge-danger">
                    {{f_float($expire_bill->TotalMontoConceptosXPagar($estudiant->id))}}
                </span>
            </h6>

        </dd>
    </dl>

@endforeach

<div class="dropdown-divider mb-0"></div>