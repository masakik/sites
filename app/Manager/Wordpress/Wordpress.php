<?php
namespace App\Manager\Wordpress;

use App\Manager\Manager;
use App\Models\Site;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Wordpress extends Manager
{
    // variáveis locais retornadas por exec
    public $info;
    public $cli;
    public $core;
    public $options;
    public $plugins;
    public $themes;
    public $settings;

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
    public function info()
    {
        $key = sha1('info' . $this->host . $this->port . $this->path . $this->site->url);
        if (Session::pull('wp-info-refresh', false) == true) {
            $ret = $this->exec('info');
            Cache::put($key, $ret);
        } else {
            $ret = Cache::rememberForever($key, function () {
                return $this->exec('info');
            });
        }

        // dd($ret);
        foreach ($ret as $k => $v) {
            $value = json_decode($v, true);
            $this->$k = $value ? $value : [];
        }

        // dd($this);
        $config = $this->site->config;
        if ($this->info['error'] || $this->info['errorMsg']) {
            $config['status'] = 'erro';
            $config['statusMsg'] = $this->info['error'] . $this->info['errorMsg'];
        } else {
            $config['status'] = 'ok';
        }
        $this->site->config = $config;
        $this->site->save();

        return true;
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
        $exec = $this->exec('plugin', $params);
        // dd($exec, $params);
        if ($exec['exec'] == 'sucesso') {
            Session::put('wp-info-refresh', true);
            return true;
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
            Session::put('wp-info-refresh', true);
            $wp = new Self($site);
            $wp->info();
            echo '.';
        }
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
    protected function exec(String $acao, $params = [])
    {
        $info['error'] = null;
        $info['errorMsg'] = null;
        $info['date'] = now()->format('d/m/y H:i:s');

        if ($this->host == 'localhost') {
            // se localhost, vamos usar o wp-cli do projeto
            $params['wp'] = app_path('Manager/Wordpress/wp');
            $cmd = 'php ' . app_path('Manager/Wordpress/sites-remoto.php');
        } else {
            // se remoto, tem de ter o wp instalado no servidor
            if (!$this->testaSsh()) {
                $info['error'] = 'ssh sem conexão';
                $ret['info'] = json_encode($info);
                return $ret;
            }
            $this->copy();
            $cmd = "ssh $this->host -p $this->port php /root/sites-remoto.php";
        }

        if (isset($this->suUser)) {
            $params['suUser'] = $this->suUser;
        }
        $params['path'] = $this->path;

        $encodedParams = base64_encode(json_encode($params));
        $cmd .= " $acao $encodedParams 2>&1";
        $execRaw = shell_exec($cmd);
        $exec = json_decode($execRaw, true);
        // dd($exec);
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('exec error json: ', json_last_error_msg(), $cmd, $execRaw);
        }

        $info['errorMsg'] = $exec['statusMsg'];

        if ($exec['status'] != 'success') {
            // mostra erro por enquanto
            $info['error'] = 'exec ' . $exec['status'] . ': ' . $exec['statusMsg'] . '. Veja log para mais detalhes';
            $ret['info'] = json_encode($info);
            return $ret;
        }
        $ret = array_merge(['info' => json_encode($info)], $exec['data']);
        return $ret;
    }

    public function isWp()
    {
        if ($this->info['error']) {
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
