<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'completed'   => fake()->boolean(30),
        ];
    }

    /**
     * Estado: tarefa concluída
     */
    public function completed(): static
    {
        return $this->state(fn () => ['completed' => true]);
    }

    /**
     * Estado: tarefa pendente
     */
    public function pending(): static
    {
        return $this->state(fn () => ['completed' => false]);
    }
}