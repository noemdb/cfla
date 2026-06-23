<?php

namespace App\Http\Livewire\Administracion\Representant;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Estudiante\Ingreso; // Agregar el modelo Ingreso
use Carbon\Carbon;

class TimelineComponent extends Component
{
    use WithPagination;

    public $representant_id;
    public $representant;
    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $selectedPayment;
    public $selectedIngreso; // Nueva propiedad para el ingreso seleccionado
    public $showModal = false;
    public $showIngresoModal = false; // Nueva propiedad para el modal de ingreso
    public $showCancelled = true;
    public $loading = false;

    protected $listeners = ['showPaymentDetail', 'showIngresoDetail']; // Agregar nuevo listener
    protected $paginationTheme = 'bootstrap';

    public function mount($representant_id = null)
    {
        $this->loading = true;
        $this->representant_id = $representant_id;
        if ($representant_id) {
            $this->representant = Representant::find($representant_id);
        }
        $this->dateFrom = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->loading = false;
    }

    public function updatedSearch()
    {
        $this->loading = true;
        $this->resetPage();
        $this->loading = false;
    }

    public function updatedDateFrom()
    {
        $this->loading = true;
        $this->resetPage();
        $this->loading = false;
    }

    public function updatedDateTo()
    {
        $this->loading = true;
        $this->resetPage();
        $this->loading = false;
    }

    public function updatedShowCancelled()
    {
        $this->loading = true;
        $this->resetPage();
        $this->loading = false;
    }

    public function selectRepresentant($id)
    {
        $this->loading = true;
        $this->representant_id = $id;
        $this->representant = Representant::find($id);
        $this->resetFilters();
        $this->resetPage();
        $this->loading = false;
    }

    public function resetFilters()
    {
        $this->loading = true;
        $this->dateFrom = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->resetPage();
        $this->loading = false;
    }

    public function showPaymentDetail($paymentId, $isCancelled = false)
    {
        $this->loading = true;

        if ($isCancelled) {
            $this->selectedPayment = RegistroPagoCombinado::withTrashed()->with([
                'registropagos' => function($query) {
                    $query->withTrashed()->with([
                        'cuentaxpagar',
                        'estudiant',
                        'pagos' => function($q) {
                            $q->withTrashed();
                        }
                    ]);
                },
                'representant'
            ])->find($paymentId);
        } else {
            $this->selectedPayment = RegistroPagoCombinado::with([
                'registropagos.cuentaxpagar',
                'registropagos.estudiant',
                'registropagos.pagos',
                'representant'
            ])->find($paymentId);
        }

        $this->showModal = true;
        $this->loading = false;
        $this->dispatchBrowserEvent('show-payment-modal');
    }

    // Nuevo método para mostrar detalle de ingreso
    public function showIngresoDetail($ingresoId)
    {
        $this->loading = true;

        $this->selectedIngreso = Ingreso::with([
            'metodo_pago',
            'banco',
            'estudiant',
            'representant',
            'registro_pago',
            'pago',
            'abono',
            'creditoafavor',
            'recurso',
            'exchange_rate'
        ])->find($ingresoId);

        $this->showIngresoModal = true;
        $this->loading = false;
        $this->dispatchBrowserEvent('show-ingreso-modal');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPayment = null;
        $this->dispatchBrowserEvent('hide-payment-modal');
    }

    // Nuevo método para cerrar modal de ingreso
    public function closeIngresoModal()
    {
        $this->showIngresoModal = false;
        $this->selectedIngreso = null;
        $this->dispatchBrowserEvent('hide-ingreso-modal');
    }

    public function getRepresentantsProperty()
    {
        if (empty($this->search)) {
            return collect();
        }

        return Representant::where(function($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('ci_representant', 'like', '%' . $this->search . '%');
        })
        ->limit(10)
        ->get();
    }

    // Nueva propiedad computed para obtener ingresos
    public function getIngresosProperty()
    {
        if (!$this->representant_id) {
            return collect();
        }

        $query = Ingreso::where('representant_id', $this->representant_id)
            ->with([
                'metodo_pago',
                'banco',
                'estudiant',
                'representant',
                'registro_pago',
                'pago',
                'abono',
                'creditoafavor'
            ]);

        // Incluir ingresos eliminados si está habilitado
        if ($this->showCancelled) {
            $query = $query->withTrashed();
        }

        // Aplicar filtros de fecha
        if ($this->dateFrom) {
            $query->whereDate('date_transaction', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('date_transaction', '<=', $this->dateTo);
        }

        return $query->orderBy('date_transaction', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'ingresos-page');
    }

    public function getPaymentsProperty()
    {
        if (!$this->representant_id) {
            return collect();
        }

        // Crear query base
        $query = RegistroPagoCombinado::where('representant_id', $this->representant_id);

        // Incluir anulados si está habilitado
        if ($this->showCancelled) {
            $query = $query->withTrashed();
        }

        // Aplicar filtros de fecha
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Cargar relaciones
        $query->with([
            'registropagos' => function($q) {
                if ($this->showCancelled) {
                    $q->withTrashed();
                }
                $q->with([
                    'cuentaxpagar',
                    'estudiant',
                    'pagos' => function($pq) {
                        if ($this->showCancelled) {
                            $pq->withTrashed();
                        }
                    }
                ]);
            }
        ]);

        // Ordenar y paginar
        return $query->orderBy('created_at', 'desc')->paginate(10, ['*'], 'payments-page');
    }

    public function render()
    {
        return view('livewire.administracion.representant.timeline-component');
    }
}
