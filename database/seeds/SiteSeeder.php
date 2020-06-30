<?php

use Illuminate\Database\Seeder;
use App\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criando Site
        $site = [
            'dominio' => 'teste',
            'numeros_usp' => 5385361,
            'owner' => 11170411,
            'status' => 'Solicitado',
            'categoria' => 'Grupo de Estudos',
            'justificativa' => 'Divulgacao'
        ];

        Site::create($site);

        factory(Site::class, 20)->create();
    }
}
