<?php

namespace App\Manager;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class Manager
{
    public $host;
    public $port;
    public $path;
    public $url;
    public $site;
    public $gerenciador = [];

    protected $config;

    public function __construct($site)
    {
        $config = $site->config;
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->path = $config['path'];
        $this->site = $site;
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
        $result = $this->runCommand([
            'ssh',
            '-o', 'BatchMode=yes',
            '-o', 'ConnectTimeout=5',
            '-o', 'ConnectionAttempts=1',
            '-p', (string) $this->port,
            $this->host,
            'echo ok',
        ], 8);

        return $result['successful'] && trim($result['output']) === 'ok';
    }

    /**
     * Copia os arquivos a serem executados no servidor remoto
     */
    protected function copy(): bool
    {
        $path = app_path('Manager/Wordpress');
        foreach (['sites-remoto.php', 'wp', 'sites-login.php'] as $file) {
            $result = $this->runCommand([
                'scp',
                '-o', 'BatchMode=yes',
                '-o', 'ConnectTimeout=5',
                '-o', 'ConnectionAttempts=1',
                '-P', (string) $this->port,
                "$path/$file",
                "$this->host:/root/$file",
            ], 15);

            if (!$result['successful']) {
                Log::channel('sites')->warning('Não foi possível copiar arquivos para o servidor remoto.', [
                    'host' => $this->host,
                    'port' => $this->port,
                    'file' => $file,
                    'output' => trim($result['output']),
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Executa um processo externo com limite rígido de duração.
     *
     * @return array{successful: bool, output: string}
     */
    protected function runCommand(array $command, int $timeout): array
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->setIdleTimeout($timeout);

        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            return [
                'successful' => false,
                'output' => "Processo excedeu o limite de {$timeout}s.",
            ];
        }

        return [
            'successful' => $process->isSuccessful(),
            'output' => $process->getOutput() . $process->getErrorOutput(),
        ];
    }
}
