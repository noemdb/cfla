<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pestudio_id', 'name', 'code', 'code_sm', 'description',
        'status_active', 'hour_social', 'total_hour_social', 'order',
    ];

    protected $table = 'grados';

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Código reducido',
        'description' => 'Descripción',
        'status_active' => 'Estado',
        'hour_social' => 'Horas sociales requeridas',
        'total_hour_social' => 'Horas sociales totales',
        'order' => 'Orden',
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function seccions()
    {
        return $this->hasMany(Seccion::class, 'grado_id');
    }

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'grado_id');
    }

    public function activeSeccions()
    {
        return $this->seccions()->where('status_active', true)->get();
    }

    // Scopes
    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('grados.status_active', $flag);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return '[' . $this->code . '] ' . $this->name;
    }

    private const TAILWIND_COLOR_MAP = [
        1 => 'indigo',
        2 => 'violet',
        3 => 'pink',
        4 => 'rose',
        5 => 'orange',
        6 => 'amber',
        7 => 'emerald',
        8 => 'teal',
        9 => 'cyan',
        10 => 'emerald',
        11 => 'sky',
        12 => 'green',
        13 => 'teal',
        14 => 'cyan',
        15 => 'lime',
        16 => 'blue',
    ];

    /**
     * Tailwind-compatible base color name (e.g. indigo, emerald, slate).
     * Replaces legacy tokens success/info/dark/yellow/purple/red with
     * proper Tailwind palette names. Always returns a valid Tailwind color.
     */
    public function getColorAttribute(): string
    {
        return self::TAILWIND_COLOR_MAP[(int) $this->id] ?? 'slate';
    }

    /**
     * Tailwind-safe class set for subtle UI accents.
     * All class strings are literal so Tailwind's content scanner can detect them.
     * Uses low opacity for subtle distinction (borders, badges, top bar).
     *
     * @return array{bar:string,badge:string,dot:string,border:string,ring:string,subtleBg:string}
     */
    public function getTailwindClassesAttribute(): array
    {
        return match ($this->color) {
            'indigo' => [
                'bar' => 'bg-indigo-500',
                'badge' => 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20',
                'dot' => 'bg-indigo-500',
                'border' => 'border-indigo-500/30',
                'ring' => 'ring-indigo-500/20',
                'subtleBg' => 'bg-indigo-500/[0.04]',
            ],
            'violet' => [
                'bar' => 'bg-violet-500',
                'badge' => 'bg-violet-500/10 text-violet-300 border border-violet-500/20',
                'dot' => 'bg-violet-500',
                'border' => 'border-violet-500/30',
                'ring' => 'ring-violet-500/20',
                'subtleBg' => 'bg-violet-500/[0.04]',
            ],
            'pink' => [
                'bar' => 'bg-pink-500',
                'badge' => 'bg-pink-500/10 text-pink-300 border border-pink-500/20',
                'dot' => 'bg-pink-500',
                'border' => 'border-pink-500/30',
                'ring' => 'ring-pink-500/20',
                'subtleBg' => 'bg-pink-500/[0.04]',
            ],
            'rose' => [
                'bar' => 'bg-rose-500',
                'badge' => 'bg-rose-500/10 text-rose-300 border border-rose-500/20',
                'dot' => 'bg-rose-500',
                'border' => 'border-rose-500/30',
                'ring' => 'ring-rose-500/20',
                'subtleBg' => 'bg-rose-500/[0.04]',
            ],
            'orange' => [
                'bar' => 'bg-orange-500',
                'badge' => 'bg-orange-500/10 text-orange-300 border border-orange-500/20',
                'dot' => 'bg-orange-500',
                'border' => 'border-orange-500/30',
                'ring' => 'ring-orange-500/20',
                'subtleBg' => 'bg-orange-500/[0.04]',
            ],
            'amber' => [
                'bar' => 'bg-amber-500',
                'badge' => 'bg-amber-500/10 text-amber-300 border border-amber-500/20',
                'dot' => 'bg-amber-500',
                'border' => 'border-amber-500/30',
                'ring' => 'ring-amber-500/20',
                'subtleBg' => 'bg-amber-500/[0.04]',
            ],
            'emerald' => [
                'bar' => 'bg-emerald-500',
                'badge' => 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20',
                'dot' => 'bg-emerald-500',
                'border' => 'border-emerald-500/30',
                'ring' => 'ring-emerald-500/20',
                'subtleBg' => 'bg-emerald-500/[0.04]',
            ],
            'teal' => [
                'bar' => 'bg-teal-500',
                'badge' => 'bg-teal-500/10 text-teal-300 border border-teal-500/20',
                'dot' => 'bg-teal-500',
                'border' => 'border-teal-500/30',
                'ring' => 'ring-teal-500/20',
                'subtleBg' => 'bg-teal-500/[0.04]',
            ],
            'cyan' => [
                'bar' => 'bg-cyan-500',
                'badge' => 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20',
                'dot' => 'bg-cyan-500',
                'border' => 'border-cyan-500/30',
                'ring' => 'ring-cyan-500/20',
                'subtleBg' => 'bg-cyan-500/[0.04]',
            ],
            'sky' => [
                'bar' => 'bg-sky-500',
                'badge' => 'bg-sky-500/10 text-sky-300 border border-sky-500/20',
                'dot' => 'bg-sky-500',
                'border' => 'border-sky-500/30',
                'ring' => 'ring-sky-500/20',
                'subtleBg' => 'bg-sky-500/[0.04]',
            ],
            'green' => [
                'bar' => 'bg-green-500',
                'badge' => 'bg-green-500/10 text-green-300 border border-green-500/20',
                'dot' => 'bg-green-500',
                'border' => 'border-green-500/30',
                'ring' => 'ring-green-500/20',
                'subtleBg' => 'bg-green-500/[0.04]',
            ],
            'lime' => [
                'bar' => 'bg-lime-500',
                'badge' => 'bg-lime-500/10 text-lime-300 border border-lime-500/20',
                'dot' => 'bg-lime-500',
                'border' => 'border-lime-500/30',
                'ring' => 'ring-lime-500/20',
                'subtleBg' => 'bg-lime-500/[0.04]',
            ],
            'blue' => [
                'bar' => 'bg-blue-500',
                'badge' => 'bg-blue-500/10 text-blue-300 border border-blue-500/20',
                'dot' => 'bg-blue-500',
                'border' => 'border-blue-500/30',
                'ring' => 'ring-blue-500/20',
                'subtleBg' => 'bg-blue-500/[0.04]',
            ],
            default => [
                'bar' => 'bg-slate-500',
                'badge' => 'bg-slate-500/10 text-slate-300 border border-slate-500/20',
                'dot' => 'bg-slate-500',
                'border' => 'border-slate-500/30',
                'ring' => 'ring-slate-500/20',
                'subtleBg' => 'bg-slate-500/[0.04]',
            ],
        };
    }
}
