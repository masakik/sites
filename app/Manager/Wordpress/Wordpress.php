<?php
namespace App\Manager\Wordpress;

use App\Models\Site;
use App\Manager\Manager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Wordpress extends Manager
{
    // variáveis locais retornadas por exec
    public $info = [];
    public $cli = [];
    public $core = [];
    public $options = [];
    public $plugins = [];
    public $themes = [];
    public $settings = [];
    public $users = [];

    // creation of dynamic properties deprecated
    public $wp;
    public $sites;
    public $configs;


    public function __construct($site)
    {
        $this->wp = app_path('Manager/Wordpress/wp');
        parent::__construct($site);
    }

    /**
     * Coleta as informações da instalação WP e guarda no objeto
     *
     * Retorna: info, cli, core, plugins, themes, configs, options
     */
    public function info(bool $refresh = false)
    {
        $key = $this->infoCacheKey();
        if ($refresh || Session::pull('wp-info-refresh', false) == true) {
            // vamos dar refresh no cache
            $ret = $this->exec('info');
            Cache::put($key, $ret);
        } else {
            // vamos pegar do cache
            $ret = Cache::rememberForever($key, function () {
                return $this->exec('info');
            });
        }

        $this->hydrateInfo($ret);

        return true;
    }

    /**
     * Carrega o último snapshot do WordPress sem fazer chamadas remotas.
     */
    public function loadCachedInfo(): bool
    {
        $ret = Cache::get($this->infoCacheKey());
        if (!is_array($ret)) {
            return false;
        }

        $this->hydrateInfo($ret, false);

        return true;
    }

    private function infoCacheKey(): string
    {
        return sha1('info' . $this->host . $this->port . $this->path . $this->site->url);
    }

    private function hydrateInfo(array $ret, bool $persistSiteConfig = true): void
    {
        if (isset($ret['data'])) {
            foreach ($ret['data'] as $k => $v) {
                $value = $v ? json_decode($v, true) : null;
                $this->$k = $value ? $value : [];
            }
            unset($ret['data']);
        } 

        // $infos = json_decode($ret['info'], true);
        foreach ($ret as $k => $v) {
            $this->info[$k] = $v;
        }

        $config = $this->site->config;

        // setando status do site
        if (!empty($this->info['error']) || !empty($this->info['errorMsg'])) {
            $config['status'] = 'erro';
            $config['statusMsg'] = $this->info['error'] . $this->info['errorMsg'];
        } else {
            $config['status'] = 'ok';
        }

        // verificando remote login
        $config['remoteLogin'] = false;
        foreach ($this->plugins as $plugin) {
            if ($plugin['name'] == 'sites-login' && $plugin['status'] == 'must-use') {
                $config['remoteLogin'] = true;
                break;
            }
        }

        if ($persistSiteConfig) {
            $this->site->config = $config;
            $this->site->save();
        }
    }

    /**
     * Ações de plugin
     *
     * 'activate','deactivate','install','delete'
     */
    public function plugin($acao, $pluginName)
    {
        $params = [
            'pluginAction' => $acao,
            'pluginName' => $pluginName,
        ];
        $exec = $this->exec("plugin $acao '$pluginName'");
        // dd($exec, $params);

        if (strpos($exec['data'], 'Success') !== false) {
            Session::put('wp-info-refresh', true);
            return true;
        } else {
            return false;
            $this->statusMsg = $exec['data'];
            return ['exec' => 'falha'];
        }
    }

    protected function obterPluginStatus($pluginName)
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin['name'] == $pluginName) {
                return $plugin['status'];
            }
        }
        return null;
    }

    /**
     * Retorna url para realizar login usando mu-plugins/sites-login
     * wp user sites-login admin
     */
    public function getLoginUrl($user)
    {
        $createUser = true;
        foreach ($this->users as $user) {
            if ($user['user_login'] == $user->codpes) {
                $createUser = false;
                break;
            }
        }
        if ($createUser) {
            $now = date('Y-m-d-H-i-s');
            $exec1 = $this->exec("user create $user->codpes $user->email --role='administrator' --user_registered='$now'");
        }
        $exec = $this->exec("user sites-login $user->codpes");
        // dd($exec, $exec1);
        if ($exec['status'] == 'success') {
            return $exec['data'];
        } else {
            return false;
        }
    }

    /**
     * Lê os remotos e coloca as informações no cache local
     */
    public static function runDaily()
    {
        // aqui usa notação de json no where. Para isso funcionar no mariadb precisa DB_CONNECTION=mariadb no .env
        $sites = Site::where('config->manager', 'wordpress')->get();
        foreach ($sites as $site) {
            $wp = new Self($site);
            $wp->info(true);
            echo '.';
        }
    }

    /**
     * Executa um comando do wp-cli
     *
     * Executa wp $acao $params
     *
     * Se for localhost, executa como usuário do script,
     * se for remoto conecta via root@ssh e faz sudo para
     * o dono da pasta onde está o WP
     *
     * @param String $acao
     * @return Array
     */
    protected function exec(String $acao, $params = [])
    {
        $info['error'] = null;
        $info['errorMsg'] = null;
        $info['date'] = now()->format('d/m/y H:i:s');

        if ($this->host == 'localhost') {
            // se localhost, vamos usar o wp-cli do projeto
            $params['wp'] = app_path('Manager/Wordpress/wp');
            $cmd = ['php', app_path('Manager/Wordpress/sites-remoto.php')];
        } else {
            // se remoto, tem de copiar os arquivos necessários
            if (!$this->testaSsh()) {
                $info['error'] = 'ssh sem conexão';
                $ret['info'] = json_encode($info);
                return $ret;
            }
            if (!$this->copy()) {
                $info['error'] = 'não foi possível copiar os arquivos para o servidor remoto';
                return $info;
            }
            $cmd = [
                'ssh',
                '-o', 'BatchMode=yes',
                '-o', 'ConnectTimeout=5',
                '-o', 'ServerAliveInterval=5',
                '-o', 'ServerAliveCountMax=2',
                '-p', (string) $this->port,
                $this->host,
                'php', '/root/sites-remoto.php',
            ];
        }

        if (isset($this->suUser)) {
            $params['suUser'] = $this->suUser;
        }
        $params['path'] = $this->path;
        $params['acao'] = $acao;

        $encodedParams = base64_encode(json_encode($params));
        $cmd[] = $encodedParams;

        // A consulta pode envolver wp-cli e serviços externos do site remoto.
        // Nunca deixe uma requisição web esperar indefinidamente por ela.
        $result = $this->runCommand($cmd, 45);
        if (!$result['successful']) {
            $info['error'] = 'exec remoto excedeu o limite ou falhou';
            $info['errorMsg'] = trim($result['output']);
            Log::channel('sites')->warning('Execução remota do WordPress falhou.', [
                'host' => $this->host,
                'port' => $this->port,
                'acao' => $acao,
                'message' => $info['errorMsg'],
            ]);

            return $info;
        }

        $execRaw = $result['output'];
        
        $exec = json_decode($execRaw, true);
        
        Log::channel('sites')->info('Execução remota do WordPress concluída.', [
            'host' => $this->host,
            'port' => $this->port,
            'acao' => $acao,
            'status' => $exec['status'] ?? 'invalid-json',
        ]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $info['error'] = 'resposta inválida do servidor remoto';
            $info['errorMsg'] = json_last_error_msg();
            Log::channel('sites')->warning('Resposta inválida do WordPress remoto.', [
                'host' => $this->host,
                'port' => $this->port,
                'acao' => $acao,
                'json_error' => json_last_error_msg(),
            ]);

            return $info;
        }
        $info = array_merge($info,$exec);
        return $info;
        // $info['errorMsg'] = $exec['statusMsg']; // desativar rsrsr
        $info['statusMsg'] = $exec['statusMsg'];
        $info['status'] = $exec['status'];

        if ($exec['status'] != 'success') {
            // mostra erro por enquanto
            $info['error'] = 'exec ' . $exec['status'] . ': ' . $exec['statusMsg'] . '. Veja log para mais detalhes';
            $ret['info'] = json_encode($info);
            return $ret;
        }
        $ret = ['info' => json_encode($info), 'data' => $exec['data']];
        return $ret;
    }

    public function isWp()
    {
        // dd($this);
        if ($this->info['status'] == 'error') {
            return false;
        }
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

}
