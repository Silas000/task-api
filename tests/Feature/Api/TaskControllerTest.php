<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nao_autenticado_recebe_401(): void
    {
        $this->getJson('/api/tasks')->assertStatus(401);
    }

    public function test_usuario_autenticado_pode_listar_suas_tarefas(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->for($user)->create();

        // Cria outro usuário com tarefas (não deve aparecer)
        $outro = User::factory()->create();
        Task::factory()->count(5)->for($outro)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_usuario_pode_criar_tarefa(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tasks', [
            'title'       => 'Minha tarefa',
            'description' => 'Descrição de teste',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Minha tarefa')
            ->assertJsonPath('data.completed', false);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title'   => 'Minha tarefa',
        ]);
    }

    public function test_criar_tarefa_sem_titulo_retorna_422(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/tasks', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_usuario_pode_ver_sua_propria_tarefa(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_usuario_nao_pode_ver_tarefa_de_outro(): void
    {
        $user  = User::factory()->create();
        $outro = User::factory()->create();
        $task  = Task::factory()->for($outro)->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/tasks/{$task->id}")
            ->assertStatus(403);
    }

    public function test_usuario_pode_atualizar_sua_tarefa(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->pending()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title'     => 'Atualizada',
            'completed' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Atualizada')
            ->assertJsonPath('data.completed', true);

        $this->assertDatabaseHas('tasks', [
            'id'        => $task->id,
            'title'     => 'Atualizada',
            'completed' => true,
        ]);
    }

    public function test_usuario_pode_deletar_sua_tarefa(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_usuario_nao_pode_deletar_tarefa_de_outro(): void
    {
        $user  = User::factory()->create();
        $outro = User::factory()->create();
        $task  = Task::factory()->for($outro)->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(422); // ValidationException do Service

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}