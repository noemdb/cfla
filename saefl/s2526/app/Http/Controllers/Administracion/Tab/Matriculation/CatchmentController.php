<?php

namespace App\Http\Controllers\Administracion\Tab\Matriculation;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentActivity;
use App\Models\app\Enrollment\CatchmentGroup;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Pescolar\Grado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\General\Email\CatchmentController as CatchmentControllerSendMail;

class CatchmentController extends Controller
{
    public function interviews(Request $request)
    {
        $catchment_interviews = CatchmentInterview::all()->sortBy('created_at');
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        return view('administracion.matriculations.interviews.index', compact('catchment_interviews', 'list_comment'));
    }

    public function edit_interview($id)
    {
        $catchment_interview = CatchmentInterview::findOrfail($id);
        $list_comment        = CatchmentInterview::COLUMN_COMMENTS;
        $list_grade          = CatchmentInterview::list_grade();
        $list_religions      = CatchmentInterview::list_religions();
        return view('administracion.matriculations.interviews.edit', compact('catchment_interview', 'list_comment', 'list_grade', 'list_religions'));
    }

    public function update_interview(Request $request, $id)
    {
        $request->validate([
            'accepted' => [
                'boolean',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->input('status_standby')) {
                        $fail('No puede aceptar y poner en espera una solicitud de matrícula al mismo tiempo.');
                    }
                },
            ],
            'status_standby' => [
                'boolean',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->input('accepted')) {
                        $fail('No se puede poner en espera y aceptar una solicitud de matrícula al mismo tiempo.');
                    }
                },
            ],
        ]);

        $catchment_interview = CatchmentInterview::findOrfail($id);
        $catchment_interview->fill($request->all());
        $catchment_interview->save();

        if ($catchment_interview->accepted == true) {
            $catchment_interview->generateToken();
            $catchment_interview->save();
        }

        if ($catchment_interview->status_notified == false) {

            $time     = Carbon::now()->addSeconds(20);
            $jobSend  = new CatchmentControllerSendMail();
            $jobSend->sendMailCatchmentAccepted($catchment_interview->id, $time);

            if ($catchment_interview->status_standby == true) {
                $time    = Carbon::now()->addSeconds(20);
                $jobSend = new CatchmentControllerSendMail();
                $jobSend->sendMailCatchmentStandby($catchment_interview->id, $time);
            }
        }

        if ($request->status_notify == true) {
            $catchment_interview->status_notified = true;
            $catchment_interview->save();
        }

        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok', $messenge);
        return redirect()->route('administracion.matriculations.interviews.index');
    }

    public function indicators(Request $request)
    {
        $catchments                       = Catchment::all()->where('status_active', true)->sortBy('created_at');
        $gradoIdTotals                    = Catchment::gradoIdTotals();
        $dailyHourlyTotals                = Catchment::dailyHourlyTotals();
        $pestudioIdTotals                 = Catchment::pestudioIdTotals();
        $institutionOriginTotals          = Catchment::institutionOriginTotals();
        $institutionOriginTotalsForGrade  = Catchment::institutionOriginTotals(1);
        $institutionOriginTotalsForInitial = Catchment::institutionOriginTotalsArray([22, 23, 24]);
        $groupIdTotals                    = Catchment::groupIdTotals();

        $catchment = Catchment::where('grade', 6)->first();
        $group_id  = ($catchment) ? $catchment->getGroupIdEnable($catchment->grade) : null;

        $compact = [
            'catchments',
            'gradoIdTotals',
            'dailyHourlyTotals',
            'pestudioIdTotals',
            'institutionOriginTotals',
            'groupIdTotals',
            'institutionOriginTotalsForGrade',
            'institutionOriginTotalsForInitial',
        ];
        return view('administracion.matriculations.catchments.indicators', compact($compact));
    }

    public function index(Request $request)
    {
        $representant_ci = (!empty($request->representant_ci)) ? $request->representant_ci : null;

        $catchments   = Catchment::select('catchments.*');
        $catchments   = ($representant_ci) ? $catchments->where('representant_ci', $representant_ci) : $catchments;
        $catchments   = $catchments->where('status_active', true)->orderBy('created_at', 'desc')->get();
        $list_comment = Catchment::COLUMN_COMMENTS;
        return view('administracion.matriculations.catchments.index', compact('catchments', 'list_comment', 'representant_ci'));
    }

    //////////////////////////////////////////////////////////////////////////////

    public function index_groups(Request $request)
    {
        $catchment_groups = CatchmentGroup::all();
        $list_comment     = CatchmentGroup::COLUMN_COMMENTS;
        return view('administracion.matriculations.catchment_groups.index', compact('catchment_groups', 'list_comment'));
    }

    public function edit_group($id)
    {
        $catchment_group = CatchmentGroup::findOrfail($id);
        $list_comment    = CatchmentGroup::COLUMN_COMMENTS;
        $list_grado      = Grado::list_pestudio_grado();
        return view('administracion.matriculations.catchment_groups.edit', compact('catchment_group', 'list_comment', 'list_grado'));
    }

    public function update_group(Request $request, $id)
    {
        $catchment_group = CatchmentGroup::findOrfail($id);
        $catchment_group->fill($request->all());
        $catchment_group->save();
        Session::flash('operp_ok', trans('db_oper_result.update_ok'));
        return redirect()->route('administracion.matriculations.catchment_groups.index');
    }

    //////////////////////////////////////////////////////////////////////////////

    public function index_activity(Request $request)
    {
        $catchment_activities = CatchmentActivity::all();
        $list_comment         = CatchmentActivity::COLUMN_COMMENTS;
        return view('administracion.matriculations.catchment_activities.index', compact('catchment_activities', 'list_comment'));
    }

    public function edit_activity($id)
    {
        $catchment_activity = CatchmentActivity::findOrfail($id);
        $list_comment       = CatchmentActivity::COLUMN_COMMENTS;
        $list_group         = CatchmentGroup::list_grado_group();
        return view('administracion.matriculations.catchment_activities.edit', compact('catchment_activity', 'list_comment', 'list_group'));
    }

    public function update_activity(Request $request, $id)
    {
        $catchment_activity = CatchmentActivity::findOrfail($id);
        $catchment_activity->fill($request->all());
        $catchment_activity->save();
        Session::flash('operp_ok', trans('db_oper_result.update_ok'));
        return redirect()->route('administracion.matriculations.catchment_activities.index');
    }

    public function destroy($id, Request $request)
    {
        $catchment = Catchment::findOrFail($id);
        $messenge  = trans('db_oper_result.destroy_not_ok');

        if ($catchment->status_delete) {
            $catchment->status_active = false;
            $catchment->save();
            $messenge  = trans('db_oper_result.delete_ok');
            $operation = 'delete';

            if ($request->ajax()) {
                return response()->json([
                    'messenge'  => $messenge,
                    'operation' => $operation,
                ]);
            }
        }

        Session::flash('operp_ok', $messenge);
        return redirect()->route('administracion.matriculations.catchments.index');
    }

    public function force_destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $catchment = Catchment::findOrFail($id);

            foreach($catchment->catchmentInterviews as $interview) {
                $interview->delete();
            }

            $catchment->delete();
            DB::commit();

            $messenge = trans('db_oper_result.delete_ok') . " (Definitivo)";
            
            if ($request->ajax()) {
                return response()->json([
                    'messenge'  => $messenge,
                    'operation' => 'forceDelete',
                ]);
            }

            Session::flash('operp_ok', $messenge);
            return redirect()->route('administracion.matriculations.catchments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar definitivamente: ' . $e->getMessage(),
                ], 500);
            }
            Session::flash('operp_error', 'Error al eliminar definitivamente: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Elimina un registro de entrevista (solo si no está aceptada)
     */
    public function destroy_interview($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($id);

            if ($interview->accepted) {
                throw new \Exception('No se puede eliminar una entrevista que ya ha sido aceptada.');
            }

            $interview->delete();
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('db_oper_result.delete_ok'),
                ]);
            }

            Session::flash('operp_ok', trans('db_oper_result.delete_ok'));
            return redirect()->route('administracion.matriculations.interviews.index');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la entrevista: ' . $e->getMessage(),
                ], 500);
            }

            Session::flash('operp_error', 'Error al eliminar la entrevista: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
