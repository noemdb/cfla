<?php

namespace App\Livewire\Student\Lms\Concerns;

use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;

trait HasStudentScope
{
    protected StudentScopeService $studentService;

    public function initializeHasStudentScope(): void
    {
        $this->studentService = app(StudentScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getStudentService(): StudentScopeService
    {
        return $this->studentService;
    }
}
