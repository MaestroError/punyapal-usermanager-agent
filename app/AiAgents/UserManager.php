<?php

namespace App\AiAgents;

use LarAgent\Agent;
use LarAgent\Attributes\Tool;
use App\Services\UserService\UserService;
use App\Services\UserService\UserServiceInterface;

class UserManager extends Agent
{
    protected $model = 'gpt-4o-mini';

    protected $history = 'session';

    protected $provider = 'default';

    protected $tools = [];

    protected UserServiceInterface $userService;

    public function __construct($key)
    {
        parent::__construct($key);
        $this->userService = app(UserService::class);
    }

    public function instructions()
    {
        $tools = $this->getTools();
        return view('prompts.user_manager.instructions', compact('tools'));
    }

    public function prompt($message)
    {
        return $message;
    }

    #[Tool("Retrive overall count of users")]
    public function countUsers()
    {
        return $this->userService->countUsers();
    }

    #[Tool("Get specific user data based on the user identificator", [
        'identifier' => 'User ID or email (Prefer to use ID if possible)',
    ])]
    public function getSpecificUser(string $identifier)
    {
        return $this->userService->findUser($identifier);
    }

}
