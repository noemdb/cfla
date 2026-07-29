<?php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public ?array $profileData = null;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->profileData = $service->getInscripcionData();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.profile')
            ->layout('student.layouts.app');
    }
}
