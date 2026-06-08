<?php

namespace App\Http\Livewire\Profesor\Users;

trait UserTrait
{
    protected $rules = [
        'user.username'=>'required|string',
        'user.password'=>'nullable|string',
        'user.email'=>'required|email',
    ];

    protected function validationAttributes()
    {
        return [
            'user.username' => $this->list_comment['username'],
            'user.password' => $this->list_comment['password'],
            'user.email' => $this->list_comment['email'],
        ];
    }
}

?>
