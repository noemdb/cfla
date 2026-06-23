<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\app\Institucion;
use Carbon\Carbon;

class GenerateFacebookToken extends Command
{
    /*
    Comando dual para gestión de tokens Facebook: consulta estado actual con validación en tiempo real o genera nuevos tokens de larga duración.
    Centraliza diagnóstico y renovación automática, integrado con el sistema WhatsApp Business para mantenimiento eficiente y sin errores.
    modo  consulta: facebook:generate-token
    modo obtener token: facebook:generate-token shortToken
    */
    protected $signature = 'facebook:generate-token {shortToken?}';
    protected $description = 'Genera un token de larga duración o muestra información del token actual';

    public function handle()
    {
        // Si no se proporciona token, mostrar información del token actual
        if (!$this->argument('shortToken')) {
            $this->showCurrentTokenInfo();
            return;
        }

        $shortToken = $this->argument('shortToken');
        $this->generateLongLivedToken($shortToken);
    }

    private function showCurrentTokenInfo()
    {
        $this->info('📋 Información del Token Actual en Base de Datos');
        $this->line(str_repeat('-', 50));

        $institucion = Institucion::find(1);

        if (!$institucion) {
            $this->error('❌ No se encontró la institución en la base de datos');
            return;
        }

        if (!$institucion->facebook_access_token) {
            $this->error('❌ No hay token de Facebook almacenado en la base de datos');
            return;
        }

        // Mostrar información básica
        $this->line('🔑 Token: ' . substr($institucion->facebook_access_token, 0, 20) . '...');
        $this->line('📅 Fecha de almacenamiento: ' . $institucion->facebook_access_token_date);

        if ($institucion->facebook_access_token_expire) {
            $expireDate = Carbon::parse($institucion->facebook_access_token_expire);
            $daysUntilExpire = Carbon::now()->diffInDays($expireDate, false);

            $this->line('⏰ Fecha de expiración: ' . $expireDate);

            if ($daysUntilExpire > 0) {
                $this->info('✅ Estado: Válido (' . $daysUntilExpire . ' días restantes)');
            } else {
                $this->error('❌ Estado: Expirado hace ' . abs($daysUntilExpire) . ' días');
            }
        } else {
            $this->warn('⚠️  No hay fecha de expiración registrada');
        }

        // Media IDs
        $this->line(str_repeat('-', 30));
        $this->line('🖼️ Media IDs:');
        $this->line('   • Control: ' . ($institucion->facebook_media_id_control ?: 'No asignado'));
        $this->line('   • Admon: ' . ($institucion->facebook_media_id_admon ?: 'No asignado'));

        // Validar token con Facebook
        $this->line(str_repeat('-', 30));
        $this->info('🔍 Validando token con Facebook API...');

        if ($this->validateTokenWithFacebook($institucion->facebook_access_token)) {
            $this->info('✅ Token válido según Facebook API');
        } else {
            $this->error('❌ Token inválido o expirado según Facebook API');
        }

        $this->line(str_repeat('-', 50));
        $this->line('💡 Para generar un nuevo token, ejecuta:');
        $this->line('   php artisan facebook:generate-token {token_corto}');
    }

    private function validateTokenWithFacebook($token): bool
    {
        $appId = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');

        if (!$appId || !$appSecret) {
            $this->warn('⚠️  No se pueden validar credenciales (Faltan FACEBOOK_APP_ID o FACEBOOK_APP_SECRET)');
            return false;
        }

        try {
            $response = Http::timeout(10)->get('https://graph.facebook.com/debug_token', [
                'input_token' => $token,
                'access_token' => "{$appId}|{$appSecret}",
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return (bool)($data['is_valid'] ?? false);
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Error al conectar con Facebook API: ' . $e->getMessage());
        }

        return false;
    }

    private function generateLongLivedToken(string $shortToken)
    {
        $appId = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');

        if (!$appId || !$appSecret) {
            $this->error('❌ Faltan FACEBOOK_APP_ID o FACEBOOK_APP_SECRET en .env');
            return;
        }

        $this->info('🔄 Convirtiendo token corto a token de larga duración...');

        try {
            $response = Http::timeout(30)->get('https://graph.facebook.com/v23.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $shortToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $longToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? null;

                $this->info('✅ Token de Larga Duración Generado:');
                $this->line('🔑 Token: ' . $longToken);

                if ($expiresIn) {
                    $days = round($expiresIn / 86400);
                    $this->line('⏰ Duración: ' . $days . ' días (' . $expiresIn . ' segundos)');
                } else {
                    $this->warn('⚠️  Duración: Desconocida');
                }

                // Preguntar si guardar automáticamente
                if ($this->confirm('💾 ¿Quieres guardar este token en la base de datos?')) {
                    $this->updateTokenInDatabase($longToken, $expiresIn);

                    // Preguntar si regenerar media IDs
                    if ($this->confirm('🖼️ ¿Quieres regenerar los Media IDs ahora?')) {
                        $this->call('facebook:refresh-token');
                    }
                }
            } else {
                $error = $response->json();
                $this->error('❌ Error al generar token:');
                $this->line('Código: ' . ($error['error']['code'] ?? 'Desconocido'));
                $this->line('Mensaje: ' . ($error['error']['message'] ?? 'Error desconocido'));
                $this->line('Tipo: ' . ($error['error']['type'] ?? 'Desconocido'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Error de conexión: ' . $e->getMessage());
        }
    }

    private function updateTokenInDatabase($token, $expiresIn)
    {
        $institucion = Institucion::find(1);
        if (!$institucion) {
            $this->error('❌ Institución no encontrada');
            return;
        }

        $institucion->facebook_access_token = $token;
        $institucion->facebook_access_token_date = Carbon::now();

        if ($expiresIn && is_numeric($expiresIn)) {
            $institucion->facebook_access_token_expire = Carbon::now()->addSeconds($expiresIn);
        } else {
            // Por defecto 60 días si no viene expires_in
            $institucion->facebook_access_token_expire = Carbon::now()->addDays(60);
        }

        $institucion->save();

        $this->info('✅ Token guardado en la base de datos exitosamente');
        $this->info('📅 Fecha de expiración: ' . $institucion->facebook_access_token_expire);
    }
}
