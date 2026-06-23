<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Pescolar\Grado;
use App\Models\app\SocialAction\CommunityAction;
use App\Models\app\SocialAction\CommunityHour;
use Illuminate\Support\Facades\DB;

trait CommunityActions
{

    public function getSocialGradosAttribute()
    {
        return Grado::query()
        ->select('grados.*')
        ->join('community_actions', 'grados.id', '=', 'community_actions.grado_id')
        ->join('community_hours', 'community_actions.id', '=', 'community_hours.community_action_id')
        ->where('community_hours.estudiant_id',$this->id)
        ->where('grados.status_active',"true")
        ->groupBy('grados.id')
        ->get();
    }

    public function getHoursCompletedForGradoId($grado_id)
    {
        return CommunityHour::query()
        ->join('community_actions', 'community_actions.id', '=', 'community_hours.community_action_id')
        ->where('community_actions.grado_id',$grado_id)
        ->where('community_hours.estudiant_id',$this->id)
        ->sum('community_hours.duration');
    }


    public function getHoursCompletedAttribute()
    {
        return CommunityHour::query()
        ->where('estudiant_id',$this->id)
        ->sum('duration');
    }

    public function getHoursCompletedForCommunityHour($community_action_id)
    {
        //dd($this->id,$community_action_id);
        return CommunityHour::query()
        ->where('estudiant_id',$this->id)
        ->where('community_action_id',$community_action_id)
        ->sum('duration');
    }

    public function getCommunityActionsAttribute()
    {
        return CommunityAction::query()
        ->select('community_actions.*')
        ->join('community_hours', 'community_actions.id', '=', 'community_hours.community_action_id')
        ->where('community_hours.estudiant_id',$this->id)
        ->groupBy('community_actions.id')
        ->get();
    }




}
