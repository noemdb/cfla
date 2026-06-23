<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use Carbon\Carbon;

class CopyCatchmentsToEnrollments extends Command
{
    /**
     * Nombre y firma del comando.
     */
    protected $signature = 'catchments:copy {--modo=revision : Modo de ejecución: revision o insertar} {--proceso=todos : Procesos: enrollments, representantes, estudiantes, todos}';

    /**
     * Descripción del comando.
     */
    protected $description = 'Copia datos desde catchments a enrollments con filtros y mapeo de campos';

    /**
     * Mapeo de campos entre tablas.
     */
    protected $mapFields = [
        'firstname'   => 'name',
        'lastname'    => 'lastname',
        'date_birth'  => 'date_birth',
        'gender'      => 'gender',
        'origin'      => 'institution',
        'representant_ci'   => 'ci_representant',
        'representant_name' => 'name_representant',
        'relationship'      => 'relationship',
        'occupation'        => 'profession_representant',
        'representant_phone' => 'phone_representant',
        'representant_cellphone' => 'cellphone_representant',
        'email'             => 'email_representant',
        'direction'         => 'dir_address',
    ];

    /**
     * Ejecutar el comando.
     */
    public function handle()
    {
        $modo = $this->option('modo');
        $proceso = $this->option('proceso');
        
        $this->info("=== INICIANDO PROCESO EN MODO: {$modo} - PROCESO: {$proceso} ===");

        $totalProcesados = 0;
        $totalCoincidencias = 0;
        $totalInsertados = 0;
        $totalRepresentantesInsertados = 0;
        $totalEstudiantesInsertados = 0;
        $totalRepresentantesDuplicados = 0;
        $totalEstudiantesDuplicados = 0;

        // Ejecutar según el proceso solicitado
        if ($proceso === 'enrollments' || $proceso === 'todos') {
            $this->procesarEnrollments($modo, $totalProcesados, $totalCoincidencias, $totalInsertados);
        }

        if ($proceso === 'representantes' || $proceso === 'todos') {
            $this->procesarRepresentantes($modo, $totalRepresentantesInsertados, $totalRepresentantesDuplicados);
        }

        if ($proceso === 'estudiantes' || $proceso === 'todos') {
            $this->procesarEstudiantes($modo, $totalEstudiantesInsertados, $totalEstudiantesDuplicados);
        }

        // Resumen
        $this->mostrarResumen($modo, $proceso, $totalProcesados, $totalCoincidencias, $totalInsertados, 
                             $totalRepresentantesInsertados, $totalRepresentantesDuplicados, 
                             $totalEstudiantesInsertados, $totalEstudiantesDuplicados);
    }

    /**
     * Procesa los enrollments (proceso original)
     */
    private function procesarEnrollments($modo, &$totalProcesados, &$totalCoincidencias, &$totalInsertados)
    {
        $this->newLine();
        $this->info('🔄 Procesando ENROLLMENTS...');

        Catchment::query()
            ->join('catchment_interviews', 'catchment_interviews.catchment_id', '=', 'catchments.id')
            ->where('catchment_interviews.accepted', true)
            ->select('catchments.*', 'catchment_interviews.accepted')
            ->chunk(100, function ($catchments) use (&$totalProcesados, &$totalCoincidencias, &$totalInsertados, $modo) {
                foreach ($catchments as $c) {
                    $totalProcesados++;

                    // Revisar si ya existe en enrollments
                    $existe = Enrollment::where('name', $c->firstname)
                        ->where('lastname', $c->lastname)
                        ->where('date_birth', $c->date_birth)
                        ->exists();

                    if ($existe) {
                        $totalCoincidencias++;
                        if ($modo === 'revision') {
                            $this->line("[DUPLICADO ENROLLMENT] {$c->firstname} {$c->lastname} - {$c->date_birth}");
                        }
                        continue;
                    }

                    // Crear arreglo con el mapeo de campos
                    $nuevo = [];
                    foreach ($this->mapFields as $campoCatchment => $campoEnrollment) {
                        $nuevo[$campoEnrollment] = $c->$campoCatchment;
                    }

                    // Campos obligatorios por defecto
                    $nuevo['ci_estudiant'] = $c->token ?? 'SIN_CI';
                    $nuevo['pestudio_id'] = 1;
                    $nuevo['grado_id'] = 1;
                    $nuevo['grupo_estable_id'] = 1;
                    $nuevo['blood_type'] = 'O+';

                    if ($modo === 'revision') {
                        $this->info("[NUEVO ENROLLMENT] {$c->firstname} {$c->lastname} - {$c->date_birth}");
                    } elseif ($modo === 'insertar') {
                        Enrollment::create($nuevo);
                        $totalInsertados++;
                        $this->info("[ENROLLMENT INSERTADO] {$c->firstname} {$c->lastname} - {$c->date_birth}");
                    }
                }
            });
    }

