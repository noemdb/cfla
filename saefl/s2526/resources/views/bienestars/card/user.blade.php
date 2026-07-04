@php
$user = Auth::user();
$profile = $user->profile;
$rol = $user->full_rol;
@endphp

<div class="card h-100 pt-2 mt-2">

    {{-- {{$user}} --}}
    {{-- {{$rol}} --}}

    <div class=" text-center">
        <img class="pt-1" width="100px" height="100px" src="{{ (isset($user->logo)) ? asset($user->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
    </div>

    <div class="card-body p-1">

        <div class="small pl-2 text-muted d-block">
            <div class="font-weight-bold">
                {{$user->username ?? ''}}
            </div>
            <div>
                {{$user->email ?? ''}}
            </div>
        </div>

        <hr>

        <div class="small text-muted">
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['name'] ?? ''}}</dt>
                {{$user->fullname ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['email'] ?? ''}}</dt>
                {{$user->email ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>{{$list_comment['is_active'] ?? ''}}</dt>
                {{ ($user->is_active=='enable') ? 'Activo':'Desactivo' }}
            </ol>

            <ol class="ml-1 pl-1">
                <dt>Fecha Inicial</dt>
                {{ (!empty($rol->finicial)) ? $rol->finicial->format('d-m-Y'): null}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Fecha Final</dt>
                {{ (!empty($rol->ffinal)) ? $rol->ffinal->format('d-m-Y') : null}}
            </ol>

        </div>

    </div>

</div>
{{-- 'pescolar_id','institucion_id','user_id','tipo_id','name','lastname','ci','position','profile_professional','photo','finicial','ffinal' --}}
