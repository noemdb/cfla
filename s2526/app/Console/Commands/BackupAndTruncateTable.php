<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupAndTruncateTable extends Command
{
    protected $signature = 'backup:truncate-activitylog';
    protected $description = 'Respaldar y truncar la tabla activity_log';
    protected $table = 'activity_log';

    public function handle()
    {
        $table = $this->table;
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            $this->error("La tabla '$table' no existe.");
            return;
        }
        if (!DB::table($table)->count()) {
            $this->info("La tabla '$table' está vacía, no se requiere respaldo ni truncamiento.");
            return;
        }
        $this->info("Iniciando respaldo y truncamiento de la tabla '$table'...");
        
        // Crear directorio de respaldo si no existe
        $timestamp = now()->format('Y_m_d');
        $backupDir = storage_path("app/backups");

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $sqlFile = "$backupDir/{$table}_{$timestamp}.sql";
        $gzFile  = "$sqlFile.gz";

        // Datos de conexión
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbName = config('database.connections.mysql.database');

        // Crear respaldo con mysqldump
        $this->info("Generando respaldo de la tabla '$table'...");
        $dumpCommand = "mysqldump -h$dbHost -P$dbPort -u$dbUser -p\"$dbPass\" $dbName $table > \"$sqlFile\"";
        exec($dumpCommand, $output, $result);

        if ($result !== 0) {
            $this->error("Error al hacer el respaldo.");
            return;
        }

        // Comprimir archivo
        $this->info("Comprimiendo respaldo...");
        $fpIn = fopen($sqlFile, 'rb');
        $fpOut = gzopen($gzFile, 'wb9');
        while (!feof($fpIn)) {
            gzwrite($fpOut, fread($fpIn, 1024 * 512));
        }
        fclose($fpIn);
        gzclose($fpOut);

        // Eliminar el archivo .sql original
        unlink($sqlFile);

        $this->info("Respaldo comprimido: $gzFile");

        // Truncar tabla
        DB::table($table)->truncate();
        $this->info("Tabla '$table' truncada.");
    }
}