    /**
     * Procesa TODOS los representantes desde catchment_interviews con accepted = true
     */
    private function procesarRepresentantes($modo, &$totalRepresentantesInsertados, &$totalRepresentantesDuplicados)
    {
        $this->newLine();
        $this->info('🔄 Procesando REPRESENTANTES de todos los catchments...');

        CatchmentInterview::query()
            ->where('accepted', true)
            ->chunk(100, function ($interviews) use (&$totalRepresentantesInsertados, &$totalRepresentantesDuplicados, $modo) {
                foreach ($interviews as $interview) {
                    $ciRepresentant = $interview->identification_number;
                    
                    // Validar si el representante ya existe
                    $representanteExiste = Representant::where('ci_representant', $ciRepresentant)->exists();
                    
                    if (!$representanteExiste) {
                        $datosRepresentante = [
                            'ci_representant' => $ciRepresentant,
                            'name' => $interview->full_name,
                            'phone' => $this->extraerTelefono($interview->phone_numbers),
                            'cellphone' => $this->extraerCelular($interview->phone_numbers),
                            'email' => $interview->email,
                            'status_active' => 'true',
                            'status_adviders' => 'false',
                            'status_blacklist' => 'false',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        if ($modo === 'revision') {
                            $this->line("[NUEVO REPRESENTANTE] CI: {$ciRepresentant} - {$interview->full_name}");
                        } elseif ($modo === 'insertar') {
                            Representant::create($datosRepresentante);
                            $totalRepresentantesInsertados++;
                            $this->info("[REPRESENTANTE INSERTADO] CI: {$ciRepresentant} - {$interview->full_name}");
                        }
                    } else {
                        $totalRepresentantesDuplicados++;
                        if ($modo === 'revision') {
                            $this->line("[REPRESENTANTE DUPLICADO] CI: {$ciRepresentant} - {$interview->full_name}");
                        }
                    }
                }
            });
    }

    /**
     * Procesa TODOS los estudiantes desde catchment_interviews con accepted = true
     */
    private function procesarEstudiantes($modo, &$totalEstudiantesInsertados, &$totalEstudiantesDuplicados)
    {
        $this->newLine();
        $this->info('🔄 Procesando ESTUDIANTES de todos los catchments...');

        CatchmentInterview::query()
            ->where('accepted', true)
            ->chunk(100, function ($interviews) use (&$totalEstudiantesInsertados, &$totalEstudiantesDuplicados, $modo) {
                foreach ($interviews as $interview) {
                    $ciEstudiant = $this->generarCIEstudiante($interview);
                    
                    // Validar si el estudiante ya existe
                    $estudianteExiste = Estudiant::where('ci_estudiant', $ciEstudiant)->exists();
                    
                    if (!$estudianteExiste) {
                        // Obtener el representante para asociarlo
                        $representante = Representant::where('ci_representant', $interview->identification_number)->first();
                        $representanteId = $representante ? $representante->id : 1111111111;

                        $datosEstudiante = [
                            'planpago_id' => 1,
                            'grado_inicial_id' => $interview->grade_year_aspiring ?? 1,
                            'seccion_inicial' => '1',
                            'type_ci_id' => 1,
                            'ci_estudiant' => $ciEstudiant,
                            'lastname' => $this->extraerApellidos($interview->student_full_name),
                            'name' => $this->extraerNombres($interview->student_full_name),
                            'gender' => $this->mapearGenero($interview->student_full_name),
                            'date_birth' => $interview->date_of_birth,
                            'representant_ci' => $interview->identification_number,
                            'representant_id' => $representanteId,
                            'status_active' => 'true',
                            'status_notice' => 1,
                            'status_blacklist' => 'false',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        if ($modo === 'revision') {
                            $this->line("[NUEVO ESTUDIANTE] CI: {$ciEstudiant} - {$interview->student_full_name}");
                        } elseif ($modo === 'insertar') {
                            Estudiant::create($datosEstudiante);
                            $totalEstudiantesInsertados++;
                            $this->info("[ESTUDIANTE INSERTADO] CI: {$ciEstudiant} - {$interview->student_full_name}");
                        }
                    } else {
                        $totalEstudiantesDuplicados++;
                        if ($modo === 'revision') {
                            $this->line("[ESTUDIANTE DUPLICADO] CI: {$ciEstudiant} - {$interview->student_full_name}");
                        }
                    }
                }
            });
    }

    /**
     * Muestra el resumen según los procesos ejecutados
     */
    private function mostrarResumen($modo, $proceso, $totalProcesados, $totalCoincidencias, $totalInsertados, 
                                  $totalRepresentantesInsertados, $totalRepresentantesDuplicados, 
                                  $totalEstudiantesInsertados, $totalEstudiantesDuplicados)
    {
        $this->newLine();
        $this->info('=== RESUMEN DEL PROCESO ===');
        $this->info("Modo: " . strtoupper($modo) . " | Proceso: " . strtoupper($proceso) . " | Fecha: " . now()->format('Y-m-d H:i:s'));
        
        $this->newLine();
        
        if ($proceso === 'enrollments' || $proceso === 'todos') {
            $this->info("📋 Enrollments: {$totalInsertados} insertados | {$totalCoincidencias} duplicados | {$totalProcesados} procesados");
        }
        
        if ($proceso === 'representantes' || $proceso === 'todos') {
            $totalRepProcesados = $totalRepresentantesInsertados + $totalRepresentantesDuplicados;
            $this->info("👥 Representantes: {$totalRepresentantesInsertados} insertados | {$totalRepresentantesDuplicados} duplicados | {$totalRepProcesados} procesados");
        }
        
        if ($proceso === 'estudiantes' || $proceso === 'todos') {
            $totalEstProcesados = $totalEstudiantesInsertados + $totalEstudiantesDuplicados;
            $this->info("🎓 Estudiantes: {$totalEstudiantesInsertados} insertados | {$totalEstudiantesDuplicados} duplicados | {$totalEstProcesados} procesados");
        }
        
        $totalInserciones = $totalInsertados + $totalRepresentantesInsertados + $totalEstudiantesInsertados;
        $totalDuplicados = $totalCoincidencias + $totalRepresentantesDuplicados + $totalEstudiantesDuplicados;
        
        $this->newLine();
        $this->info("📈 Resumen final: {$totalInserciones} nuevos registros | {$totalDuplicados} duplicados evitados");
        
        if ($modo === 'revision') {
            $this->warn('⚠️  MODO REVISIÓN - Para insertar use: --modo=insertar');
        } else {
            $this->info('✅ PROCESO COMPLETADO');
        }
        
        $this->newLine();
        $this->info('💡 Ejemplos de uso:');
        $this->line('   php artisan catchments:copy --modo=revision --proceso=representantes');
        $this->line('   php artisan catchments:copy --modo=insertar --proceso=estudiantes');
        $this->line('   php artisan catchments:copy --modo=insertar --proceso=todos');
    }

    /**
     * Extrae el primer teléfono de la cadena de teléfonos
     */
    private function extraerTelefono($telefonos)
    {
        if (empty($telefonos)) return null;
        
        $nums = explode(',', $telefonos);
        return trim($nums[0]) ?? null;
    }

    /**
     * Extrae el celular (segundo teléfono si existe, sino el primero)
     */
    private function extraerCelular($telefonos)
    {
        if (empty($telefonos)) return null;
        
        $nums = explode(',', $telefonos);
        return isset($nums[1]) ? trim($nums[1]) : trim($nums[0]);
    }

    /**
     * Genera una CI para el estudiante basada en datos disponibles
     */
    private function generarCIEstudiante($interview)
    {
        // Crear una CI temporal basada en fecha de nacimiento y hash del nombre
        $fecha = str_replace('-', '', $interview->date_of_birth);
        $hash = substr(md5($interview->student_full_name), 0, 4);
        return $fecha . $hash;
    }

    /**
     * Extrae los apellidos del nombre completo (últimas dos palabras)
     */
    private function extraerApellidos($nombreCompleto)
    {
        if (empty($nombreCompleto)) return null;
        
        $partes = explode(' ', trim($nombreCompleto));
        if (count($partes) <= 2) {
            return isset($partes[1]) ? $partes[1] : '';
        }
        
        // Tomar las últimas dos partes como apellidos
        return implode(' ', array_slice($partes, -2));
    }

    /**
     * Extrae los nombres del nombre completo (primeras palabras, excluyendo apellidos)
     */
    private function extraerNombres($nombreCompleto)
    {
        if (empty($nombreCompleto)) return null;
        
        $partes = explode(' ', trim($nombreCompleto));
        if (count($partes) <= 2) {
            return $partes[0];
        }
        
        // Tomar todas menos las últimas dos partes
        return implode(' ', array_slice($partes, 0, -2));
    }

    /**
     * Mapea el género basado en el nombre (lógica básica, se puede mejorar)
     */
    private function mapearGenero($nombreCompleto)
    {
        // Lógica básica - en un caso real se podría usar una librería o base de datos de nombres
        $nombre = strtolower($this->extraerNombres($nombreCompleto));
        
        // Nombres femeninos comunes que terminan en 'a'
        if (str_ends_with($nombre, 'a') || 
            str_contains($nombre, 'maria') || 
            str_contains($nombre, 'ana') ||
            str_contains($nombre, 'lucia') ||
            str_contains($nombre, 'sofia')) {
            return 'Femenino';
        }
        
        // Por defecto masculino (se puede ajustar según necesidades)
        return 'Masculino';
    }
}
