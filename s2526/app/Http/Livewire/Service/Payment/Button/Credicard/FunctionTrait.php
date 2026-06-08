<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

use App\Models\app\Planpago\ExchangeRate;

trait FunctionTrait
{
    public function goHome()
    {
        $this->mount();
    }

    public function setExchangeRateAmmount()
    {
        $this->exchange_rate_ammount = ExchangeRate::getAmmountExchange();
    }

    public function fullHolderId()
    {
        return $this->holder_id_doc.str_pad($this->holder_id, 9, "0", STR_PAD_LEFT);
    }
}

?>
