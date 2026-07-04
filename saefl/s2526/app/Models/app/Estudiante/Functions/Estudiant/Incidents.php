<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentAgreement;
use Carbon\Carbon;

trait Incidents
{
    public function getIncidetsNotificatedAttribute()
    {
        $incidents = Incident::select('incidents.*')
        ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
        ->where('estudiants.id',$this->id)
        ->where('incidents.status_notify',true)
        ->get();

        return $incidents;
    }

    public function getAnnouncementsAttribute()
    {
        $announcements = Incident::select('incidents.*')
        ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
        ->where('estudiants.id',$this->id)
        ->where('incidents.status_announcement',true)
        ->get();

        return $announcements;
    }

    public function getIncidentAgreementsAttribute()
    {
        $incident_agreements = IncidentAgreement::select('incident_agreements.*')
            ->join('incidents', 'incidents.id', '=', 'incident_agreements.incident_id')
            ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
            ->where('estudiants.id',$this->id)
            ->orderBy('incident_agreements.created_at','desc')
            ->get();

        return $incident_agreements;
    }

    // public function getIndexAggressionAttribute()
    // {
    //     $incidents = $this->incidents;
    //     $goal = $incidents->count();
    //     $incidents_aggression = $this->incidents->where('status_aggression',true);
    //     $real = $incidents_aggression->count();

    //     $index_aggression = ($goal > 0) ? $real / $goal : null ;

    //     $index_aggression = $index_aggression * 100;

    //     $index_aggression = round($index_aggression,2);

    //     return $index_aggression;
    // }

    public function getIncidentEventsAttribute()
    {
        $events = array();
        $incidents = $this->incidents->sortBy('created_at');

        foreach ($incidents as $incident) {
            $event = array();
            $exist = Carbon::parse($incident->created_at);
            if ($exist) {
                $date = $incident->created_at->format('Y-m-d');
                $time = $incident->created_at->format('His');
                $event = [
                    'incident_id'=>$incident->id,
                    'incident'=>$incident,
                    'date'=>$incident->created_at,
                    'type'=>'registerIncident',
                    'class'=>'primary',
                    'description'=>'Registro de Incidencia'
                ];
                $events [$date] [$time] = $event;

                if ($incident->date_notify_email) {
                    $date_notify_email = Carbon::parse($incident->date_notify_email);
                    if ($date_notify_email) {
                        $event = array();
                        $event = [
                            'incident_id'=>$incident->id,
                            'incident'=>$incident,
                            'date'=>$date_notify_email,
                            'type'=>'sendNotifyIncident',
                            'class'=>'success',
                            'description'=>'Notificación del registro de incidencia enviada'
                        ];
                        $date = $date_notify_email->format('Y-m-d');
                        $time = $date_notify_email->format('His');
                        $events [$date] [$time] = $event;
                    }
                }

                if ($incident->date_notify_agreement_email) {
                    $date_notify_agreement_email = Carbon::parse($incident->date_notify_agreement_email);
                    if ($date_notify_agreement_email) {
                        $event = array();
                        $event = [
                            'incident_id'=>$incident->id,
                            'incident'=>$incident,
                            'date'=>$date_notify_agreement_email,
                            'type'=>'sendNotifyIncidentAgreement',
                            'class'=>'info',
                            'description'=>'Notificación del acuerdo alcanzado enviada'
                        ];
                        $date = $date_notify_agreement_email->format('Y-m-d');
                        $time = $date_notify_agreement_email->format('His');
                        $events [$date] [$time] = $event;
                    }
                }

                if ($incident->time_announcement) {
                    $time_announcement = Carbon::parse($incident->time_announcement);
                    if ($time_announcement) {
                        $event = array();
                        $event = [
                            'incident_id'=>$incident->id,
                            'incident'=>$incident,
                            'date'=>$time_announcement,
                            'type'=>'sendNotifyIncidentInterview',
                            'class'=>'warning',
                            'description'=>'Entrevista programda',
                        ];
                        $date = $time_announcement->format('Y-m-d');
                        $time = $time_announcement->format('His');
                        $events [$date] [$time] = $event;
                    }
                }

                if ($incident->date_close) {
                    $date_close = Carbon::parse($incident->date_close);
                    if ($date_close) {
                        $event = array();
                        $event = [
                            'incident_id'=>$incident->id,
                            'incident'=>$incident,
                            'date'=>$date_close,
                            'type'=>'closeIncidet',
                            'class'=>'danger',
                            'description'=>'Cierre del Incidente'
                        ];
                        $date = $date_close->format('Y-m-d');
                        $time = $date_close->format('His');
                        $events [$date] [$time] = $event;

                        if ($incident->status_notify_close) {
                            $event = array();
                            $event = [
                                'incident_id'=>$incident->id,
                                'incident'=>$incident,
                                'date'=>$date_close,
                                'type'=>'sendNotifycloseIncidet',
                                'class'=>'secondary',
                                'description'=>'Notificación del cierre del incidente enviada.'
                            ];
                            $date = $date_close->format('Y-m-d');
                            $time = $date_close->addSecond()->format('H:i:s');
                            $events [$date] [$time] = $event;
                        }
                    }
                }

            }
        }
        ksort($events);
        // foreach ($events as $event) {
            //sort($event);
        // }


        return $events;

    }
}
