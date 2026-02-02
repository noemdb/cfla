<?php

namespace App\Livewire\App\Enrollment;

use App\Models\app\Learner\Representant;
use Livewire\Component;

class MainComponent extends Component
{

    public $modalAssistent, $simpleModal, $modalSearch, $modalStart, $modalEmpty;
    public $ci;
    public $currentStep = 1;
    public $representant = null;
    public $error_message = null;

    public function render()
    {
        return view('livewire.app.enrollment.main-component');
    }

    public function setStep($step)
    {
        $this->currentStep = $step;
    }

    public function setStart()
    {
        $this->currentStep = 2;
    }

    public function restart()
    {
        $this->currentStep = 1;
        $this->ci = null;
        $this->representant = null;
        $this->error_message = null;
    }

    public function search()
    {
        $this->validate([
            'ci' => 'required|numeric'
        ]);

        $this->representant = Representant::where('ci_representant', $this->ci)->first();

        if (!$this->representant) {
            $this->error_message = 'Representante no encontrado en el sistema.';
            $this->representant = null;
        } else {
            $this->error_message = null;
        }
    }

    public function goToSaefl()
    {
        $url = env('APP_URL_SAEFL', '.') . '/general/enrollments/index/' . $this->ci;
        return $this->redirect($url);
    }
}
