<?php

namespace App\Observers;

use App\Models\app\Admon\Payment;
use App\Services\Binnacle;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        Binnacle::logModelEvent($payment, 'model_created', [
            'title' => 'Pago registrado',
            'description' => "Se registró un pago (tipo: {$payment->type_pay})",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function updated(Payment $payment): void
    {
        if (! $payment->isDirty()) {
            return;
        }

        Binnacle::logModelEvent($payment, 'model_updated', [
            'title' => 'Pago actualizado',
            'description' => "Se actualizó un pago (tipo: {$payment->type_pay})",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function deleted(Payment $payment): void
    {
        Binnacle::logModelEvent($payment, 'model_deleted', [
            'title' => 'Pago eliminado',
            'description' => "Se eliminó un pago (tipo: {$payment->type_pay})",
            'category' => 'user_action',
            'severity' => 'warning',
        ]);
    }
}
