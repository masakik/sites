<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Aegir\Aegir;

class desabilitaSiteAegir implements ShouldQueue
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
        $retorno = $this->aegir->desabilitaSite($dominio);
    }
}
