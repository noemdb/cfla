<?php

namespace App\Http\Livewire\General\Instrument;

use Livewire\Component;

class DiagnostigComponent extends Component
{
    public $mode;

    public function mount()
    {
        $this->mode="initial";
    }

    public function render()
    {
        return view('livewire.general.instrument.diagnostig-component');
    }

    public function next()
    {
        switch ($this->mode) {
            case 'initial': $this->mode="resume"; break;
            case 'resume': $this->mode="basicTerms"; break;
            case 'basicTerms': $this->mode="structures"; break;
            case 'structures': $this->mode="comprende"; break;
            case 'comprende': $this->mode="applies"; break;
            case 'applies': $this->mode="analyzes"; break;
            case 'analyzes': $this->mode="evaluates"; break;
            case 'evaluates': $this->mode="create"; break;
            case 'create': $this->mode="final"; break;
            case 'final': $this->mode="initial"; break;
            
            default: $this->mode="initial"; break;
        }
        // dd($this->mode);
    }

    public function previous()
    {
        switch ($this->mode) {
            case 'initial': $this->mode="initial"; break;
            case 'resume': $this->mode="initial"; break;
            case 'basicTerms': $this->mode="resume"; break;
            case 'structures': $this->mode="basicTerms"; break;
            case 'comprende': $this->mode="structures"; break;
            case 'applies': $this->mode="comprende"; break;
            case 'analyzes': $this->mode="applies"; break;
            case 'evaluates': $this->mode="analyzes"; break;
            case 'create': $this->mode="evaluates"; break;
            case 'final': $this->mode="create"; break;
            
            default: $this->mode="initial"; break;
        }
        // dd($this->mode);
    }
}
