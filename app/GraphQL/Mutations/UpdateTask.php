<?php

namespace App\GraphQL\Mutations;

use App\Models\Task;
use App\Services\TaskService;

class UpdateTask
{
    public function __construct(private TaskService $taskService) {}

    public function __invoke($_, array $args)
    {
        $user = auth()->user();
        $task = Task::findOrFail($args['id']);

        // Remove o 'id' do array antes de passar para o service
        $input = $args;
        unset($input['id']);

        return $this->taskService->update($user, $task, $input);
    }
}