<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Aegir\Aegir;

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
        $this->aegir = new Aegir;
        $dominio = $this->alvo;
        $site_modelo = env('SITE_MODELO');
        $id_node_bd = env('ID_NODE_BD');
        $id_node_plataforma = env('ID_NODE_PLATAFORMA');
        $retorno = $this->aegir->clonaSite($dominio);
    }
}
