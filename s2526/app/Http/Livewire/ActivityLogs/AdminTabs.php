<?php

namespace App\Http\Livewire\ActivityLogs;

use Livewire\Component;

class AdminTabs extends Component
{
    public $activeTab = 'dashboard';

    protected $listeners = [
        'refreshDashboard' => '$refresh',
        'refreshTable' => '$refresh',
        'refreshSystemLogs' => '$refresh',
        'refreshResendEmails' => '$refresh'
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        // Emitir eventos específicos según el tab activo
        switch ($tab) {
            case 'dashboard':
                $this->emit('refreshDashboardData');
                break;
            case 'details':
                $this->emit('refreshTableData');
                break;
            case 'system-logs':
                $this->emit('refreshSystemLogsData');
                break;
            case 'resend-emails':
                $this->emit('refreshResendEmailsData');
                break;
        }
    }

    public function render()
    {
        return view('livewire.activity-logs.admin-tabs');
    }
}
