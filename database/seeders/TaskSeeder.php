<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 5 usuários, cada um com 3 tarefas
        User::factory(5)
            ->has(Task::factory()->count(3))
            ->create();
    }
}