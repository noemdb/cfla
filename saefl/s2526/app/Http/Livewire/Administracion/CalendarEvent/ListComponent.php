<?php

namespace App\Http\Livewire\Administracion\CalendarEvent;

use App\Http\Livewire\Administracion\CalendarEvent\CalendarEventTrait;
use App\Models\app\Planpago\CalendarEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListComponent extends Component
{
    use WithPagination;
    use CalendarEventTrait;

    protected $paginationTheme = 'bootstrap-4';
    public CalendarEvent $calendar_event;
    public $iconosDisponibles = [];

    // ↓ REEMPLAZAR VARIABLES DE MODO POR BANDERAS DE MODAL
    public $modalCreate = false;
    public $modalEdit = false;
    public $modalShow = false;
    
    public $list_comment;
    public $calendar_event_id;
    public $search = '',$paginate=10;

    protected $listeners = ['showSwal','alertConfirm','alertQuestion','remove'];

    public function mount()
    {
        $this->calendar_event = new CalendarEvent;
        $this->closeModals(); // ← INICIALIZAR TODOS LOS MODALES CERRADOS
        $this->list_comment = CalendarEvent::COLUMN_COMMENTS;
        $this->iconosDisponibles = CalendarEvent::ICONS_LIST;
    }

    // ↓ NUEVO MÉTODO PARA CERRAR TODOS LOS MODALES
    public function closeModals()
    {
        $this->modalCreate = false;
        $this->modalEdit = false;
        $this->modalShow = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // ↓ REEMPLAZAR MÉTODO CLOSE
    public function close()
    {
        $this->closeModals();
        $this->calendar_event = new CalendarEvent;
        $this->resetPage();
    }

    public function render()
    {
        $calendar_events = CalendarEvent::paginate($this->paginate);
        return view('livewire.administracion.calendar-event.list-component', compact('calendar_events'));
    }

    public function create()
    {
        $this->closeModals();
        $this->modalCreate = true;
        $this->calendar_event = new CalendarEvent;
    }

    public function edit($id)
    {
        $this->closeModals();
        $calendar_event = CalendarEvent::findOrFail($id);
        $this->calendar_event = $calendar_event;
        $this->calendar_event_id = $calendar_event->id;
        $this->modalEdit = true;
    }

    public function save()
    {
        //dd($this->calendar_event->status_holidays);
        $data = $this->validate();
        
        DB::transaction(function () {
            $this->calendar_event->user_id = Auth::id();
            $this->calendar_event->save();
            $this->calendar_event_id = $this->calendar_event->id;
        });

        $this->closeModals();

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
            'toast' => false,
            'position' => 'center',
        ]);
    }

    // ↓ ELIMINAR MÉTODO modeIndex() YA NO ES NECESARIO

    public function remove($id)
    {
        DB::transaction(function () use ($id) {
            $calendar_event = CalendarEvent::findOrFail($id);
            
            if ($calendar_event->status_delete) {
                throw new \Exception('No se puede eliminar un evento con fecha pasada o actual.');
            }
            
            $calendar_event->delete();
        });

        $this->closeModals(); // ← CERRAR MODALES SI ESTÁN ABIERTOS

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);
    }

    public function alertConfirm($id)
    {
        try {
            $calendar_event = CalendarEvent::findOrFail($id);
            
            if ($calendar_event->status_delete) {
                $this->showSwal(
                    'No se puede eliminar', 
                    'No es posible eliminar eventos con fecha pasada o actual.', 
                    'error'
                );
                return;
            }

            $this->dispatchBrowserEvent('swal:confirm', [
                'type' => 'question',
                'message' => '¿Estás seguro?',
                'text' => 'Si realizas esta operación, no la podrás revertir',
                'id' => $calendar_event->id
            ]);

        } catch (\Exception $e) {
            $this->showSwal('Error', $e->getMessage(), 'error');
        }
    }

    // ↓ NUEVO MÉTODO PARA MOSTRAR DETALLES
    public function show($id)
    {
        $this->closeModals();
        $calendar_event = CalendarEvent::findOrFail($id);
        $this->calendar_event = $calendar_event;
        $this->modalShow = true;
    }

    protected function handleTransactionError(\Exception $e, $operation = 'operación')
    {
        $errorMessage = "Error durante la {$operation}: " . $e->getMessage();
        
        \Log::error($errorMessage, [
            'user_id' => Auth::id(),
            'operation' => $operation,
            'exception' => $e
        ]);

        $this->showSwal('Error', 'Ocurrió un error inesperado. Por favor, intente nuevamente.', 'error');
    }
}