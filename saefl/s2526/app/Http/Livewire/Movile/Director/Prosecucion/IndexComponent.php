<?php

namespace App\Http\Livewire\Movile\Director\Prosecucion;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class IndexComponent extends Component
{
    public $timezone = 'America/Caracas';
    public $totalStudents;
    public $confirmedStudents;
    public $pendingStudents;
    public $confirmationPercentage;
    public $pestudioTotals;
    public $gradoTotals;
    public $seccionTotals;
    public $genderTotals;
    public $arr;
    public $dailyConfirmations;
    public $weeklyConfirmations;
    public $monthlyConfirmations;
    public $confirmationTrend;
    public $selectedPeriod = 'daily';
    public $hourlyConfirmations;
    public $selectedDateRange = 'today';

    public function mount()
    {
        $this->loadIndicators();
        $this->arr = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark'];
    }

    public function loadIndicators()
    {
        // Obtener totales generales
        $this->totalStudents = $this->getTotalStudents();
        $this->confirmedStudents = $this->getConfirmedStudents();
        $this->pendingStudents = $this->totalStudents - $this->confirmedStudents;
        $this->confirmationPercentage = $this->totalStudents > 0
            ? round(($this->confirmedStudents / $this->totalStudents) * 100, 2)
            : 0;

        // Obtener totales por categorías
        $this->pestudioTotals = $this->getPestudioTotals();
        $this->gradoTotals = $this->getGradoTotals();
        $this->seccionTotals = $this->getSeccionTotals();
        $this->genderTotals = $this->getGenderTotals();

        // Obtener confirmaciones por período
        $this->dailyConfirmations = $this->getDailyConfirmations();
        $this->weeklyConfirmations = $this->getWeeklyConfirmations();
        $this->monthlyConfirmations = $this->getMonthlyConfirmations();
        $this->confirmationTrend = $this->getConfirmationTrend();
        $this->hourlyConfirmations = $this->getHourlyConfirmations();
        //dd($this->hourlyConfirmations);
    }

    // Mantener métodos existentes hasta getMonthlyConfirmations...
    private function getTotalStudents()
    {
        return Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->count();
    }

    private function getConfirmedStudents()
    {
        return Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('estudiants.status_prosecution', true)
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->count();
    }

    private function getPestudioTotals()
    {
        $totals = Pestudio::select(
            'pestudios.id',
            'pestudios.name',
            'pestudios.code',
            DB::raw('COUNT(CASE WHEN estudiants.status_prosecution = 1 THEN 1 END) as confirmed'),
            DB::raw('COUNT(estudiants.id) as total')
        )
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('pestudios.id', 'pestudios.name', 'pestudios.code')
            ->orderBy('pestudios.id')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->get();

        return $totals->map(function ($item) {
            $item->pending = $item->total - $item->confirmed;
            $item->percentage = $item->total > 0 ? round(($item->confirmed / $item->total) * 100, 2) : 0;
            return $item;
        });
    }

    private function getGradoTotals()
    {
        $totals = Grado::select(
            'grados.id',
            'grados.name',
            'pestudios.name as pestudio_name',
            DB::raw('COUNT(CASE WHEN estudiants.status_prosecution = 1 THEN 1 END) as confirmed'),
            DB::raw('COUNT(estudiants.id) as total')
        )
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('grados.id', 'grados.name', 'pestudios.name')
            ->orderBy('grados.id')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->get();

        return $totals->map(function ($item) {
            $item->pending = $item->total - $item->confirmed;
            $item->percentage = $item->total > 0 ? round(($item->confirmed / $item->total) * 100, 2) : 0;
            return $item;
        });
    }

    private function getSeccionTotals()
    {
        $totals = DB::table('seccions')
            ->select(
                'seccions.id',
                'seccions.name as seccion_name',
                'grados.name as grado_name',
                'pestudios.name as pestudio_name',
                DB::raw('COUNT(CASE WHEN estudiants.status_prosecution = 1 THEN 1 END) as confirmed'),
                DB::raw('COUNT(estudiants.id) as total')
            )
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->groupBy('seccions.id', 'seccions.name', 'grados.name', 'pestudios.name')
            ->orderBy('pestudios.id')
            ->orderBy('grados.id')
            ->orderBy('seccions.name')
            ->get()
            ->map(function ($item) {
                $item = (object) $item;
                $item->pending = $item->total - $item->confirmed;
                $item->percentage = $item->total > 0 ? round(($item->confirmed / $item->total) * 100, 2) : 0;
                return $item;
            });

        return $totals;
    }

    private function getGenderTotals()
    {
        $totals = DB::table('estudiants')
            ->select(
                'estudiants.gender',
                DB::raw('COUNT(CASE WHEN estudiants.status_prosecution = 1 THEN 1 END) as confirmed'),
                DB::raw('COUNT(estudiants.id) as total')
            )
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('estudiants.gender')
            ->get()
            ->map(function ($item) {
                $item = (object) $item;
                $item->pending = $item->total - $item->confirmed;
                $item->percentage = $item->total > 0 ? round(($item->confirmed / $item->total) * 100, 2) : 0;
                return $item;
            });

        return $totals;
    }

