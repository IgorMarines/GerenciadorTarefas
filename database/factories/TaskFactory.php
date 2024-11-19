<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gerar um nome aleatório para a tarefa
        $status = [
            'pending',
            'in_progress',
            'blocked',
            'completed',
        ];

        return [
            'title' => $this->faker->sentence,  // Gerar um nome de tarefa aleatório
            'description' => $this->faker->paragraph,  // Gerar uma descrição aleatória
            'user_id' => User::all()->random()->id, // user id randomico entre os usuarios existentes
            'status' => $this->faker->randomElement($status),  // Selecionar um status aleatório
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
