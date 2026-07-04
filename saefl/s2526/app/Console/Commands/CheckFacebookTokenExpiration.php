<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FacebookTokenService;
use Exception;

class CheckFacebookTokenExpiration extends Command
{
    protected $signature = 'facebook:check-expiration';
    protected $description = 'Verifica y renueva el token de Facebook si está por expirar';

    public function __construct(private FacebookTokenService $tokenService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('🔍 Verificando expiración del token de Facebook...');

        try {
            $wasRenewed = $this->tokenService->checkAndRenewTokenIfNeeded();

            if ($wasRenewed) {
                $this->info('✅ Token renovado exitosamente');
            } else {
                $this->info('ℹ️ Token aún vigente, no se requiere renovación');
            }
        } catch (Exception $e) {
            $this->error('❌ Error al verificar token: ' . $e->getMessage());
        }
    }
}
