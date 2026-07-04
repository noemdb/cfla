@php $director = Auth::user()->autoridad; @endphp
<div class="card h-100 pt-2 mt-2">

    <div class=" text-center">
        <img class="pt-1" width="100px" height="100px" src="{{ (isset($director->logo)) ? asset($director->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
    </div>

    <div class="card-body p-1">

        <small class="align-text-bottom text-mute d-block pb-2 text-uppercase">
            {{$list_comment['institucion_id']}}: <br>
            <span class=" font-weight-bolder">
                {{$director->institucion->name ?? ''}}
            </span>
        </small>

        <small class="align-text-bottom text-mute d-block text-uppercase">
            {{$list_comment['user_id']}}:
            <span class=" font-weight-bolder">
                {{$director->user->username ?? ''}}
            </span>
        </small>
        <br>

        <div class="small text-muted">
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['name']}}</dt>
                {{$director->fullname ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['ci']}}</dt>
                {{$director->ci ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['tipo_id']}}</dt>
                {{$director->tautoridad->name ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['position']}}</dt>
                {{$director->position ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['profile_professional']}}</dt>
                {{$director->profile_professional ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['finicial']}}</dt>
                {{ (!empty($director->finicial)) ? f_date($director->finicial) : null}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['ffinal']}}</dt>
                {{ (!empty($director->ffinal)) ? f_date($director->ffinal) : null}}
            </ol>

        </div>

    </div>

</div>
{{-- 'pescolar_id','institucion_id','user_id','tipo_id','name','lastname','ci','position','profile_professional','photo','finicial','ffinal' --}}
