<?php

namespace Database\Factories;

use App\Models\CicloFormativo;
use App\Models\Modulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Modulo>
 */
class ModuloFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ciclo_formativo_id' => fn() => CicloFormativo::factory()->create()->id,
            'nombre' => $this->faker->sentence(3),
            'codigo' => $this->faker->unique()->bothify('####'),
            'horas_totales' => $this->faker->numberBetween(30, 200),
            'descripcion' => $this->faker->paragraph(),
        ];
    }
}
