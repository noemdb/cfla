<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;

use Carbon\Carbon;

use App\Models\app\Estudiante\Representant;

trait StepperTrait
{
    public function goConnecting($step) // set date_expires, access_token, bank_code
    {
        $this->step = $step;

        $client = $this->getClient(); //dd($client);

        if (empty($this->access_token)) {
            $this->getTokenAccess($client);
        }

        $this->getCardInfo(); //dd($this);

        $this->getCardHolderCommissionService();

        $this->modeConnected = true;
        $this->modeAssistent = false;
    }

    public function goTabRepresentant($step)
    {
        if ($this->setRepresentant($this->ci_representant)) {
            $this->step = $step;
        } else {
            $this->step = 1;
            session()->flash('messenge', 'CI no encontrada!!! Intentelo nuevamente');
        }
    }

    public function setRepresentant($ci_representant)
    {
        $representant = Representant::where('ci_representant', $ci_representant)->first();
        if ($representant) {

            $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
            $exchange_ammount_unexpired_bill = $representant->exchange_ammount_unexpired_bill;
            $ammount = $this->exchange_rate_ammount * $exchange_ammount_expire_bill;
            $ammount_unexpired = $this->exchange_rate_ammount * $exchange_ammount_unexpired_bill;
            $exchange_expire_bill_pendientes = $representant->exchange_expire_bill_pendientes;
            $count_bills = ($this->exchange_rate_ammount > 0) ? $exchange_expire_bill_pendientes->count() : 0;


            $this->email_representant = $representant->email;
            $this->name_representant = $representant->name;
            $this->ci_representant = $representant->ci_representant;
            $this->fill_ci_representant = $this->type_ci . '-' . $this->ci_representant;

            $this->exchange_ammount_unexpired_bill = round($exchange_ammount_unexpired_bill, 2);
            $this->ammount_unexpired = round($ammount_unexpired, 2);

            $this->ammount = round($ammount, 2);
            $this->exchange_ammount_expire_bill = round($exchange_ammount_expire_bill, 2);
            $this->ammount_pay = ($this->totalPay) ? $this->ammount : $this->ammount_pay; //dd($this->ammount_pay);

            $ammount_pay_exchange = ($this->exchange_rate_ammount) ? $this->ammount_pay / $this->exchange_rate_ammount : null; //exchange_rate_ammount
            $ammount_pay_exchange = round($ammount_pay_exchange,2);
            $ammount_pay_exchange = number_format($ammount_pay_exchange,2,'.','');
            $this->ammount_pay_exchange = $ammount_pay_exchange;

            $this->ammount_pay = number_format($this->ammount_pay,2,'.','');  //if($this->ammount_pay=="0.00") dd($this->ammount_pay);
            $this->count_bills = $count_bills;

            $estudiants_formaly = $representant->estudiants_formaly;
            $this->status_estudiants_formaly = $estudiants_formaly->isNotEmpty();
            if (! $this->status_estudiants_formaly) {
                $this->validateOnly('status_estudiants_formaly');
            }
            return $representant;
        }
    }

    public function goTabSetAmmountPay($step)
    {
        $representant = $this->setRepresentant($this->ci_representant);
        if ($representant) {
            $this->step = $step;
        } else {
            $this->step = 1;
            session()->flash('messenge', 'Cédula del representante no encontrada!');
        }
    }

    public function goTabSetDataCard($step)
    {
        $this->step = $step;
        $this->modeAssistent = true;
        $this->modeConnected = false;
        $this->modeSendTokenBank = false;
        //dd($this->date_expiration);
    }

    public function goPayment($step)
    {
        $this->step = $step;
        $this->resetErrorBag();
        // $this->getCardHolderCommissionService();
    }
}
