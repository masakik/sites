<?php

namespace App\Aegir;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Aegir
{
    private $aegir_host;
    private $aegir_key;
    private $aegir_protocol;

    private $client;

    public function __construct()
    {
        // Valores de configuração funcionam mesmo após `php artisan config:cache`.
        $this->aegir_protocol = config('sites.aegir.protocol');
        $this->aegir_host = config('sites.aegir.host');
        $this->aegir_key = config('sites.aegir.key');

            $this->client = new Client([
                'base_uri' => "{$this->aegir_protocol}://{$this->aegir_host}",
            ]);

    }
    /**
     * Verifica o status do site no Aegir.
     * Retorna null quando a API do Aegir não pôde ser consultada.
     *
     * Uma indisponibilidade da API do Aegir não é evidência de que o servidor
     * do site esteja offline (por exemplo, o SSH pode continuar acessível).
     */
    public function verificaStatus($dominio): ?string
    {
        try {
            $res = $this->client->request('GET', "/aegir/saas/site/{$dominio}.json", [
                'query' => ['api-key' => $this->aegir_key],
                'connect_timeout' => 1.5,
                'timeout' => 5,
            ]);
            $body = json_decode($res->getBody());
            if (isset($body->site_status)) {
                if ($body->site_status == 1)
                    return "Aprovado - Habilitado";
                elseif ($body->site_status == -1)
                    return "Aprovado - Desabilitado";
                else
                    return "Aprovado - Em Processamento";
            } else
                return "Aprovado - Em Processamento";
        } catch (\Throwable $e) {
            Log::channel('sites')->warning('Não foi possível consultar o status no Aegir; status do site preservado.', [
                'dominio' => $dominio,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function desabilitaSite($dominio)
    {
        $res = $this->client->request('POST', '/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'disable',
                'api-key' => $this->aegir_key
            ]
        ]);
    }


    public function habilitaSite($dominio)
    {
        $res = $this->client->request('POST', '/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'enable',
                'api-key' => $this->aegir_key
            ]
        ]);
    }

    public function instalaSite($dominio)
    {
        $res = $this->client->request('POST', '/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'install',
                'api-key' => $this->aegir_key
            ]
        ]);
    }

    public function deletaSite($dominio)
    {
        $res = $this->client->request('POST', '/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'delete',
                'api-key' => $this->aegir_key
            ]
        ]);
    }
}
