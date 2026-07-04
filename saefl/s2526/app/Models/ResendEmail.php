<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResendEmail extends Model
{
    protected $fillable = [
        'resend_id',
        'from',
        'to',
        'subject',
        'html',
        'text',
        'cc',
        'bcc',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'events'
    ];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'events' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime'
    ];

    public function updateStatus($status, $timestamp = null)
    {
        $this->status = $status;

        switch ($status) {
            case 'sent':
                $this->sent_at = $timestamp ?? now();
                break;
            case 'delivered':
                $this->delivered_at = $timestamp ?? now();
                break;
            case 'opened':
                $this->opened_at = $timestamp ?? now();
                break;
            case 'clicked':
                $this->clicked_at = $timestamp ?? now();
                break;
        }

        $this->save();
    }

    public function addEvent($event)
    {
        $events = $this->events ?? [];
        $events[] = $event;
        $this->events = $events;
        $this->save();
    }
}
