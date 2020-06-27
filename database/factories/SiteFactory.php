<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Site;
use Faker\Generator as Faker;
use App\User;

$factory->define(Site::class, function (Faker $faker) {

    $owner = [
        '5385361',
        '2517070',
        '3426504'
    ];
    $categoria = [
        'Grupo de estudo',
        'Grupo de pesquisa',
        'Departamento',
        'Administrativo', 
        'Centro',
        'Associação',
        'Laboratório',
        'Comissão',
        'Evento',
        'Programa de Pós-Graduação'
    ];

    return [
        'dominio' => $faker->unique()->word,
        'numeros_usp' => $owner[array_rand($owner)],  
        'owner' =>  $faker->unique()->docente(), 
        'status' => 'Aprovado',
        'categoria' => $categoria[array_rand($categoria)], 
        'justificativa' => $faker->sentence,
    ];
});
