<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVotingPollRequest;
use App\Http\Requests\UpdateVotingPollRequest;
use App\Models\VotingPoll;
use Illuminate\Http\Request;

class VotingPollController extends Controller
{
    // Remover cualquier middleware de autorización por ahora
    public function __construct()
    {
        // Sin middleware de autorización por el momento
    }

    public function index()
    {
        $polls = VotingPoll::with('options')
        ->withVotesCount()
        ->paginate(10);

        return view('admin.voting.polls.index', compact('polls'));
    }

    public function create()
    {
        return view('admin.voting.polls.create');
    }

    public function store(StoreVotingPollRequest $request)
    {
        try {
            $poll = VotingPoll::create($request->validated());

            // Crear opciones
            foreach ($request->options as $optionData) {
                $poll->options()->create(['label' => $optionData['label']]);
            }

            return redirect()->route('admin.voting.dashboard')
                ->with('success', 'Encuesta creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la encuesta: ' . $e->getMessage());
        }
    }

    public function show(VotingPoll $poll)
    {
        $poll->load(['options.votes', 'sessions']);
        return view('admin.voting.polls.show', compact('poll'));
    }

    public function edit(VotingPoll $poll)
    {
        $poll->load('options');
        return view('admin.voting.polls.edit', compact('poll'));
    }

    public function update(UpdateVotingPollRequest $request, VotingPoll $poll)
    {
        try {
            $poll->update($request->validated());

            // Actualizar opciones
            $poll->options()->delete();
            foreach ($request->options as $optionData) {
                $poll->options()->create(['label' => $optionData['label']]);
            }

            return redirect()->route('admin.voting.dashboard')
                ->with('success', 'Encuesta actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la encuesta: ' . $e->getMessage());
        }
    }

    public function destroy(VotingPoll $poll)
    {
        try {
            $poll->delete();
            return redirect()->route('admin.voting.dashboard')
                ->with('success', 'Encuesta eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la encuesta: ' . $e->getMessage());
        }
    }

    public function start(VotingPoll $poll)
    {
        try {
            $poll->update([
                'enable' => true,
                'date' => now(),
            ]);

            return redirect()->back()->with('success', 'Encuesta iniciada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al iniciar la encuesta: ' . $e->getMessage());
        }
    }

    public function stop(VotingPoll $poll)
    {
        try {
            $poll->update(['enable' => false]);
            return redirect()->back()->with('success', 'Encuesta detenida exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al detener la encuesta: ' . $e->getMessage());
        }
    }
}
