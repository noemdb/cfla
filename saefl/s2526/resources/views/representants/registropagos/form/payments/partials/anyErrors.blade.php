@if ($errors->any())
    <span>
        Corrija lo siguiente:
    </span>
    <div class="alert alert-danger px-1 small">
        <ol class="ml-2 pl-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ol>
    </div>
    <hr>
@endif
