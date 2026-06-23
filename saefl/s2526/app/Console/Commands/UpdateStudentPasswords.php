<?php

namespace App\Console\Commands;

use App\User;
use App\Models\sys\Rol;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdateStudentPasswords extends Command
{
    /**
     * The name and signature of the console command.
     * php artisan users:update-student-passwords --password=".kainos." --show
     * 
     * @var string
     */
    protected $signature = 'users:update-student-passwords 
                            {--password= : Contraseña específica para todos los usuarios}
                            {--dni : Usar el número de cédula como contraseña (ci_estudiant)}
                            {--random : Generar contraseñas aleatorias}
                            {--show : Mostrar las contraseñas generadas}
                            {--dry-run : Simula la ejecución sin guardar cambios en la base de datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las contraseñas de los usuarios con área ESTUDIANTIL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando actualización de contraseñas para usuarios ESTUDIANTIL...');
        
        // Verificar si es modo simulación
        if ($this->option('dry-run')) {
            $this->warn('=== MODO SIMULACIÓN (--dry-run) ===');
            $this->warn('No se guardarán cambios en la base de datos');
            $this->newLine();
        }
        
        // Obtener la fecha actual para filtrar roles vigentes
        $fecha = Carbon::now();
        
        // Obtener los usuarios con rol ESTUDIANTIL vigente
        $users = User::whereHas('rols', function($query) use ($fecha) {
            $query->where('area', 'ESTUDIANTIL')
                  ->where('finicial', '<=', $fecha)
                  ->where('ffinal', '>=', $fecha);
        })->with('estudiant')->get();
        
        if ($users->isEmpty()) {
            $this->error('No se encontraron usuarios con rol ESTUDIANTIL vigente.');
            return 1;
        }
        
        $this->info("Se encontraron {$users->count()} usuarios con rol ESTUDIANTIL.");
        
        // Determinar el método de generación de contraseñas
        $passwordMethod = $this->determinePasswordMethod();
        
        if (!$passwordMethod) {
            $this->error('Debes especificar un método de generación de contraseñas: --password, --dni o --random');
            return 1;
        }
        
        // Mostrar resumen del método seleccionado
        $this->displayMethodSummary($passwordMethod);
        
        // Confirmar antes de proceder (excepto en dry-run)
        if (!$this->option('dry-run') && !$this->confirm('¿Deseas continuar con la actualización de contraseñas?')) {
            $this->info('Operación cancelada.');
            return 0;
        }
        
        // Barra de progreso
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        $updated = 0;
        $passwords = [];
        $errors = [];
        
        foreach ($users as $user) {
            $newPassword = $this->generatePassword($user, $passwordMethod);
            $oldPassword = $user->password; // Solo para referencia en dry-run
            
            try {
                if (!$this->option('dry-run')) {
                    DB::beginTransaction();
                    
                    // Actualizar la contraseña usando el mutador del modelo
                    $user->password = $newPassword;
                    $user->save();
                    
                    DB::commit();
                }
                
                $updated++;
                
                // Guardar información para mostrar
                $ident = $user->estudiant->ci_estudiant ?? $user->username ?? 'N/A';
                $record = [
                    'ID' => $user->id,
                    'Usuario' => $user->username,
                    'Email' => $user->email,
                    'Identificación' => $ident,
                    'Nueva Contraseña' => $newPassword
                ];
                
                // En modo simulación, mostrar también la contraseña anterior
                if ($this->option('dry-run')) {
                    $record['Contraseña Actual (hash)'] = substr($oldPassword, 0, 20) . '...';
                }
                
                $passwords[] = $record;
                
            } catch (\Exception $e) {
                if (!$this->option('dry-run')) {
                    DB::rollBack();
                }
                $errors[] = "Error con usuario ID {$user->id} ({$user->username}): " . $e->getMessage();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Mostrar resultados
        if ($this->option('dry-run')) {
            $this->warn('=== SIMULACIÓN COMPLETADA ===');
            $this->line("Se procesaron {$updated} de {$users->count()} usuarios (simulación)");
        } else {
            $this->info("Proceso completado. {$updated} de {$users->count()} contraseñas actualizadas correctamente.");
        }
        
        // Mostrar errores si los hay
        if (!empty($errors)) {
            $this->newLine();
            $this->error('ERRORES ENCONTRADOS:');
            foreach ($errors as $error) {
                $this->error($error);
            }
        }
        
        // Mostrar contraseñas si se solicitó
        if ($this->option('show') && !empty($passwords)) {
            $this->newLine();
            
            if ($this->option('dry-run')) {
                $this->info('SIMULACIÓN - CONTRASEÑAS QUE SE GENERARÍAN:');
                $headers = ['ID', 'Usuario', 'Email', 'Identificación', 'Nueva Contraseña', 'Contraseña Actual (hash)'];
            } else {
                $this->info('CONTRASEÑAS GENERADAS:');
                $headers = ['ID', 'Usuario', 'Email', 'Identificación', 'Nueva Contraseña'];
            }
            
            $this->table($headers, $passwords);
            
            if (!$this->option('dry-run')) {
                $this->warn('IMPORTANTE: Guarda estas contraseñas en un lugar seguro. No podrás volver a verlas.');
            }
        }
        
        // Resumen final para modo simulación
        if ($this->option('dry-run')) {
            $this->newLine();
            $this->warn('=== FIN DE LA SIMULACIÓN ===');
            $this->warn('Para aplicar los cambios, ejecuta el comando sin --dry-run');
        }
        
        return 0;
    }
    
    /**
     * Determinar el método de generación de contraseñas
     */
    private function determinePasswordMethod()
    {
        if ($this->option('password')) {
            return ['method' => 'fixed', 'value' => $this->option('password')];
        }
        
        if ($this->option('dni')) {
            return ['method' => 'dni'];
        }
        
        if ($this->option('random')) {
            return ['method' => 'random'];
        }
        
        // Preguntar al usuario si no especificó método
        $choice = $this->choice(
            '¿Cómo deseas generar las contraseñas?',
            ['fixed' => 'Contraseña fija para todos', 'dni' => 'Usar cédula del estudiante', 'random' => 'Contraseñas aleatorias'],
            'random'
        );
        
        if ($choice === 'fixed') {
            $password = $this->secret('Ingresa la contraseña para todos los usuarios:');
            return ['method' => 'fixed', 'value' => $password];
        }
        
        if ($choice === 'dni') {
            return ['method' => 'dni'];
        }
        
        return ['method' => 'random'];
    }
    
    /**
     * Mostrar resumen del método seleccionado
     */
    private function displayMethodSummary($method)
    {
        $this->newLine();
        $this->info('RESUMEN DE LA OPERACIÓN:');
        
        switch ($method['method']) {
            case 'fixed':
                $this->line('• Método: Contraseña fija para todos los usuarios');
                $this->line('• La contraseña se ha proporcionado (oculta por seguridad)');
                break;
                
            case 'dni':
                $this->line('• Método: Usar cédula del estudiante como contraseña');
                $this->line('• Se usará el campo ci_estudiant de cada estudiante');
                break;
                
            case 'random':
                $this->line('• Método: Contraseñas aleatorias');
                $this->line('• Se generará una contraseña única de 10 caracteres para cada usuario');
                break;
        }
        
        if ($this->option('show')) {
            $this->line('• Se mostrarán las contraseñas generadas al finalizar');
        }
        
        if ($this->option('dry-run')) {
            $this->warn('• MODO SIMULACIÓN ACTIVADO: No se guardarán cambios');
        }
        
        $this->newLine();
    }
    
    /**
     * Generar contraseña según el método seleccionado
     */
    private function generatePassword($user, $method)
    {
        switch ($method['method']) {
            case 'fixed':
                return $method['value'];
                
            case 'dni':
                // Usar la cédula del estudiante como contraseña
                $ci = $user->estudiant->ci_estudiant ?? null;
                if ($ci) {
                    return $ci;
                }
                // Fallback a random si no hay cédula (con advertencia)
                if (!$this->option('dry-run')) {
                    $this->warn("Usuario ID {$user->id} no tiene cédula, se usará contraseña aleatoria.");
                }
                return $this->generateRandomPassword();
                
            case 'random':
                return $this->generateRandomPassword();
                
            default:
                return $this->generateRandomPassword();
        }
    }
    
    /**
     * Generar contraseña aleatoria
     */
    private function generateRandomPassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}