    private function getDailyConfirmations()
    {
        return DB::table('estudiants')
            ->select(
                DB::raw('DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->where('estudiants.date_prosecution', '>=', now()->subDays(30))
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->groupBy(DB::raw('DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))'))
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    }

    private function getWeeklyConfirmations()
    {
        return DB::table('estudiants')
            ->select(
                DB::raw('YEARWEEK(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as week'),
                DB::raw('MIN(DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))) as week_start'),
                DB::raw('MAX(DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))) as week_end'),
                DB::raw('COUNT(*) as count')
            )
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->where('estudiants.date_prosecution', '>=', now()->subWeeks(12))
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->groupBy(DB::raw('YEARWEEK(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))'))
            ->orderBy('week', 'asc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    }

    private function getMonthlyConfirmations()
    {
        return DB::table('estudiants')
            ->select(
                DB::raw('YEAR(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as year'),
                DB::raw('MONTH(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->where('estudiants.date_prosecution', '>=', now()->subMonths(12))
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->groupBy(
                DB::raw('YEAR(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))'),
                DB::raw('MONTH(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))')
            )
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    }

    private function getHourlyConfirmations()
    {
        $dateFilter = $this->getDateFilterForHourly();

        // Consulta base con conversión de zona horaria correcta
        $query = DB::table('estudiants')
            ->select(
                DB::raw('DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as date'),
                DB::raw('HOUR(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00")) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            // ->where('estudiants.date_prosecution', '>=', $dateFilter)
            ;

        // Filtro adicional para "hoy" - limitar hasta la hora actual
        if ($this->selectedDateRange === 'today') {
            $nowInCaracas = now($this->timezone);
            $query->where('estudiants.date_prosecution', '<=', $nowInCaracas->utc());
        }

        return $query
            ->groupBy(
                DB::raw('DATE(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))'),
                DB::raw('HOUR(CONVERT_TZ(estudiants.date_prosecution, "+00:00", "-04:00"))')
            )
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'date' => $item->date,
                    'hour' => (int) $item->hour,
                    'count' => (int) $item->count,
                    'datetime' => $item->date . ' ' . str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00:00',
                    'formatted_datetime' => Carbon::parse($item->date . ' ' . str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00:00')
                        ->format('d-m H:i')
                ];
            });
    }

    private function getDateFilterForHourly()
    {
        switch ($this->selectedDateRange) {
            case 'today':
                return now($this->timezone)->startOfDay()->utc();
            case 'week':
                return now($this->timezone)->subWeek()->utc();
            case 'month':
                return now($this->timezone)->subMonth()->utc();
            default:
                return now($this->timezone)->startOfDay()->utc();
        }
    }

    private function getCurrentHourLimit()
    {
        switch ($this->selectedDateRange) {
            case 'today':
                return now($this->timezone)->hour;
            case 'week':
            case 'month':
                return 23; // Para períodos más largos, mostrar todas las horas
            default:
                return now($this->timezone)->hour;
        }
    }

    /**
     * CONSULTA CORREGIDA PARA TENDENCIA DE CONFIRMACIONES
     */
    private function getConfirmationTrend()
    {
        $today = now($this->timezone);
        $lastWeek = $today->copy()->subWeek();
        $twoWeeksAgo = $today->copy()->subWeeks(2);

        // Confirmaciones de esta semana
        $thisWeek = DB::table('estudiants')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            // ->where('estudiants.date_prosecution', '>=', $lastWeek->utc())
            // ->where('estudiants.date_prosecution', '<=', $today->utc())
            ->whereNotIn('seccions.id', ['21','22','35','46','75','76','77','78']) // se excluye ultimo grado
            ->count();

        // Confirmaciones de la semana anterior
        $previousWeek = DB::table('estudiants')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('estudiants.status_prosecution', 1)
            ->where('estudiants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->whereNotNull('estudiants.date_prosecution')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->where('estudiants.date_prosecution', '>=', $twoWeeksAgo->utc())
            // ->where('estudiants.date_prosecution', '<', $lastWeek->utc())
            ->count();

        return [
            'this_week' => $thisWeek,
            'previous_week' => $previousWeek,
            'trend' => $previousWeek > 0 ? round((($thisWeek - $previousWeek) / $previousWeek) * 100, 2) : 0
        ];
    }

    public function changePeriod($period)
    {
        $this->selectedPeriod = $period;
    }

    public function changeDateRange($range)
    {
        $this->selectedDateRange = $range;
        $this->hourlyConfirmations = $this->getHourlyConfirmations();

        $hourLimit = $this->getCurrentHourLimit();
        $currentTimeInfo = [
            'hour_limit' => $hourLimit,
            'timezone' => $this->timezone,
            'current_time' => now($this->timezone)->format('H:i'),
            'selected_range' => $this->selectedDateRange
        ];

        $this->emit('hourlyConfirmationsUpdated', $this->hourlyConfirmations, $currentTimeInfo);
    }

    public function refreshData()
    {
        $this->loadIndicators();

        $hourLimit = $this->getCurrentHourLimit();
        $currentTimeInfo = [
            'hour_limit' => $hourLimit,
            'timezone' => $this->timezone,
            'current_time' => now($this->timezone)->format('H:i'),
            'selected_range' => $this->selectedDateRange
        ];

        $this->emit('dataRefreshed');
        $this->emit('hourlyConfirmationsUpdated', $this->hourlyConfirmations, $currentTimeInfo);
    }

    public function render()
    {
        return view('livewire.movile.director.prosecucion.index-component');
    }
}
