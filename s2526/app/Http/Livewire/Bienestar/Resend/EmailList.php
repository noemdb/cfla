<?php

namespace App\Http\Livewire\Bienestar\Resend;

use App\Models\ResendEmail;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class EmailList extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedEmail = null;
    public $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => '']
    ];

    protected $listeners = [
        'refreshEmails' => '$refresh',
        'showModal' => 'showEmailDetails',
        'hideModal' => 'closeModal'
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function showEmailDetails($emailId)
    {
        $this->selectedEmail = ResendEmail::find($emailId);
        $this->showModal = true;
        $this->emit('modal:show');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedEmail = null;
        $this->emit('modal:hide');
    }

    public function render()
    {
        $query = ResendEmail::query()
            ->where('subject', 'like', '%Matrícula Escolar%')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('to', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc');

        $emails = $query->paginate(10);

        return view('livewire.bienestar.resend.email-list', [
            'emails' => $emails
        ]);
    }
}
