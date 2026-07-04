<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;

trait Enrollments
{
    public function getDateEnrollmentAttribute()
    {
        $erollment = Enrollment::where('estudiant_id',$this->id)->first();
        if ($erollment) {
            $estudiant = Estudiant::find($this->id);
            $estudiant->date_birth = $erollment->date_birth;
            $estudiant->save();
            return $erollment->date_birth;
        }
    }

    public function getEnrollmentAttribute()
    {
        $enrollment = Enrollment::select('enrollments.*')
            ->join('estudiants', 'estudiants.ci_estudiant', '=', 'enrollments.ci_estudiant')
            ->groupBy('estudiants.id')
            ->orderBy('enrollments.created_at','desc')
            ->first();

        return $enrollment;
    }
}
