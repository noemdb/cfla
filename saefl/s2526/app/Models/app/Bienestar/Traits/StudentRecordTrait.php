<?php

namespace App\Models\app\Bienestar\Traits;

use App\Models\app\Bienestar\StudentRecord;
use Illuminate\Support\Facades\DB;

trait StudentRecordTrait
{
    public static function indicators()
    {
        $indicators = collect();
        $comment = StudentRecord::COLUMN_COMMENTS;

        $student_records = StudentRecord::all(); //dd($student_records->take(20));
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

        foreach ($student_records as $student_record) {
            $private_vehicle = ($student_record->status_transport_private_vehicle) ? ($private_vehicle + 1) : $private_vehicle ;
            $public_vehicle = ($student_record->status_transport_public_vehicle) ? ($public_vehicle + 1) : $public_vehicle ;
            $walking = ($student_record->status_transport_walking) ? ($walking + 1) : $walking ;
            $other = ($student_record->status_transport_other) ? ($other + 1) : $other ;
            ///////////////////status_vaccination_schedule/////////////////////////////////
            $vaccination_schedule_true = ($student_record->status_vaccination_schedule) ? ($vaccination_schedule_true + 1) : $vaccination_schedule_true ;
            $vaccination_schedule_false = (!$student_record->status_vaccination_schedule) ? ($vaccination_schedule_false + 1) : $vaccination_schedule_false ;
            ///////////////////illness/////////////////////////////////
            $cardiovascular = ($student_record->status_illness_cardiovascular) ? ($cardiovascular + 1) : $cardiovascular ;
            $cancer = ($student_record->status_illness_cancer) ? ($cancer + 1) : $cancer ;
            $lupus = ($student_record->status_illness_lupus) ? ($lupus + 1) : $lupus ;
            $diabetes = ($student_record->status_illness_diabetes) ? ($diabetes + 1) : $diabetes ;
            $renal_problems = ($student_record->status_illness_renal_problems) ? ($renal_problems + 1) : $renal_problems ;
            $overweight = ($student_record->status_illness_overweight) ? ($overweight + 1) : $overweight ;
            $illness_other = ($student_record->status_illness_other) ? ($illness_other + 1) : $illness_other ;

            ///////////////////illness/////////////////////////////////
            $intellectual_disability = ($student_record->status_conditions_intellectual_disability) ? ($intellectual_disability + 1) : $intellectual_disability ;
            $motor_disability = ($student_record->status_conditions_motor_disability) ? ($motor_disability + 1) : $motor_disability ;
            $visual_disability = ($student_record->status_conditions_visual_disability) ? ($visual_disability + 1) : $visual_disability ;
            $hearing_impairment = ($student_record->status_conditions_hearing_impairment) ? ($hearing_impairment + 1) : $hearing_impairment ;
            $outstanding_attitudes = ($student_record->status_conditions_outstanding_attitudes) ? ($outstanding_attitudes + 1) : $outstanding_attitudes ;
            $autism = ($student_record->status_conditions_autism) ? ($overweight + 1) : $autism ;
            $conditions_other = ($student_record->status_conditions_other) ? ($conditions_other + 1) : $conditions_other ;

            ///////////////////status_vaccination_schedule/////////////////////////////////
            $treated_by_specialist_true = ($student_record->status_treated_by_specialist) ? ($treated_by_specialist_true + 1) : $treated_by_specialist_true ;
            $treated_by_specialist_false = (!$student_record->status_treated_by_specialist) ? ($treated_by_specialist_false + 1) : $treated_by_specialist_false ;
        }

        ///////////////////transport/////////////////////////////////
        $transport = [
            'private_vehicle'=>$private_vehicle,
            'public_vehicle'=>$public_vehicle,
            'walking'=>$walking,
            'other'=>$other,
            'total'=>$student_records->count(),
        ]; $indicators->put('transport',$transport);

        ///////////////////vaccination/////////////////////////////////
        $vaccination = [
            'vaccination_schedule_true'=>$vaccination_schedule_true,
            'vaccination_schedule_false'=>$vaccination_schedule_false,
            'total'=>$student_records->count(),
        ]; $indicators->put('vaccination',$vaccination);

        ///////////////////status_sports_potential/////////////////////////////////
        $sports_potentials = DB::table('student_records')
            ->select('student_records.sports_potential')
            ->selectRaw('count(student_records.sports_potential) as count_sports_potential')
            ->groupBy('sports_potential')
            ->orderBy('count_sports_potential','desc')
            ->whereNotNull('sports_potential')
            ->get()
            ->pluck('count_sports_potential','sports_potential')->toArray()
            ; //dd($sports_potentials->pluck('count_sports_potential','sports_potential')->toArray());
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
        $treated_by_specialist = [
            'treated_by_specialist_true'=>$treated_by_specialist_true,
            'treated_by_specialist_false'=>$treated_by_specialist_false,
            'total'=>$student_records->count(),
        ]; $indicators->put('treated_by_specialist',$treated_by_specialist);

        return $indicators ;
    }

    public static function illness_others()
    {
        $illness_others = DB::table('student_records')
        ->select('student_records.illness_other')
        ->selectRaw('count(student_records.illness_other) as count_illness_other')
        ->whereNotNull('student_records.illness_other')
        ->groupBy('illness_other')
        ->orderBy('count_illness_other','desc')
        ->get();
        return $illness_others;
    }

    public static function conditions_others()
    {
        $conditions_others = DB::table('student_records')
        ->select('student_records.conditions_other')
        ->selectRaw('count(student_records.conditions_other) as count_conditions_other')
        ->whereNotNull('student_records.conditions_other')
        ->groupBy('conditions_other')
        ->orderBy('count_conditions_other','desc')
        ->get();
        return $conditions_others;
    }

    public static function treated_specialists()
    {
        $treated_specialists = DB::table('student_records')
        ->select('student_records.specialist')
        ->selectRaw('count(student_records.specialist) as count_specialist')
        ->whereNotNull('student_records.specialist')
        ->groupBy('specialist')
        ->orderBy('count_specialist','desc')
        ->get();
        return $treated_specialists;
    }

}
