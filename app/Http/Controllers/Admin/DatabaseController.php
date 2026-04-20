<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DatabaseController extends Controller
{
    /**
     * Genera y descarga un respaldo completo de la base de datos MySQL.
     * Solo accesible para administradores.
     */
    public function downloadBackup()
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }

        try {
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $date = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "respaldo_{$dbName}_{$date}.sql";
            $path = storage_path("app/{$filename}");

            // Definir el comando mysqldump
            // Usamos el entorno para pasar la contraseña de forma segura (sin que aparezca en el listado de procesos)
            $command = [
                'mysqldump',
                "--no-tablespaces", // Evita errores si el usuario no tiene privilegios de PROCESS
                "-h{$dbHost}",
                "-u{$dbUser}",
                $dbName
            ];

            $process = new Process($command, null, ['MYSQL_PWD' => $dbPass]);
            $process->setTimeout(600); // 10 minutos de tiempo límite
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Escribir la salida al archivo temporal
            file_put_contents($path, $process->getOutput());

            Log::info("Respaldo de base de datos generado exitosamente por: " . Auth::user()->username);

            return Response::download($path, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error("Error generando respaldo BD: " . $e->getMessage());
            
            return back()->with('error', 'Error crítico al generar el respaldo de la base de datos. Verique los logs del sistema.');
        }
    }
}
