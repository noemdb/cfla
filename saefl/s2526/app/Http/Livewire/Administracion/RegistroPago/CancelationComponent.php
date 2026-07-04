<?php

namespace App\Http\Livewire\Administracion\RegistroPago;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Estudiant;

class CancelationComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    // Search and Filter Properties
    public $search = '';
    public $finicial = '';
    public $ffinal = '';
    public $bco_finicial = '';
    public $bco_ffinal = '';
    public $number_i_pay = '';
    public $status_unexpired = '';
    public $is_adjustment = '';
    public $status_inscription_affects = '';
    public $cancellation_status = '';

    // Modal Properties
    public $showDetailsModal = false;
    public $selectedPayment = null;

    // Justification Modal Properties
    public $showJustificationModal = false;
    public $justificationPaymentId = null;
    public $justificationText = '';
    public $justificationStudentName = '';
    public $justificationConceptName = '';

    // Pagination
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'finicial' => ['except' => ''],
        'ffinal' => ['except' => ''],
        'cancellation_status' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'paymentMarkedCancellable' => 'handlePaymentMarkedCancellable',
        'paymentCancellableRemoved' => 'handlePaymentCancellableRemoved',
    ];

    protected $rules = [
        'justificationText' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'justificationText.required' => 'La justificación es obligatoria.',
        'justificationText.min' => 'La justificación debe tener al menos 10 caracteres.',
        'justificationText.max' => 'La justificación no puede exceder 500 caracteres.',
    ];

    public function mount()
    {
        $this->finicial = now()->startOfMonth()->format('Y-m-d');
        $this->ffinal = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCancellationStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'finicial',
            'ffinal',
            'bco_finicial',
            'bco_ffinal',
            'number_i_pay',
            'status_unexpired',
            'is_adjustment',
            'status_inscription_affects',
            'cancellation_status'
        ]);
        $this->finicial = now()->startOfMonth()->format('Y-m-d');
        $this->ffinal = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function getRegistroPagosProperty()
    {
        $query = RegistroPago::withTrashed()
            ->select('registro_pagos.*')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->leftjoin('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->leftjoin('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->with(['estudiant.representant', 'cuentaxpagar', 'user', 'pagos', 'creditoafavor'])
            ->where('estudiants.status_active', 'true')
            ->orderBy('registro_pagos.created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('estudiants.name', 'like', '%' . $this->search . '%')
                    ->orWhere('estudiants.lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('estudiants.ci_estudiant', 'like', '%' . $this->search . '%')
                    ->orWhere('representants.ci_representant', 'like', '%' . $this->search . '%')
                    ->orWhere('representants.name', 'like', '%' . $this->search . '%')
                    ->orWhere('cuentaxpagars.name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->finicial) {
            $query->where('registro_pagos.created_at', '>=', $this->finicial);
        }

        if ($this->ffinal) {
            $query->where('registro_pagos.created_at', '<=', $this->ffinal . ' 23:59:59');
        }

        if ($this->status_unexpired !== '') {
            $query->where('registro_pagos.status_unexpired', $this->status_unexpired === 'true');
        }

        if ($this->cancellation_status) {
            switch ($this->cancellation_status) {
                case 'active':
                    $query->whereNull('registro_pagos.cancelled_at')
                        ->where('registro_pagos.cancellable', false);
                    break;
                case 'cancellable':
                    $query->whereNull('registro_pagos.cancelled_at')
                        ->where('registro_pagos.cancellable', true);
                    break;
                case 'cancelled':
                    $query->whereNotNull('registro_pagos.cancelled_at')
                        ->whereNotNull('registro_pagos.approval_date');
                    break;
                case 'pending_approval':
                    $query->whereNotNull('registro_pagos.cancelled_at')
                        ->whereNull('registro_pagos.approval_date');
                    break;
            }
        }

        if ($this->number_i_pay || $this->bco_finicial || $this->bco_ffinal) {
            $query->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id');
            if ($this->number_i_pay) {
                $query->where('ingresos.number_i_pay', 'like', '%' . $this->number_i_pay . '%');
            }
            if ($this->bco_finicial) {
                $query->where('ingresos.date_payment', '>=', $this->bco_finicial);
            }
            if ($this->bco_ffinal) {
                $query->where('ingresos.date_payment', '<=', $this->bco_ffinal);
            }
        }

        return $query->paginate($this->perPage);
    }

    public function getTotalAmountProperty()
    {
        $registroPagos = $this->registroPagos;
        $total = 0;
        $totalExchange = 0;

        foreach ($registroPagos as $registro) {
            $total += $registro->pagos->sum('pagos_ammount');
            $totalExchange += $registro->pagos->sum('exchange_ammount');
        }

        return [
            'total' => $total,
            'total_exchange' => $totalExchange,
            'count' => $registroPagos->count()
        ];
    }

    public function showDetails($paymentId)
    {
        $this->selectedPayment = RegistroPago::withTrashed()
            ->with([
                'estudiant.representant',
                'cuentaxpagar',
                'user',
                'pagos',
                'creditoafavor'
            ])->find($paymentId);

        if ($this->selectedPayment) {
            $this->showDetailsModal = true;
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPayment = null;
    }

    public function setShowJustificationModal($paymentId, $studentName, $conceptName)
    {
        $this->justificationPaymentId = $paymentId;
        $this->justificationStudentName = $studentName;
        $this->justificationConceptName = $conceptName;
        $this->justificationText = '';
        $this->resetErrorBag(['justificationText']);
        $this->showJustificationModal = true;
    }

    public function closeJustificationModal()
    {
        $this->showJustificationModal = false;
        $this->justificationPaymentId = null;
        $this->justificationStudentName = '';
        $this->justificationConceptName = '';
        $this->justificationText = '';
        $this->resetErrorBag(['justificationText']);
    }

    public function markAsCancellable()
    {
        $this->validate([
            'justificationText' => 'required|string|min:10|max:500',
        ]);

        $this->dispatchBrowserEvent('show-loading');

        $payment = RegistroPago::withTrashed()->find($this->justificationPaymentId);

        if (!$payment) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'Registro de pago no encontrado.'
            ]);
            return;
        }

        if ($payment->cancellable) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'warning',
                'message' => 'Este pago ya está marcado como anulable.'
            ]);
            return;
        }

        if ($payment->cancelled_at) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'Este pago ya ha sido anulado.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $payment->update([
                'cancellable' => true,
                'approval_user_id' => Auth::id(),
                'approval_date' => now(),
                'justification_annulment' => $this->justificationText,
            ]);

            // Log the action
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student' => ($payment->estudiant->name ?? '') . ' ' . ($payment->estudiant->lastname ?? ''),
                    'concept' => $payment->cuentaxpagar->name,
                    'justification' => $this->justificationText,
                ])
                ->log('payment_marked_as_cancellable');

            DB::commit();

            $this->closeJustificationModal();

            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'success',
                'message' => 'Pago marcado como anulable exitosamente.'
            ]);

            $this->emit('paymentMarkedCancellable', $this->justificationPaymentId);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'Error al marcar el pago como anulable: ' . $e->getMessage()
            ]);
        }
    }

    public function removeCancellable($paymentId)
    {
        $this->dispatchBrowserEvent('show-loading');

        $payment = RegistroPago::withTrashed()->find($paymentId);

        if (!$payment) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'Registro de pago no encontrado.'
            ]);
            return;
        }

        if (!$payment->cancellable) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'warning',
                'message' => 'Este pago no está marcado como anulable.'
            ]);
            return;
        }

        if ($payment->cancelled_at) {
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'No se puede quitar el estado anulable de un pago ya anulado.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $payment->update([
                'cancellable' => false,
                'approval_user_id' => null,
                'approval_date' => null,
                'justification_annulment' => null,
            ]);

            // Log the action
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student' => ($payment->estudiant->name ?? '') . ' ' . ($payment->estudiant->lastname ?? ''),
                    'concept' => $payment->cuentaxpagar->name,
                ])
                ->log('payment_cancellable_removed');

            DB::commit();

            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'success',
                'message' => 'Estado anulable removido exitosamente.'
            ]);

            $this->emit('paymentCancellableRemoved', $paymentId);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('hide-loading');
            $this->dispatchBrowserEvent('show-alert', [
                'type' => 'error',
                'message' => 'Error al remover el estado anulable: ' . $e->getMessage()
            ]);
        }
    }

    public function getPaymentStatus($payment)
    {
        if (! is_null($payment->deleted_at)) {
            // if ($payment->cancelled_at && $payment->approval_date) {
            return [
                'status' => 'cancelled',
                'class' => 'badge-danger',
                'icon' => 'fa-times-circle',
                'text' => 'Anulado'
            ];
        }

        if ($payment->cancelled_at && !$payment->approval_date) {
            return [
                'status' => 'pending_approval',
                'class' => 'badge-warning',
                'icon' => 'fa-clock',
                'text' => 'Pendiente Aprobación'
            ];
        }

        if ($payment->cancellable && !$payment->cancelled_at) {
            return [
                'status' => 'cancellable',
                'class' => 'badge-secondary',
                'icon' => 'fa-unlock',
                'text' => 'Anulable'
            ];
        }

        if ($payment->status_prepayment === 'true') {
            return [
                'status' => 'prepayment',
                'class' => 'badge-info',
                'icon' => 'fa-exclamation-circle',
                'text' => 'Pago Adelantado'
            ];
        }

        return [
            'status' => 'active',
            'class' => 'badge-success',
            'icon' => 'fa-check-circle',
            'text' => 'Activo'
        ];
    }

    public function handlePaymentMarkedCancellable($paymentId)
    {
        // Refresh the component when a payment is marked as cancellable
        $this->render();
    }

    public function handlePaymentCancellableRemoved($paymentId)
    {
        // Refresh the component when cancellable status is removed
        $this->render();
    }

    public function render()
    {
        return view('livewire.administracion.registro-pago.cancelation-component', [
            'registroPagos' => $this->registroPagos,
            'totalAmount' => $this->totalAmount,
        ]);
    }
}
