<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;

class clonaSiteAegir implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $alvo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($alvo)
    {
        $this->alvo = $alvo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $alvo = $this->alvo;
      $site_modelo = env('SITE_MODELO');
      $id_node_bd = env('ID_NODE_BD');
      $id_node_plataforma = env('ID_NODE_PLATAFORMA');
      //$cliente_email = \Auth::user()->email;
      //$cliente_nome = \Auth::user()->name;

      $client = new Client([
           'base_uri' => 'http://aegir.fflch.usp.br'
      ]);

      $res = $client->request('POST','/aegir/saas/task/', [
          'form_params' => [
              'target' => $site_modelo,
              'type' => 'clone', 
              'options[new_uri]' => $alvo,
              'options[database]' => $id_node_bd,
              'options[target_platform]' => $id_node_plataforma,
              /*'options[client_email]' => $cliente_email,
              'options[client_name]' => $cliente_nome,*/
              'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
          ]
      ]);
    }
}
