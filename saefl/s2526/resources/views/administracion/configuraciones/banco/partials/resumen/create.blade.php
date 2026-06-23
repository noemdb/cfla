<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos bancos registrados</p>
    <small class="px-1">
        @foreach ($bancos as $banco)
            <div class="row align-items-center">
                <div class="col-2 text-right h-auto">
                    <span class=" font-weight-bold">{{$loop->iteration}}</span>
                </div>
                <div class="col-10">
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['name']}}</dt>
                        <dd class="text-secondary">{{ $banco->name}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['description']}}</dt>
                        <dd class="text-secondary">{{ $banco->description}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['currency_id']}}</dt>
                        <dd class="text-secondary">{{ ($banco->currency) ? $banco->currency->name : null}}</dd>
                    </dl>
                </div>
            </div>
            <hr>
        @endforeach
    </small>
</div>
