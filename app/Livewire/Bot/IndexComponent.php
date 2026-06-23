<?php

namespace App\Livewire\Bot;

use App\Services\Bot\AutoresponderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.bot')]
class IndexComponent extends Component
{
    public array $messages = [];
    public string $newMessage = '';
    public bool $isTyping = false;

    protected AutoresponderService $botService;

    public function boot(AutoresponderService $botService): void
    {
        $this->botService = $botService;
    }

    public function mount(): void
    {
        $this->messages[] = [
            'type' => 'bot',
            'text' => $this->botService->getMenu(),
            'time' => now()->format('H:i'),
        ];
    }

    public function sendMessage(): void
    {
        $message = trim($this->newMessage);

        if (empty($message)) {
            return;
        }

        // Agregar mensaje del usuario al chat
        $this->messages[] = [
            'type' => 'user',
            'text' => $message,
            'time' => now()->format('H:i'),
        ];

        $this->newMessage = '';
        $this->isTyping = true;

        // Force a re-render to show the typing indicator
        $this->dispatch('$refresh');

        // Process the response via the saefl API
        $this->processResponse($message);
    }

    protected function processResponse(string $message): void
    {
        $response = $this->botService->main($message);

        $this->isTyping = false;

        $this->messages[] = [
            'type' => 'bot',
            'text' => $response,
            'time' => now()->format('H:i'),
        ];
    }

    public function render()
    {
        return view('livewire.bot.index-component');
    }
}
