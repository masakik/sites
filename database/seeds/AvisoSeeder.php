<?php

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

        App\Aviso::create($entrada);

        factory(App\Aviso::class, 50)->create();
    }
}
