<?php

namespace App\Http\Controllers\Bienestar;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }

    public function home(Request $request)
    {

        $representant_ci = (!empty($request->representant_ci)) ? $request->representant_ci : null;

        $user = Auth::user();

        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $now = Carbon::now()->format('Y-m-d');
        $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();

        $list_comment = User::COLUMN_COMMENTS;

        // Obtener indicadores de censos (Catchment)
        $catchment_indicators = $this->getCatchmentIndicators($representant_ci);

        // Obtener indicadores de entrevistas
        $interview_indicators = $this->getInterviewIndicators($representant_ci);

        return view('bienestars.home'
            ,compact('user','list_comment','estudiants','pestudios','plan_beneficos','representant_ci','catchment_indicators','interview_indicators')
        );
    }

    public function indicators()
    {
        $user = Auth::user();

        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $now = Carbon::now()->format('Y-m-d');
        $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();

        $list_comment = User::COLUMN_COMMENTS;

        return view('bienestars.indicators'
            ,compact('user','list_comment','estudiants','pestudios','plan_beneficos')
        );
    }

    private function getCatchmentIndicators($representant_ci = null)
    {
        $query = Catchment::query()->where('catchments.status_active', true);

        // Aplicar filtro por cédula si existe
        if ($representant_ci) {
            $query->where('representant_ci', 'like', '%' . $representant_ci . '%');
        }

        $baseQuery = clone $query;

        // Estadísticas generales
        $total_catchments = $baseQuery->count();
        $with_interviews = (clone $query)->whereHas('catchmentInterviews')->count();
        $without_interviews = $total_catchments - $with_interviews;
        $foreign_students = (clone $query)->where('status_foreign', true)->count();
        $with_siblings = (clone $query)->where('status_siblings_college', true)->count();
        $accepted_terms = (clone $query)->where('status_accept_terms', true)->count();

        // Estadísticas por género
        $by_gender = (clone $query)
            ->select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        // Estadísticas por grado usando el método existente
        $by_grade = (clone $query)
            ->select('grade', DB::raw('count(*) as total'))
            ->with('grado')
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->orderBy('total', 'desc')
            ->get();

        // Estadísticas por institución de origen (top 10)
        $by_origin = (clone $query)
            ->select('origin', DB::raw('count(*) as total'))
            ->whereNotNull('origin')
            ->where('origin', '!=', '')
            ->groupBy('origin')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        // Estadísticas por grupo de actividades
        $by_group = (clone $query)
            ->select('catchment_groups.name', DB::raw('count(catchments.id) as total'))
            ->join('catchment_groups', 'catchment_groups.id', '=', 'catchments.group_id')
            ->groupBy('catchment_groups.id', 'catchment_groups.name')
            ->orderBy('total', 'desc')
            ->get();

        // Estadísticas por día de cita
        $by_appointment_day = (clone $query)
            ->select('day_appointment', DB::raw('count(*) as total'))
            ->whereNotNull('day_appointment')
            ->groupBy('day_appointment')
            ->orderBy('day_appointment')
            ->get();

        // Estadísticas por nivel educativo del representante
        $by_educational_level = (clone $query)
            ->select('educational_level', DB::raw('count(*) as total'))
            ->whereNotNull('educational_level')
            ->where('educational_level', '!=', '')
            ->groupBy('educational_level')
            ->orderBy('total', 'desc')
            ->get();

        // Estadísticas por parentesco
        $by_relationship = (clone $query)
            ->select('relationship', DB::raw('count(*) as total'))
            ->whereNotNull('relationship')
            ->where('relationship', '!=', '')
            ->groupBy('relationship')
            ->orderBy('total', 'desc')
            ->get();

        // Estadísticas por mes (últimos 6 meses)
        $by_month = (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Estadísticas de edades (rangos)
        $age_ranges = [
            '3-5' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) BETWEEN 3 AND 5')->count(),
            '6-8' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) BETWEEN 6 AND 8')->count(),
            '9-11' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) BETWEEN 9 AND 11')->count(),
            '12-14' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) BETWEEN 12 AND 14')->count(),
            '15-17' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) BETWEEN 15 AND 17')->count(),
            '18+' => (clone $query)->whereRaw('TIMESTAMPDIFF(YEAR, date_birth, CURDATE()) >= 18')->count(),
        ];

        // Porcentajes
        $percentages = [
            'interview_rate' => $total_catchments > 0 ? round(($with_interviews / $total_catchments) * 100, 1) : 0,
            'foreign_rate' => $total_catchments > 0 ? round(($foreign_students / $total_catchments) * 100, 1) : 0,
            'siblings_rate' => $total_catchments > 0 ? round(($with_siblings / $total_catchments) * 100, 1) : 0,
            'terms_acceptance_rate' => $total_catchments > 0 ? round(($accepted_terms / $total_catchments) * 100, 1) : 0,
        ];

        return [
            'totals' => [
                'total_catchments' => $total_catchments,
                'with_interviews' => $with_interviews,
                'without_interviews' => $without_interviews,
                'foreign_students' => $foreign_students,
                'with_siblings' => $with_siblings,
                'accepted_terms' => $accepted_terms,
            ],
            'by_gender' => $by_gender,
            'by_grade' => $by_grade,
            'by_origin' => $by_origin,
            'by_group' => $by_group,
            'by_appointment_day' => $by_appointment_day,
            'by_educational_level' => $by_educational_level,
            'by_relationship' => $by_relationship,
            'by_month' => $by_month,
            'age_ranges' => $age_ranges,
            'percentages' => $percentages,
        ];
    }

    private function getInterviewIndicators($representant_ci = null)
    {
        $query = CatchmentInterview::query();

        // Aplicar filtro por cédula si existe
        if ($representant_ci) {
            $query->where('identification_number', 'like', '%' . $representant_ci . '%');
        }

        $baseQuery = clone $query;

        // Estadísticas generales
        $total_interviews = $baseQuery->count();
        $accepted = (clone $query)->where('accepted', true)->count();
        $standby = (clone $query)->where('status_standby', true)->count();
        $notified = (clone $query)->where('status_notified', true)->count();
        $pending = (clone $query)->where('accepted', false)->where('status_standby', false)->count();
        $rejected = (clone $query)->where('accepted', false)->where('status_standby', false)->whereNotNull('rating')->where('rating', '<', 3)->count();
        $with_siblings = (clone $query)->where('has_siblings', true)->count();
        $catholic = (clone $query)->where('agreement_to_catholic_formation', true)->count();

        // Estadísticas por grado
        $by_grade = (clone $query)
            ->select('grade_year_aspiring', DB::raw('count(*) as total'))
            ->with('grado')
            ->groupBy('grade_year_aspiring')
            ->orderBy('total', 'desc')
            ->get();

        // Estadísticas por calificación
        $by_rating = (clone $query)
            ->select('rating', DB::raw('count(*) as total'))
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // Estadísticas por mes (últimos 6 meses)
        $by_month = (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Estadísticas de capacidad de pago
        $payment_capacity = [
            'can_pay_dollars' => (clone $query)->where('able_to_pay_dollars', true)->count(),
            'can_pay_bolivars' => (clone $query)->where('able_to_pay_bolivars', true)->count(),
            'has_guarantor' => (clone $query)->where('has_payment_responsible', true)->count(),
        ];

        // Estadísticas religiosas
        $religious_stats = [
            'catholic_aware' => (clone $query)->where('awareness_of_catholic_school_affiliation', true)->count(),
            'agrees_catholic_formation' => (clone $query)->where('agreement_to_catholic_formation', true)->count(),
            'agrees_catholic_activities' => (clone $query)->where('agreement_to_participate_in_catholic_activities', true)->count(),
        ];

        // Porcentajes
        $percentages = [
            'acceptance_rate' => $total_interviews > 0 ? round(($accepted / $total_interviews) * 100, 1) : 0,
            'standby_rate' => $total_interviews > 0 ? round(($standby / $total_interviews) * 100, 1) : 0,
            'notification_rate' => $total_interviews > 0 ? round(($notified / $total_interviews) * 100, 1) : 0,
            'pending_rate' => $total_interviews > 0 ? round(($pending / $total_interviews) * 100, 1) : 0,
            'siblings_rate' => $total_interviews > 0 ? round(($with_siblings / $total_interviews) * 100, 1) : 0,
            'catholic' => $total_interviews > 0 ? round(($catholic / $total_interviews) * 100, 1) : 0,
        ];

        return [
            'totals' => [
                'total_interviews' => $total_interviews,
                'accepted' => $accepted,
                'standby' => $standby,
                'notified' => $notified,
                'pending' => $pending,
                'rejected' => $rejected,
                'with_siblings' => $with_siblings,
                'catholic' => $catholic,
            ],
            'by_grade' => $by_grade,
            'by_rating' => $by_rating,
            'by_month' => $by_month,
            'payment_capacity' => $payment_capacity,
            'religious_stats' => $religious_stats,
            'percentages' => $percentages,
        ];
    }


}
