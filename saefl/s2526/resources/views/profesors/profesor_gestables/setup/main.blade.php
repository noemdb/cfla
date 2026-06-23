<div class="card shadow">
    <div class="card-body p-0">
        <div class="border rounded ">
            <h5 class="card-title alert alert-secondary">
                @php
                    $fullname = $selected->fullname;
                    $fullname_sm = ($fullname) ? Str::limit(strtoupper($fullname),32): null;
                    $asignatura = ($selected) ? $selected->asignatura : null ;
                @endphp
                <span class="small font-weight-bold" title="{{$fullname ?? null}}">
                    {{ ($asignatura) ? $asignatura->code : ''}} {{ $fullname ?? ''}}
                </span>
            </h5>
            <p class="card-text">
                <div class="p-1">
                    @include('profesors.profesor_gestables.setup.evaluacions')
                </div>
                <hr>
                <div class="p-1">
                    @include('profesors.profesor_gestables.setup.create')
                </div>
            </p>
        </div>
    </div>
</div>
