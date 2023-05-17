<?php
namespace App\Manager\Wordpress;

class Wordpress
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
        $this->suUser = $config['suUser'];

        $this->wp = app_path('Manager/Wordpress/wp');

        $this->info();
        // dd($this);

    }

    /**
     * Coleta as informações da instalação WP e guarda no objeto
     * 
     * Retorna: cli, core, plugins, themes, configs, optiuons 
     */
    public function info()
    {
        $returns = $this->exec('info');
        // dd($returns);
        foreach ($returns as $k => $v) {
            $value = json_decode($v, true);
            $this->$k = $value ? $value : [];
        }
        return true;
    }

    /**
     * Executa um comando do wp-cli
     *
     * Se for localhost, executa como usuário do script,
     * se for remoto conecta via root@ssh e faz sudo para
     * o dono da pasta onde está o WP
     *
     * @param String $acao
     * @return Array
     */
    protected function exec(String $acao = 'info')
    {
        if ($this->host == 'localhost') {
            // se localhost, vamos usar o wp-cli do projeto
            $params['wp'] = app_path('Manager/Wordpress/wp');
            $cmd = 'php ' . app_path('Manager/Wordpress/sites-remoto.php');
        } else {
            // se remoto, tem de ter o wp instalado no servidor
            if (!$this->testaSsh()) {
                $this->error = 'ssh sem conexão';
                return [];
            }
            $this->copy();
            $cmd = "ssh $this->host -p $this->port php /root/sites-remoto.php";
        }

        if ($this->suUser) {
            $params['suUser'] = $this->suUser;
        }
        $params['path'] = $this->path;

        $encodedParams = base64_encode(json_encode($params));
        $cmd .= " $acao $encodedParams 2>&1";
        $execRaw = shell_exec($cmd);
        $exec = json_decode($execRaw, true);
        // dd($execRaw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('exec error json: ', json_last_error_msg(), $cmd, $execRaw);
        }
        if ($exec['status'] != 'success') {
            // mostra erro por enquanto
            $this->error = 'exec ' . $exec['status'] . ': ' . $exec['statusMsg'] . '. Veja log para mais detalhes';
            return [];
        }

        $this->errorMsg = $exec['statusMsg'];
        return $exec['data'];
    }

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

    public function isWp()
    {
        if ($this->error) {
            return false;
        }
        // if (is_numeric($this->core['version'])) {
        //     return true;
        // }
        return true;
    }

    /**
     * Retorna atualizações do core
     *
     * @return String
     */
    public function coreUpdates()
    {
        $ret = '';
        // se updates vem vazio é um [] aí o decode dá erro
        $updates = !is_array($this->core['updates'])
        ? json_decode($this->core['updates'], true)
        : [];

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $ret .= $update['update_type'] . ': ' . $update['version'] . ', ';
            }
            $ret = substr($ret, 0, -2);
        } else {
            $ret .= 'none';
        }
        return $ret;
    }

    public function options($option_name)
    {
        foreach ($this->options as $option) {
            if ($option['option_name'] == $option_name) {
                return $option['option_value'];
            }
        }
        return null;
    }

    public function uspdevSenhaunicaWP()
    {
        return '';
        $plugins = $this->plugins();
        if (!$plugins) {
            return false;
        }
        return $this->wp('config get SENHAUNICA_ADMINS');
    }
}
