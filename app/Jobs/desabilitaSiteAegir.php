<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;

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
        $alvo = $this->alvo;

        $client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br'
        ]);

        $res = $client->request('POST','/aegir/saas/task/', [
            'form_params' => [
                'target' => $alvo,
                'type' => 'disable',
                'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
            ]
        ]);
    }
}
