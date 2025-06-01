<?php

namespace App\AiAgents;

use LarAgent\Agent;
use LarAgent\Attributes\Tool;
use App\Services\UserService\UserService;
use App\Enums\SubscriptionType;

class UserManager extends Agent
{
    protected $model = 'gpt-4o';

    protected $history = 'session';

    protected $provider = 'default';

    protected $tools = [];

    protected $isAdmin = false;

    protected UserService $userService;

    public function __construct($key)
    {
        parent::__construct($key);
        $this->userService = new UserService();
    }

    public function instructions()
    {
        $tools = $this->getTools();
        return view('prompts.user_manager.instructions', compact('tools'));
    }

    public function isAdmin(bool $isAdmin = true)
    {
        $this->isAdmin = $isAdmin;
    }

    public function prompt($message)
    {

        return $message;
    }

    #[Tool("Returns a amount of all users")]
    public function countUsers()
    {
        return $this->userService->countUsers();
    }

    #[Tool("Find the user data base on the identifier", [
        'identifier' => 'User ID or the email (Prefer ID if possible)'
    ])]
    public function findUser(string $identifier)
    {
        return $this->userService->findUser($identifier);
    }

    #[Tool("Change the subscription type of the user", [
        'identifier' => 'User ID or the email (Prefer ID if possible)',
        'subscriptionType' => 'Subscription type'
    ])]
    public function changeSubscription(string $identifier, SubscriptionType $subscriptionType)
    {
        return $this->userService->changeSubscription($identifier, $subscriptionType);
    }
}
