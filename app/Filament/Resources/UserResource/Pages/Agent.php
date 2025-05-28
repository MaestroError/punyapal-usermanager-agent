<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use App\AiAgents\UserManager;
use LarAgent\Agent as AgentClass;

class Agent extends Page
{
    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.user-resource.pages.agent';
    
    // Navigation Settings
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Agent';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;
    
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }
    
    public static function getNavigationBadge(): ?string
    {
        return 'New';
    }

    public ?array $chatHistory = [
        [
            'role' => 'system',
            'content' => 'Name yourself on every response, for example: \'I, Model [X] created by [Y], sending respond: [response]\''
        ]
    ];
    public ?string $message = '';

    public function mount(): void
    {
        $this->form->fill();
        $this->setChatHistoryFromAgent();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Chat')
                    ->schema([
                        Textarea::make('message')
                            ->label('Message')
                            ->placeholder('Type your message here...')
                            ->rows(2)
                            ->required(),
                    ])
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Send')
                ->action('sendMessage')
                ->color('primary'),
            Action::make('clear')
                ->label('Clear History')
                ->action('clearHistory')
                ->color('danger')
                ->requiresConfirmation(),
        ];
    }

    public function sendMessage(): void
    {
        $this->validateOnly('message');
        
        // Add user message to chat history
        $this->getAgentInstace()->respond($this->message);

        $this->setChatHistoryFromAgent();

        // @todo Try to implement streaming with wire-stream: https://livewire.laravel.com/docs/wire-stream

        $this->reset('message');
    }

    public function clearHistory(): void
    {
        $this->chatHistory = [];
        
        $this->getAgentInstace()->clear();

        $this->setChatHistoryFromAgent();

        Notification::make()
            ->title('Chat history cleared')
            ->success()
            ->send();
    }

    public function getChatHistory(): array
    {
        return array_filter($this->chatHistory, fn($message) => in_array($message['role'], ['user', 'assistant']));
    }

    public function getSystemMessages(): array
    {
        return array_filter($this->chatHistory, fn($message) => !in_array($message['role'], ['user', 'assistant']));
    }

    protected function setChatHistoryFromAgent(): void {
        $this->chatHistory = $this->getAgentInstace()->chatHistory()->toArray();
    }

    protected function getAgentInstace(): AgentClass {
        return UserManager::forUser(auth()->user());
    }
}
