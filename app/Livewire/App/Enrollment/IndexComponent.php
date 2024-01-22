<?php

namespace App\Livewire\App\Enrollment;

use App\Models\app\Academy\Census;
use App\Models\app\Academy\Enrollment;
use App\Models\app\Blog\Post;
use App\Models\app\Learner\Representant;
use Livewire\Component;

class IndexComponent extends Component
{
    use RulesTrait;
    public Enrollment $enrollment;
    public Census $census;

    public $ci;
    public $step = 0, $limit = 6;
    public $modalAssistent,$simpleModal;
    public $list_comment;
    public $ci_estudiant;

    public function mount($enrollment = null)
    {
        $this->enrollment = $enrollment;
        $this->census = new Census();

        $this->ci = '14608133';
        $this->ci = '32446229';

        $this->list_comment = Enrollment::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.app.enrollment.index-component');
    }

    public function search()
    {
        $census = Census::where('ci_estudiant', $this->ci)->where('status_admite','true')->first();
        if ($census) {
            $this->census = $census;
            $this->step = 1;
            $this->enrollment->fill($census->toArray()); //dd($this->enrollment);
        }        
        $this->modalAssistent = true;
    }

    public function next($step)
    {
        switch ($step) {
            case '1':
                $this->validateOnly('enrollment.ci_estudiant');
                break;
        }
    }
}
