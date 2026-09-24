<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    /**
     * Lista todas as tarefas do usuário autenticado.
     *
     * Retorna uma coleção padronizada via TaskCollection.
     */
    public function index(Request $request): TaskCollection
    {
        $tasks = $this->taskService->listForUser($request->user());

        return new TaskCollection($tasks);
    }

    /**
     * Cria uma nova tarefa para o usuário autenticado.
     *
     * Valida os dados de entrada e delega a criação ao TaskService.
     * Retorna 201 (Created) com o recurso criado.
     */
    public function store(Request $request): TaskResource
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed'   => 'boolean',
        ]);

        $task = $this->taskService->create($request->user(), $validated);

        // Carrega a relação "user" para o Resource não fazer lazy load
        $task->load('user');

        return new TaskResource($task);
    }

    /**
     * Exibe uma tarefa específica (apenas se pertencer ao usuário autenticado).
     *
     * Retorna 403 (Forbidden) caso a tarefa pertença a outro usuário.
     */
    public function show(Request $request, Task $task): TaskResource|JsonResponse
    {
        // Verificação de propriedade (ownership)
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Carrega a relação "user" para o Resource
        $task->load('user');

        return new TaskResource($task);
    }

    /**
     * Atualiza uma tarefa existente (apenas se pertencer ao usuário autenticado).
     *
     * A validação de propriedade é feita dentro do TaskService,
     * que lança ValidationException se o usuário não for o dono.
     */
    public function update(Request $request, Task $task): TaskResource
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'completed'   => 'boolean',
        ]);

        $task = $this->taskService->update($request->user(), $task, $validated);

        // Recarrega a relação "user" após a atualização
        $task->load('user');

        return new TaskResource($task);
    }

    /**
     * Remove uma tarefa (apenas se pertencer ao usuário autenticado).
     *
     * Retorna 204 (No Content) em caso de sucesso.
     * A validação de propriedade é feita dentro do TaskService.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->taskService->delete($request->user(), $task);

        return response()->json(null, 204);
    }
}