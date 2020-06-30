<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Site;
use Faker\Generator as Faker;
use App\User;

$factory->define(Site::class, function (Faker $faker) {
    $categorias = Site::categorias();

    return [
        'dominio' => $faker->unique()->word,
        'numeros_usp' => $faker->unique()->docente(),  
        'owner' =>  $faker->unique()->docente(), 
        'status' => 'Aprovado',
        'categoria' => $categorias[array_rand($categorias)], 
        'justificativa' => $faker->sentence,
    ];
});
