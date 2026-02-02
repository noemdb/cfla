<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVotingPollRequest;
use App\Http\Requests\UpdateVotingPollRequest;
use App\Models\VotingPoll;
use Illuminate\Http\Request;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Exception;
use Illuminate\Support\Facades\Log;

class VotingPollController extends Controller
{
    // Remover cualquier middleware de autorización por ahora
    public function __construct()
    {
        // Sin middleware de autorización por el momento
    }

    public function index()
    {
        try {
            $polls = VotingPoll::with('options')
                ->withVotesCount()
                ->paginate(10);

            Log::info('Admin polls index accessed', [
                'polls_count' => $polls->count(),
                'ip' => request()->ip()
            ]);

            return view('admin.voting.polls.index', compact('polls'));

        } catch (Exception $e) {
            Log::error('Error loading admin polls index: ' . $e->getMessage(), [
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al cargar las encuestas: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function create()
    {
        try {
            Log::info('Admin poll create page accessed', [
                'ip' => request()->ip()
            ]);

            return view('admin.voting.polls.create');

        } catch (Exception $e) {
            Log::error('Error loading poll create page: ' . $e->getMessage());

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al cargar la página de creación.',
                    'icon' => 'error'
                ]);
        }
    }

    public function store(StoreVotingPollRequest $request)
    {
        try {
            $poll = VotingPoll::create($request->validated());

            // Crear opciones
            foreach ($request->options as $optionData) {
                $poll->options()->create([
                    'label' => $optionData['label']
                ]);
            }

            Log::info('Poll created successfully', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'options_count' => count($request->options),
                'created_by_ip' => request()->ip()
            ]);

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => '¡Éxito!',
                    'description' => 'Encuesta creada exitosamente.',
                    'icon' => 'success'
                ]);

        } catch (\Exception $e) {
            Log::error('Error creating poll: ' . $e->getMessage(), [
                'request_data' => $request->validated(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al crear la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function show(VotingPoll $poll)
    {
        try {
            $poll->load(['options.votes', 'sessions']);

            Log::info('Poll details viewed', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'ip' => request()->ip()
            ]);

            return view('admin.voting.polls.show', compact('poll'));

        } catch (Exception $e) {
            Log::error('Error loading poll details: ' . $e->getMessage(), [
                'poll_id' => $poll->id ?? 'unknown'
            ]);

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al cargar los detalles de la encuesta.',
                    'icon' => 'error'
                ]);
        }
    }

    public function edit(VotingPoll $poll)
    {
        try {
            $poll->load('options');

            Log::info('Poll edit page accessed', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'ip' => request()->ip()
            ]);

            return view('admin.voting.polls.edit', compact('poll'));

        } catch (Exception $e) {
            Log::error('Error loading poll edit page: ' . $e->getMessage(), [
                'poll_id' => $poll->id ?? 'unknown'
            ]);

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al cargar la página de edición.',
                    'icon' => 'error'
                ]);
        }
    }

    public function update(UpdateVotingPollRequest $request, VotingPoll $poll)
    {
        try {
            $oldTitle = $poll->title;
            $poll->update($request->validated());

            // Actualizar opciones
            $poll->options()->delete();
            foreach ($request->options as $optionData) {
                $poll->options()->create([
                    'label' => $optionData['label']
                ]);
            }

            Log::info('Poll updated successfully', [
                'poll_id' => $poll->id,
                'old_title' => $oldTitle,
                'new_title' => $poll->title,
                'options_count' => count($request->options),
                'updated_by_ip' => request()->ip()
            ]);

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => '¡Actualizado!',
                    'description' => 'Encuesta actualizada exitosamente.',
                    'icon' => 'success'
                ]);

        } catch (\Exception $e) {
            Log::error('Error updating poll: ' . $e->getMessage(), [
                'poll_id' => $poll->id,
                'request_data' => $request->validated(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al actualizar la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function destroy(VotingPoll $poll)
    {
        try {
            $pollTitle = $poll->title;
            $pollId = $poll->id;

            $poll->delete();

            Log::info('Poll deleted successfully', [
                'poll_id' => $pollId,
                'poll_title' => $pollTitle,
                'deleted_by_ip' => request()->ip()
            ]);

            return redirect()->route('admin.voting.dashboard')
                ->with('wireui.notification', [
                    'title' => '¡Eliminado!',
                    'description' => "Encuesta \"{$pollTitle}\" eliminada exitosamente.",
                    'icon' => 'success'
                ]);

        } catch (\Exception $e) {
            Log::error('Error deleting poll: ' . $e->getMessage(), [
                'poll_id' => $poll->id ?? 'unknown',
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al eliminar la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function start(VotingPoll $poll)
    {
        try {
            $poll->update([
                'enable' => true,
                'date' => now(),
            ]);

            Log::info('Poll started successfully', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'started_at' => now(),
                'started_by_ip' => request()->ip()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => '¡Encuesta iniciada!',
                    'description' => "La encuesta \"{$poll->title}\" está ahora activa y recibiendo votos.",
                    'icon' => 'success'
                ]);

        } catch (\Exception $e) {
            Log::error('Error starting poll: ' . $e->getMessage(), [
                'poll_id' => $poll->id,
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al iniciar la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function stop(VotingPoll $poll)
    {
        try {
            $poll->update(['enable' => false]);

            Log::info('Poll stopped successfully', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'stopped_at' => now(),
                'stopped_by_ip' => request()->ip()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => '¡Encuesta detenida!',
                    'description' => "La encuesta \"{$poll->title}\" ha sido detenida y ya no acepta votos.",
                    'icon' => 'info'
                ]);

        } catch (\Exception $e) {
            Log::error('Error stopping poll: ' . $e->getMessage(), [
                'poll_id' => $poll->id,
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al detener la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function reset(VotingPoll $poll)
    {
        try {
            $pollTitle = $poll->title;
            $voteCount = $poll->votes()->count();

            // Detener la encuesta primero
            $poll->update([
                'enable' => false,
                'date' => null,
            ]);

            // Obtener todas las sesiones relacionadas con esta encuesta
            $sessions = VotingSession::where('poll_id', $poll->id)->get();

            // Eliminar todos los votos relacionados con estas sesiones
            foreach ($sessions as $session) {
                VotingVote::where('session_uuid', $session->uuid)->delete();
            }

            // Eliminar todas las sesiones de esta encuesta
            VotingSession::where('poll_id', $poll->id)->delete();

            Log::info('Poll reset successfully', [
                'poll_id' => $poll->id,
                'poll_title' => $pollTitle,
                'votes_deleted' => $voteCount,
                'sessions_deleted' => $sessions->count(),
                'reset_by_ip' => request()->ip()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => '¡Encuesta reiniciada!',
                    'description' => "La encuesta \"{$pollTitle}\" ha sido reiniciada. Se eliminaron {$voteCount} votos y todas las sesiones.",
                    'icon' => 'warning'
                ]);

        } catch (Exception $e) {
            Log::error('Error resetting poll: ' . $e->getMessage(), [
                'poll_id' => $poll->id,
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('wireui.notification', [
                    'title' => 'Error',
                    'description' => 'Error al reiniciar la encuesta: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function results()
    {
        $polls = VotingPoll::with(['options.votes'])
            ->withVotesCount()
            ->get();
        return view('admin.voting.polls.results', compact('polls'));
    }

    public function publicList()
    {
        $polls = VotingPoll::where('enable', true)
            ->with('options')
            ->withVotesCount()
            ->get();

        return view('admin.voting.polls.list', compact('polls'));
    }
}
