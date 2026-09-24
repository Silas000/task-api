<?php

namespace App\GraphQL\Mutations;

use App\Services\TaskService;

class CreateTask
{
    public function __construct(private TaskService $taskService) {}

    public function __invoke($_, array $args)
    {
        $user = auth()->user();

        return $this->taskService->create($user, $args);
    }
}