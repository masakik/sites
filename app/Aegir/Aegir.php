<?php

namespace App\Aegir;
use GuzzleHttp\Client;

class Aegir
{
    private $aegir_host;
    private $aegir_key;
    private $aegir_protocol;

    // variável para testar aegir está no ar
    private $client;
    private $clientStatus = true;

    public function __construct()
    {
        // variáveis do .env
        $this->aegir_protocol = env('AEGIR_PROTOCOL');
        $this->aegir_host = env('AEGIR_HOST');
        $this->aegir_key = env('AEGIR_KEY');
        
        $this->client = new Client([
             'base_uri' => "{$this->aegir_protocol}://{$this->aegir_host}",
        ]);
        
        // verifica se o aegir está atendendo requisições
        try {
            $this->client->request('GET',"/aegir/saas/site/{$this->aegir_host}.json", 
                ['query' => ['api-key' => $this->aegir_key],
                 'connect_timeout' => 1.5
                ]);
        } catch(\Exception $e) {
            $this->clientStatus = false;
        }
    }
    /**
     * Verifica o status do site no Aegir.
     * Retorno: Habilitado, Desabilitado, Em Processamento, Servidor Offline
     */
    public function verificaStatus($dominio)
    {
        if($this->clientStatus) {
            $res = $this->client->request('GET',"/aegir/saas/site/{$dominio}.json", ['query' => ['api-key' => $this->aegir_key]]);
            $body = json_decode($res->getBody());
            if (isset($body->site_status)){
                if ($body->site_status == 1)
                    return "Habilitado";
                elseif ($body->site_status == -1)
                    return "Desabilitado";
            }
            else
                return "Em Processamento";
        }
        else 
            return 'Servidor Offline';
    }

    public function desabilitaSite($dominio)
    {
        $res = $this->client->request('POST','/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'disable',
                'api-key' => $this->aegir_key
            ]
        ]);
    }

    public function habilitaSite($dominio)
    {
        $res = $this->client->request('POST','/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'enable',
                'api-key' => $this->aegir_key
            ]
        ]);
    }

    public function instalaSite($dominio)
    {
      $id_node_bd = env('AEGIR_ID_BD');
      $id_node_plataforma = env('AEGIR_ID_PLATAFORMA');
      $install_profile = env('AEGIR_INSTALL_PROFILE');

      $res = $this->client->request('POST','/aegir/saas/task/', [
          'form_params' => [
              'target' => $dominio,
              'type' => 'install',
              'options[profile]' => $install_profile,
              'options[database]' => $id_node_bd,
              'options[target_platform]' => $id_node_plataforma,
              'api-key' => $this->aegir_key
          ]
      ]);
    }
    
    public function deletaSite($dominio)
    {
        $res = $this->client->request('POST','/aegir/saas/task/', [
            'form_params' => [
                'target' => $dominio,
                'type' => 'delete',
                'api-key' => $this->aegir_key
            ]
        ]);
    }
}
