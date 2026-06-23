<?php

namespace App\Http\Livewire\Administracion\Tab\Resend;

use App\Models\ResendEmail;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class EmailList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedEmail = null;
    public $showModal = false;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1]
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

    public function updatingPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';

        $this->sortField = $field;
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
            ->where(function ($query) {
                $query->where('subject', 'like', '%Notificación de Cobro%')
                    ->orWhere('subject', 'like', '%Notificaciones SAEFL%');
            })
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
            });

        // Aplicar ordenación
        if ($this->sortField === 'created_at') {
            $query->orderBy('created_at', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $emails = $query->paginate(10);

        return view('livewire.administracion.tab.resend.email-list', [
            'emails' => $emails
        ]);
    }
}
