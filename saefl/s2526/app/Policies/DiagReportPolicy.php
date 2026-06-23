<?php

namespace App\Policies;

use App\Models\app\Instrument\DiagReport;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class DiagReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any reports.
     * Coordinador and Dirección can see all, Docente sees their areas only.
     */
    public function viewAny(User $user)
    {
        return $user->isProfesor() || $user->isControl() || $user->IsDirector();
    }

    /**
     * Determine if user can view a specific report.
     */
    public function view(User $user, DiagReport $report)
    {
        // Directors and coordinators can view all
        if ($user->IsDirector() || $user->isControl()) {
            return true;
        }

        // Docentes can only view reports for their students
        if ($user->isProfesor()) {
            // Check if this profesor teaches this student
            // This would need to check through pevaluacions/pensums
            return $this->profesorTeachesStudent($user, $report->estudiant_id);
        }

        return false;
    }

    /**
     * Determine if user can update a report.
     * Only coordinators can edit reports.
     */
    public function update(User $user, DiagReport $report)
    {
        // Only coordinators and directors can update
        return $user->isControl() || $user->IsDirector();
    }

    /**
     * Determine if user can approve a report.
     * Only coordinators can approve.
     */
    public function approve(User $user, DiagReport $report)
    {
        return $user->isControl();
    }

    /**
     * Determine if user can sign a report.
     * Docentes can sign reports for their areas.
     */
    public function sign(User $user, DiagReport $report)
    {
        if (!$user->isProfesor()) {
            return false;
        }

        // Check if profesor teaches this student
        return $this->profesorTeachesStudent($user, $report->estudiant_id);
    }

    /**
     * Helper: Check if profesor teaches a student.
     */
    protected function profesorTeachesStudent(User $user, $estudiantId)
    {
        if (!$user->profesor) {
            return false;
        }

        // Check through pevaluacions if this profesor has this student
        $hasStudent = \DB::table('pevaluacions')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('inscripcions', 'pensums.pestudio_id', '=', 'inscripcions.pestudio_id')
            ->where('pevaluacions.profesor_id', $user->profesor->id)
            ->where('inscripcions.estudiant_id', $estudiantId)
            ->exists();

        return $hasStudent;
    }
}
