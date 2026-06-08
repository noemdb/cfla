<?php

namespace App\Http\Livewire\Administracion\Home;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Profesor;

class Dashboard extends Component
{
    // Propiedades públicas disponibles en la vista Livewire
    public $estudiants;
    public $pestudios;
    public $indicadores;
    public $plan_beneficos;
    public $retiros;
    public $profesors;
    public $pescolar;
    public $date_start;
    public $date_end;

    public function mount()
    {
        // === LÓGICA QUE ESTABA EN HomeController@dashboard ===

        $this->estudiants = Estudiant::select('estudiants.*')
            ->active('true')
            ->WidthInscripcion()
            ->get();

        $this->pestudios = Pestudio::orderBy('id', 'asc')
            ->where('status_active', 'true')
            ->get();

        // Indicadores generales (estructura usada en los parciales)
        $this->indicadores = Pestudio::getIndicadores();

        $now = Carbon::now()->format('Y-m-d');

        $this->plan_beneficos = PlanBenefico::where('created_at', '<=', $now)
            ->where('ffinal', '>=', $now)
            ->get();

        $this->retiros = Retiro::getEstudiants();

        $this->profesors = Profesor::asignado('true')->get();

        $this->pescolar = Pescolar::orderBy('created_at', 'DESC')->first();

        if ($this->pescolar) {
            $this->date_start = Date::createFromFormat('Y-m-d', $this->pescolar->finicial);
            $this->date_end   = Date::createFromFormat('Y-m-d', $this->pescolar->ffinal);
        }
    }

    public function render()
    {
        return view('livewire.administracion.home.dashboard');
    }
}
