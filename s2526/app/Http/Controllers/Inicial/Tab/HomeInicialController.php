<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Lapso;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeInicialController extends Controller
{
    public $user, $autoridad, $list_comment_autoridad;

    public function __construct()
    {
        $this->middleware(['auth','is_inicial', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function home()
    {
        $user = $this->user;
        $autoridad = $this->autoridad;
        $list_comment_autoridad = $this->list_comment_autoridad;

        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();

        return view('inicials.home', compact('user','autoridad','list_comment_autoridad','lapsos','lapso_active'));
    }

    public function useCases()
    {
        $user = $this->user;
        $autoridad = $this->autoridad;
        $list_comment_autoridad = $this->list_comment_autoridad;

        // Datos para los casos de uso
        $useCases = $this->getUseCasesData();

        return view('inicials.use-cases', compact('user','autoridad','list_comment_autoridad','useCases'));
    }

    private function getUseCasesData()
    {
        return [
            [
                'id' => 'authentication',
                'title' => 'Autenticación de Usuarios',
                'description' => 'Los docentes inician sesión con credenciales seguras para acceder al sistema.',
                'icon' => 'fas fa-lock',
                'color' => 'primary'
            ],
            [
                'id' => 'weekly-planning',
                'title' => 'Gestión de Planificación Semanal',
                'description' => 'Crear, editar, eliminar y visualizar planes semanales de clase.',
                'icon' => 'fas fa-calendar-week',
                'color' => 'success'
            ],
            [
                'id' => 'classroom-projects',
                'title' => 'Gestión de Proyectos de Aula',
                'description' => 'Registrar y gestionar proyectos pedagógicos desarrollados durante el año escolar.',
                'icon' => 'fas fa-project-diagram',
                'color' => 'info'
            ],
            [
                'id' => 'evaluations',
                'title' => 'Gestión de Evaluaciones',
                'description' => 'Registrar actividades de evaluación y seguimiento del desempeño estudiantil.',
                'icon' => 'fas fa-clipboard-check',
                'color' => 'warning'
            ],
            [
                'id' => 'pedagogical-reports',
                'title' => 'Generación de Informes Pedagógicos',
                'description' => 'Generar informes finales sobre el progreso de los estudiantes.',
                'icon' => 'fas fa-file-alt',
                'color' => 'secondary'
            ],
            [
                'id' => 'special-reports',
                'title' => 'Administración de Informes Especiales',
                'description' => 'Gestionar eventos o situaciones particulares que requieren atención especial.',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger'
            ],
            [
                'id' => 'export-print',
                'title' => 'Exportar o Imprimir Formatos',
                'description' => 'Descargar o imprimir documentos en formato PDF listos para presentar oficialmente.',
                'icon' => 'fas fa-print',
                'color' => 'dark'
            ]
        ];
    }
}
