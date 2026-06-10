<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivityLog extends Model
{
    protected $table = 'lms_activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'activity_id', 'user_id', 'event',
        'context_id', 'context_type', 'ip_address',
    ];

    protected $dates = ['created_at'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        int $activityId,
        int $userId,
        string $event,
        ?int $contextId = null,
        ?string $contextType = null
    ): void {
        static::create([
            'activity_id'  => $activityId,
            'user_id'      => $userId,
            'event'        => $event,
            'context_id'   => $contextId,
            'context_type' => $contextType,
            'ip_address'   => request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
