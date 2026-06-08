<?php
// app/Http/Livewire/Administracion/Users/UserTrait.php

namespace App\Http\Livewire\Administracion\Users;

trait UserTrait
{
    protected $rules = [
        'user.username' => 'required|string',
        'user.password' => 'nullable',
        'user.email' => 'required|email',
        'user.is_active' => 'nullable',
        'user.status_update' => 'nullable',
        'user.work_id' => 'nullable',
        'user.card_id' => 'nullable',
        'user.ident' => 'nullable',
        'user.number_id' => 'nullable',
        'user.is_diagnostic' => 'nullable|boolean',
        
        'profile.firstname' => 'required|string',
        'profile.lastname' => 'required|string',
        'profile.card_number' => 'nullable|string',
        
        'rol.area' => 'required|string',
        'rol.rol' => 'required|string',
        'rol.cargo_id' => 'nullable|exists:cargos,id',
        'rol.assit_schedule_id' => 'nullable|exists:assit_schedules,id',
        'rol.finicial' => 'required|date',
        'rol.ffinal' => 'required|date',
        'rol.group' => 'nullable|string',
        'rol.status_schedule' => 'nullable|boolean',
    ];

    protected function validationAttributes()
    {
        return [
            'user.username' => $this->list_comment['username'] ?? 'Usuario',
            'user.password' => $this->list_comment['password'] ?? 'Contraseña',
            'user.email' => $this->list_comment['email'] ?? 'Email',
            'user.is_active' => $this->list_comment['is_active'] ?? 'Estado',
            'user.status_update' => $this->list_comment['status_update'] ?? 'Actualización de estado',
            'user.work_id' => $this->list_comment['work_id'] ?? 'ID de trabajo',
            'user.card_id' => $this->list_comment['card_id'] ?? 'ID de tarjeta',
            'user.ident' => $this->list_comment['ident'] ?? 'Identificación',
            'user.number_id' => $this->list_comment['number_id'] ?? 'Número de ID',
            'user.is_diagnostic' => $this->list_comment['is_diagnostic'] ?? 'Diagnostico',

            'profile.firstname' => $this->list_comment_profile['firstname'] ?? 'Nombres',
            'profile.lastname' => $this->list_comment_profile['lastname'] ?? 'Apellidos',
            'profile.card_number' => $this->list_comment_profile['card_number'] ?? 'Cédula',

            'rol.area' => $this->list_comment_rol['area'] ?? 'Área',
            'rol.rol' => $this->list_comment_rol['rol'] ?? 'Rol',
            'rol.cargo_id' => $this->list_comment_rol['cargo_id'] ?? 'Cargo',
            'rol.finicial' => $this->list_comment_rol['finicial'] ?? 'Fecha inicial',
            'rol.ffinal' => $this->list_comment_rol['ffinal'] ?? 'Fecha final',
            'rol.group' => $this->list_comment_rol['group'] ?? 'Grupo',
            'rol.status_schedule' => $this->list_comment_rol['status_schedule'] ?? 'Estado de horario',
            'rol.assit_schedule_id' => $this->list_comment_rol['assit_schedule_id'] ?? 'Horario de asistencia',
        ];
    }
}