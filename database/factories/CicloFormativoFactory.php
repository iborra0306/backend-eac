<?php

namespace Database\Factories;

use App\Models\CicloFormativo;
use App\Models\FamiliaProfesional;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CicloFormativo>
 */
class CicloFormativoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'familia_profesional_id' => fn() => FamiliaProfesional::factory()->create()->id,
            'nombre' => $this->faker->sentence(3),
            'codigo' => $this->faker->unique()->bothify('####'),
            'grado' => $this->faker->randomElement(['GB', 'GM', 'GS']),
            'descripcion' => $this->faker->paragraph(),
        ];
    }
}
