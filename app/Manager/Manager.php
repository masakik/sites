<?php

namespace App\Manager;

class Manager
{
    /** Mensagem caso tenha algum erro que impeça de coletar informações do WP */
    public $error = null;
    public $errorMsg; // erro nao fatal

    public function __construct($site)
    {
        $config = $site->config;
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->path = $config['path'];
        // $this->suUser = $config['suUser'];

        $this->info();
    }

    /**
     * Executa um comando no site
     *
     * $acao pode ser 'info', etc.
     *
     * @param $acao Comando a ser executado
     * @return Array
     */
    protected function exec(String $acao)
    {
        // prototipo de função para executar comando
        return [];
    }

    /**
     * Coleta as informações da instalação WP e guarda no objeto
     */
    public function info()
    {
      // prototipo para coletar as informações do remoto
      // deve guardar no objeto $this->xxx;
        $returns = $this->exec('info');
        // dd($returns);
        foreach ($returns as $k => $v) {
            $value = json_decode($v, true);
            $this->$k = $value ? $value : [];
        }
        return true;
    }

    /**
     * Testa a conexão ssh com servidor remoto
     *
     * @return Bool
     */
    protected function testaSsh()
    {
        $cmd = "ssh -o BatchMode=yes -o ConnectTimeout=5 $this->host -p $this->port echo ok 2>&1";
        $exec = shell_exec($cmd);
        return $exec == "ok\n" ? true : false;
    }

    /**
     * Copia os arquivos a serem executados no servidor remoto
     */
    protected function copy()
    {
        $path = app_path('Manager/Wordpress');
        $cmd = "scp -P $this->port $path/sites-remoto.php $this->host:/root/sites-remoto.php 2>&1";
        $exec = shell_exec($cmd);
        if ($exec) {
            dd('copy error', $cmd, $exec);
        }

        $cmd = "scp -P $this->port $path/wp $this->host:/root/wp 2>&1";
        $exec = shell_exec($cmd);
        if ($exec) {
            dd('copy error', $cmd, $exec);
        }
    }
}
