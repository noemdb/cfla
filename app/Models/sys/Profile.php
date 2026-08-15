<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'card_number', 'firstname', 'lastname',
        'url_img', 'dir_address',
    ];

    protected $table = 'profiles';

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * card_number (cédula) se enmascara por ser PII sensible.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'user_id', 'card_number', 'firstname', 'lastname', 'url_img', 'dir_address'];
    }

    public function maskedAuditFields(): array
    {
        return ['card_number'];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getFullnameAttribute()
    {
        return trim(($this->firstname ?? '').' '.($this->lastname ?? ''));
    }
}
