<?php

namespace App\Http\Livewire\ActivityLogs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ResendEmail;
use Carbon\Carbon;

class ResendEmails extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-5';

    public $start_date;
    public $end_date;
    public $status = '';
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $showDetails = false;
    public $selectedEmail = null;

    protected $queryString = [
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
        'status' => ['except' => ''],
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'refreshResendEmailsData' => '$refresh'
    ];

    public function mount()
    {
        $this->start_date = now()->subDays(7)->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function showDetails($emailId)
    {
        $this->selectedEmail = ResendEmail::find($emailId);
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedEmail = null;
    }

    public function getStatusClass($status)
    {
        return match (strtolower($status)) {
            'delivered' => 'success',
            'sent' => 'info',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    public function render()
    {
        $query = ResendEmail::query()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('created_at', '<=', $this->end_date);
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('to', 'like', '%' . $this->search . '%')
                        ->orWhere('from', 'like', '%' . $this->search . '%');
                });
            });

        $emails = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);

        return view('livewire.activity-logs.resend-emails', [
            'emails' => $emails,
            'statuses' => [
                'pending' => 'Pendiente',
                'sent' => 'Enviado',
                'delivered' => 'Entregado',
                'failed' => 'Fallido'
            ]
        ]);
    }
}
