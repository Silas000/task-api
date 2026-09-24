<?php

namespace App\GraphQL\Mutations;

use App\Models\Task;
use App\Services\TaskService;

class DeleteTask
{
    public function __construct(private TaskService $taskService) {}

    public function __invoke($_, array $args)
    {
        $user = auth()->user();
        $task = Task::findOrFail($args['id']);

        $this->taskService->delete($user, $task);

        return $task;
    }
}