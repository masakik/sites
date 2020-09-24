<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categorias = Site::categorias();
        $status = Site::status();
        return [
            'dominio' => $this->faker->unique()->word,
            'numeros_usp' => $this->faker->unique()->docente(),
            'owner' =>  $this->faker->unique()->docente(),
            'status' => $status[array_rand($status)],
            'categoria' => $categorias[array_rand($categorias)],
            'justificativa' => $this->faker->sentence,
        ];
    }
}
