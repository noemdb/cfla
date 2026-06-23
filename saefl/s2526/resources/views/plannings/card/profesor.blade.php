@php
    $profesor = (empty($profesor)) ? Auth::user()->profesor : $profesor ;
    $user = Auth::user();
@endphp
<div class="card h-100">

    <img class="pt-1" width="100px" height="100px" src="{{ (isset($profesor->logo)) ? asset($profesor->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

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
                <dt>Nombre</dt>
                {{$profesor->fullname ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>CI</dt>
                {{$profesor->ci_profesor ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Tipo de facilitador</dt>
                {{$profesor->ti_teacher ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Fecha de nacimiento</dt>
                {{(!empty($profesor->date_birth)) ? f_date($profesor->date_birth) : ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Lugar de nacimiento</dt>
                {{$profesor->city_birth ?? ''}}
                {{$profesor->town_hall_birth ?? ''}}
                {{$profesor->state_birth ?? ''}}
                {{$profesor->country_birth ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Dirección de residencia</dt>
                {{$profesor->dir_address ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Números de teléfono</dt>
                {{$profesor->phone ?? ''}}
                {{$profesor->cellphone ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Correo electrónico</dt>
                {{$profesor->email ?? ''}}
            </ol>
        </div>

    </div>

</div>
{{--
CREATE TABLE `profesors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ti_teacher` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de facilitador',
  `ci_profesor` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cédula de identidad, Id temporal o pasaporte',
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombres',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombres',
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Género',
  `date_birth` date DEFAULT NULL COMMENT 'Fecha de nacimiento',
  `city_birth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lugar de nacimiento',
  `town_hall_birth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Municipio de nacimiento',
  `state_birth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Estado de nacimiento',
  `country_birth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'País de nacimiento',
  `dir_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dirección de residencia',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de teléfono fijo',
  `cellphone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de teléfono celular',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Correo electrónico',
  `status_active` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Estado del Banco',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; --}}
