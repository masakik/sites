<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

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
            'justificativa' => 'Seed de Site de Divulgação',
            'config' => ['manager' => 'aegir'],
        ];

        Site::create($site);

        Site::factory()->count(20)->create();
    }
}
