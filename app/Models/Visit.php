<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'path',
        'method',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'screen_resolution',
        'language',
        'country',
        'city',
        'is_robot',
        'user_id',
        'session_id'
    ];

    protected $casts = [
        'is_robot' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    // Relación con el usuario (si está autenticado)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes para filtros comunes
     */

    // Scope para visitas de un usuario específico
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Scope para visitas de una URL específica
    public function scopeForUrl(Builder $query, string $url): Builder
    {
        return $query->where('url', 'like', "%{$url}%");
    }

    // Scope para visitas de un rango de fechas
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // Scope para visitas de dispositivos móviles
    public function scopeMobile(Builder $query): Builder
    {
        return $query->where('device_type', 'mobile');
    }

    // Scope para visitas de bots/crawlers
    public function scopeBots(Builder $query): Builder
    {
        return $query->where('is_robot', true);
    }

    // Scope para visitas humanas (no bots)
    public function scopeHumans(Builder $query): Builder
    {
        return $query->where('is_robot', false);
    }

    /**
     * Métodos útiles
     */

    // Registrar una nueva visita desde un Request de Laravel
    public static function logFromRequest(Request $request): Visit
    {
        $userAgent = $request->userAgent();

        return self::create([
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'referrer' => $request->header('referer'),
            'device_type' => self::detectDeviceType($userAgent),
            'browser' => self::detectBrowser($userAgent),
            'browser_version' => self::detectBrowserVersion($userAgent),
            'platform' => self::detectPlatform($userAgent),
            'language' => $request->getPreferredLanguage(),
            'is_robot' => self::isRobot($userAgent),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ]);
    }

    // Detección básica de dispositivo
    protected static function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) return 'desktop';

        $userAgent = strtolower($userAgent);

        if (preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/', $userAgent)) {
            return (strpos($userAgent, 'ipad') !== false) ? 'tablet' : 'mobile';
        }

        return 'desktop';
    }

    // Detección básica de navegador
    protected static function detectBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) return null;

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'msie') !== false || strpos($userAgent, 'trident') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($userAgent, 'edg') !== false) {
            return 'Microsoft Edge';
        } elseif (strpos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'opera') !== false || strpos($userAgent, 'opr') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    // Detección básica de versión de navegador
    protected static function detectBrowserVersion(?string $userAgent): ?string
    {
        if (!$userAgent) return null;

        // Patrón simple para extraer la versión (puede no ser 100% preciso)
        $pattern = '/(?:chrome|firefox|safari|opera|msie|edg|opr)[\/\s](\d+\.\d+)/i';
        if (preg_match($pattern, $userAgent, $matches)) {
            return $matches[1];
        }

        return null;
    }

    // Detección básica de plataforma/sistema operativo
    protected static function detectPlatform(?string $userAgent): ?string
    {
        if (!$userAgent) return null;

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'macintosh') !== false || strpos($userAgent, 'mac os x') !== false) {
            return 'macOS';
        } elseif (strpos($userAgent, 'linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    // Detección básica de bots
    protected static function isRobot(?string $userAgent): bool
    {
        if (!$userAgent) return false;

        $bots = [
            'bot',
            'spider',
            'crawl',
            'slurp',
            'search',
            'archiver',
            'facebook',
            'twitter',
            'linkedin',
            'google',
            'yandex',
            'bing',
            'duckduckgo',
            'baidu',
            'curl',
            'wget',
            'python'
        ];

        $userAgent = strtolower($userAgent);

        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Métodos de análisis estadístico
     */

    // Obtener estadísticas resumidas
    public static function getStats(string $period = 'today'): array
    {
        $query = self::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        return [
            'total' => $query->count(),
            'unique_visitors' => $query->distinct('ip_address')->count('ip_address'),
            'mobile' => $query->clone()->mobile()->count(),
            'desktop' => $query->clone()->where('device_type', 'desktop')->count(),
            'tablet' => $query->clone()->where('device_type', 'tablet')->count(),
            'bots' => $query->clone()->bots()->count(),
            'top_browsers' => $query->clone()
                ->selectRaw('browser, count(*) as count')
                ->groupBy('browser')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray(),
            'top_pages' => $query->clone()
                ->selectRaw('path, count(*) as count')
                ->groupBy('path')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray(),
        ];
    }

    // Obtener las visitas únicas por día para gráficos
    public static function getUniqueVisitsChartData(int $days = 30): array
    {
        $data = self::selectRaw('DATE(created_at) as date, COUNT(DISTINCT ip_address) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('is_robot', false)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date'),
            'data' => $data->pluck('count'),
        ];
    }
}
