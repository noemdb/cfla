<?php

namespace App\Models\app\Bienestar\Traits;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\Representant;
use Illuminate\Support\Facades\DB;

trait EnrollmentTrait
{
    public static function indicators()
    {
        $indicators = collect();
        $comment = Enrollment::COLUMN_COMMENTS;

        $enrollments = Enrollment::EnrollmentsFormaly()->get(); 
        $total = $enrollments->count();
        $private_vehicle = 0;
        $public_vehicle = 0;
        $walking = 0;
        $other = 0;
        $vaccination_schedule_true = 0;
        $vaccination_schedule_false = 0;
        $cardiovascular = 0;
        $cancer = 0;
        $lupus = 0;
        $diabetes = 0;
        $renal_problems = 0;
        $overweight = 0;
        $illness_other = 0;
        $intellectual_disability=0;
        $motor_disability=0;
        $visual_disability=0;
        $hearing_impairment=0;
        $outstanding_attitudes=0;
        $autism=0;
        $conditions_other=0;

        $treated_by_specialist_true = 0;
        $treated_by_specialist_false = 0;

        foreach ($enrollments as $enrollment) {
            $private_vehicle = ($enrollment->status_transport_private_vehicle) ? ($private_vehicle + 1) : $private_vehicle ;
            $public_vehicle = ($enrollment->status_transport_public_vehicle) ? ($public_vehicle + 1) : $public_vehicle ;
            $walking = ($enrollment->status_transport_walking) ? ($walking + 1) : $walking ;
            $other = ($enrollment->status_transport_other) ? ($other + 1) : $other ;
            ///////////////////status_vaccination_schedule/////////////////////////////////
            // $vaccination_schedule_true = ($enrollment->status_vaccination_schedule) ? ($vaccination_schedule_true + 1) : $vaccination_schedule_true ;
            // $vaccination_schedule_false = (!$enrollment->status_vaccination_schedule) ? ($vaccination_schedule_false + 1) : $vaccination_schedule_false ;
            ///////////////////illness/////////////////////////////////
            $cardiovascular = ($enrollment->status_illness_cardiovascular) ? ($cardiovascular + 1) : $cardiovascular ;
            $cancer = ($enrollment->status_illness_cancer) ? ($cancer + 1) : $cancer ;
            $lupus = ($enrollment->status_illness_lupus) ? ($lupus + 1) : $lupus ;
            $diabetes = ($enrollment->status_illness_diabetes) ? ($diabetes + 1) : $diabetes ;
            $renal_problems = ($enrollment->status_illness_renal_problems) ? ($renal_problems + 1) : $renal_problems ;
            $overweight = ($enrollment->status_illness_overweight) ? ($overweight + 1) : $overweight ;
            $illness_other = ($enrollment->illness_other<>'NULL' && $enrollment->status_illness_other) ? ($illness_other + 1) : $illness_other ;

            ///////////////////illness/////////////////////////////////
            $intellectual_disability = ($enrollment->status_conditions_intellectual_disability) ? ($intellectual_disability + 1) : $intellectual_disability ;
            $motor_disability = ($enrollment->status_conditions_motor_disability) ? ($motor_disability + 1) : $motor_disability ;
            $visual_disability = ($enrollment->status_conditions_visual_disability) ? ($visual_disability + 1) : $visual_disability ;
            $hearing_impairment = ($enrollment->status_conditions_hearing_impairment) ? ($hearing_impairment + 1) : $hearing_impairment ;
            $outstanding_attitudes = ($enrollment->status_conditions_outstanding_attitudes) ? ($outstanding_attitudes + 1) : $outstanding_attitudes ;
            $autism = ($enrollment->status_conditions_autism) ? ($overweight + 1) : $autism ;
            $conditions_other = ($enrollment->status_conditions_other) ? ($conditions_other + 1) : $conditions_other ;

            ///////////////////treated_by_specialist_true/////////////////////////////////
            // $treated_by_specialist_true = ($enrollment->status_treated_by_specialist) ? ($treated_by_specialist_true + 1) : $treated_by_specialist_true ;
            // $treated_by_specialist_false = (!$enrollment->status_treated_by_specialist) ? ($treated_by_specialist_false + 1) : $treated_by_specialist_false ;
        }

        ///////////////////transport/////////////////////////////////
        $transport = [
            'private_vehicle'=>$private_vehicle,
            'public_vehicle'=>$public_vehicle,
            'walking'=>$walking,
            'other'=>$other,
            'total'=>$enrollments->count(),
        ]; $indicators->put('transport',$transport);


        ///////////////////status_sports_potential/////////////////////////////////
        $sports_potentials = DB::table('enrollments')
            ->select('enrollments.sports_potential')
            ->selectRaw('count(enrollments.sports_potential) as count_sports_potential')
            ->where('enrollments.sports_potential','<>','NULL')
            ->groupBy('sports_potential')
            ->orderBy('count_sports_potential','desc')
            ->whereNotNull('sports_potential')
            ->get()
            ->pluck('count_sports_potential','sports_potential')->toArray()
            ;
        $indicators->put('sports_potentials',$sports_potentials);

        ///////////////////illness/////////////////////////////////
        $illness = [
            $comment['status_illness_cardiovascular']=>$cardiovascular,
            $comment['status_illness_cancer']=>$cancer,
            $comment['status_illness_lupus']=>$lupus,
            $comment['status_illness_diabetes']=>$diabetes,
            $comment['status_illness_renal_problems']=>$renal_problems,
            $comment['status_illness_overweight']=>$overweight,
            $comment['status_illness_other']=>$illness_other,
        ]; arsort($illness); //dd($illness);
        $indicators->put('illness',$illness);

        ///////////////////conditions/////////////////////////////////
        $conditions = [
            $comment['status_conditions_intellectual_disability']=>$intellectual_disability,
            $comment['status_conditions_motor_disability']=>$motor_disability,
            $comment['status_conditions_visual_disability']=>$visual_disability,
            $comment['status_conditions_hearing_impairment']=>$hearing_impairment,
            $comment['status_conditions_outstanding_attitudes']=>$outstanding_attitudes,
            $comment['status_conditions_autism']=>$autism,
            $comment['status_conditions_other']=>$conditions_other,
        ]; arsort($conditions);
        $indicators->put('conditions',$conditions);

        ///////////////////treated_by_specialist/////////////////////////////////
        $treated_specialists = Enrollment::treated_specialists();
        $true_treated_specialists = $treated_specialists->count();
        $false_treated_specialists = $total - $true_treated_specialists;
        $treated_by_specialist = [
            'treated_by_specialist_true'=>$true_treated_specialists,
            'treated_by_specialist_false'=>$false_treated_specialists,
            'total'=>$total,
        ]; $indicators->put('treated_by_specialist',$treated_by_specialist);

        
        ///////////////////vaccination_schedules/////////////////////////////////
        $vaccination_schedules = Enrollment::vaccination_schedules(); //dd($vaccination_schedules);
        $true_vaccination_schedules = $vaccination_schedules->count(); //dd($true_vaccination_schedules);
        $false_vaccination_schedules = $total - $true_vaccination_schedules;
        $vaccination = [
            'vaccination_schedule_true'=>$true_vaccination_schedules,
            'vaccination_schedule_false'=>$false_vaccination_schedules,
            'total'=>$total,
        ]; $indicators->put('vaccination',$vaccination);   

        //$representants = Representant::formalySolvents(); dd($representants);                 

        return $indicators ;
    }

