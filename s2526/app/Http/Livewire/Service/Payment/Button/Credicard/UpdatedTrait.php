<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php enable_ammount_pay

trait UpdatedTrait
{
	public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedAmmountPay($value)
    {
        $ammount_pay_exchange = ($this->exchange_rate_ammount) ? $this->ammount_pay / $this->exchange_rate_ammount : null; //dd($ammount_pay_exchange);
        // $ammount_pay_exchange = round($ammount_pay_exchange,2);
        // dd($ammount_pay_exchange,numberFormat($ammount_pay_exchange,2,'.',''));
        $ammount_pay_exchange = numberFormat($ammount_pay_exchange,2,'.','');
        $this->ammount_pay_exchange = $ammount_pay_exchange; //dd($ammount_pay_exchange);

    }

    public function updatedCardType($value)
    {
        $this->card_type_payment = ($value=="TDC") ? "CREDIT" : "DEBIT";
        $this->card_type_holder = ($value=="TDC") ? "TDC" : "TDD";
    }

    public function updatedDateExpiration($value)
    {
        $this->expiration_month = substr($this->date_expiration,0,2);
        $this->expiration_year = substr($this->date_expiration,3,2);
    }
    public function updatedTotalPay ()
    {
        $this->ammount_pay = ($this->totalPay) ? $this->ammount : null;
        $this->ammount_pay = ($this->ammount_pay) ? number_format($this->ammount_pay,2,'.','') : null;

        $ammount_pay_exchange = ($this->exchange_rate_ammount) ? $this->ammount_pay / $this->exchange_rate_ammount : null; //exchange_rate_ammount
        // $ammount_pay_exchange = round($ammount_pay_exchange,2);
        // $ammount_pay_exchange = number_format($ammount_pay_exchange,2,'.','');
        $ammount_pay_exchange = numberFormat($ammount_pay_exchange,2,'.','');
        $this->ammount_pay_exchange = $ammount_pay_exchange;
    }

    public function updatedHolderId($value)
    {
        $this->loadlderIdCon();
    }

    public function updatedHolderType($value)
    {
        $this->loadlderIdCon();
    }

    public function loadlderIdCon()
    {
        $this->holder_id_con = $this->holder_type. str_pad($this->holder_id, 9, "0", STR_PAD_LEFT); //dd($this->holder_id_con);
    }

    public function enableAmmountPay ()
    {
        $this->ammount_pay = null;
        $this->totalPay = false;
    }

    public function setAmmountPayUnexpired ()
    {
        $this->ammount_pay = number_format($this->ammount_unexpired,2,'.','');
        $this->totalPay = false;
        $this->validateOnly("ammount_pay");
    }

    public function setCardPinType ()
    {
        $this->card_pin_type = ($this->card_pin_type == "password") ? "text" : "password" ;
    }

    public function setCVCType ()
    {
        $this->cvc_type = ($this->cvc_type == "password") ? "text" : "password" ;
    }
}

?>
