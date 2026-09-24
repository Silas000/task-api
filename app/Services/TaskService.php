<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function listForUser(User $user)
    {
        return $user->tasks()->latest()->get();
    }

    public function create(User $user, array $data): Task
    {
        return $user->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'completed' => $data['completed'] ?? false,
        ]);
    }

    public function update(User $user, Task $task, array $data): Task
    {
        // Verificação de propriedade (autorização)
        if ($task->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'task' => 'Você não tem permissão para editar esta tarefa.'
            ]);
        }

        $task->update(array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'completed' => $data['completed'] ?? null,
        ], fn($value) => !is_null($value)));

        return $task->fresh();
    }

    public function delete(User $user, Task $task): bool
    {
        if ($task->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'task' => 'Você não tem permissão para excluir esta tarefa.'
            ]);
        }

        return $task->delete();
    }
}