    public static function illness_others()
    {
        $illness_others = Enrollment::EnrollmentsFormaly()
        ->select('enrollments.illness_other')
        ->selectRaw('count(enrollments.illness_other) as count_illness_other')
        ->where('enrollments.status_illness_other',true)
        ->where('enrollments.illness_other','<>','NULL')
        ->whereNotNull('enrollments.illness_other')
        ->groupBy('enrollments.illness_other')
        ->orderBy('count_illness_other','desc')
        ->get();
        return $illness_others;
    }

    public static function conditions_others()
    {
        $conditions_others = Enrollment::EnrollmentsFormaly()
        ->select('enrollments.conditions_other')
        ->selectRaw('count(enrollments.conditions_other) as count_conditions_other')
        ->whereNotNull('enrollments.conditions_other')
        ->where('enrollments.conditions_other','<>','NULL')
        ->groupBy('enrollments.conditions_other')
        ->orderBy('count_conditions_other','desc')
        ->get();
        return $conditions_others;
    }

    public static function treated_specialists()
    {
        $treated_specialists = Enrollment::EnrollmentsFormaly()
        ->select('enrollments.specialist')
        ->selectRaw('count(enrollments.specialist) as count_specialist')
        ->whereNotNull('enrollments.specialist')
        ->where('enrollments.specialist','<>','NULL')
        ->groupBy('enrollments.specialist')
        ->orderBy('count_specialist','desc')
        ->get(); //dd($treated_specialists);
        return $treated_specialists;
    }

    public static function vaccination_schedules()
    {
        $vaccination_schedules = Enrollment::EnrollmentsFormaly()
        ->select('enrollments.status_vaccination_schedule')
        ->selectRaw('count(enrollments.status_vaccination_schedule) as count_status_vaccination_schedule')
        ->where('enrollments.status_vaccination_schedule',true)
        // ->where('enrollments.specialist','<>','NULL')
        ->groupBy('enrollments.status_vaccination_schedule')
        ->orderBy('count_status_vaccination_schedule','desc')
        ->get(); //dd($vaccination_schedules);
        return $vaccination_schedules;
    }

}
