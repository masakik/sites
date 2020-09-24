<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AvisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entrada = [
            'titulo' => 'Exemplo de mensagem de um aviso na home page',
            'corpo' => 'Exemplo de corpo de mensagem: site em manutenção',
            'divulgacao_home_ate' => '2020-12-20',
        ];

        App\Models\Aviso::create($entrada);

        factory(App\Models\Aviso::class, 50)->create();
    }
}
