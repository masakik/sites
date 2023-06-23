<?php
/**
 * Executa ações no WP do servidor remoto
 *
 * - instancia a classe Wordpress
 * - executa o método ação
 * - retorna o resultado para o servidor do sites
 *
 * https://github.com/uspdev/sites
 * Atualizado em 15/6/2023
 */

if (empty($argv[1])) {
    die(json_encode(['status' => 'error', 'msg' => 'ação inexistente']));
}
$params = json_decode(base64_decode($argv[1]), true);
$acao = $params['acao'];

$wp = new Wordpress($params);
$ret = $wp->acao($acao);
// echo $ret;
echo json_encode($ret);

class Manager
{
    // Parametros passados para o script
    /* caminho onde o WP está instalado */
    protected $path = null;

    /* Usuário comum para o qual o root fará su */
    protected $suUser = null;

    /* Mensagem de erro fatal */
    protected $error = null;

    protected $status;

    public function __construct($params)
    {
        // recebe os parâmetros
        foreach ($params as $key => $param) {
            $this->$key = $param;
        }

        if (!is_dir($this->path)) {
            $this->error = ['status' => 'error', 'statusMsg' => 'path não é dir: ' . $this->path];
        } else {
            // obter o user proprietário da pasta se não passado por param
            if (!$this->suUser) {
                $this->suUser = posix_getpwuid(fileowner($this->path))['name'];
            }
        }
    }

    /**
     * Executa uma ação
     *
     * @param String $acao
     * @return Array
     */
    public function acao($acao)
    {
        // deve ser preenchido na classe filha
    }
}

class Wordpress extends Manager
{
    /** executável do WP-CLI */
    protected $wp = null;

    /** Mensagem de erro não fatal */
    protected $statusMsg = null;

    /** acoes que a classe irá responder, somente info por enquanto */
    // protected static $acoes = ['info', 'plugin', 'user'];

    public function __construct($params)
    {
        // recebe os parâmetros
        // verifica pasta
        // pega suUser
        parent::__construct($params);
    }

    /**
     * Executa uma ação - comando do WP-CLI
     *
     * @param String $acao
     * @return Array
     */
    public function acao($acao)
    {
        $gerenciador['gerenciador'] = $this->gerenciador();
        if ($this->status == 'error') {
            $ret['status'] = $this->status;
            $ret['statusMsg'] = $this->statusMsg;
            $ret['data']['gerenciador'] = $gerenciador['gerenciador'];
            return $ret;
        }

        $ret['status'] = 'success';
        if ($acao == 'info') {
            // info é uma ação especial
            $acaoResult = $this->acaoInfo();
            $ret['data'] = array_merge($gerenciador, $acaoResult);
        } else {
            // outras ações
            $ret['data'] = $this->wp($acao, '');
        }

        $ret['statusMsg'] = $this->statusMsg;
        // print_r($ret);
        return $ret;
    }

    /**
     * Retorna informações básicas do caminho remoto ($this->path)
     */
    protected function gerenciador()
    {
        if (!is_dir($this->path)) {
            $this->status = 'error'; //['status' => 'error', 'statusMsg' => 'path não é dir: ' . $this->path];
            $this->statusMsg = 'path não é dir: ' . $this->path;
            $ret['error'] = "O caminho $this->path não existe!";
        } else {
            // obter o user proprietário da pasta se não passado por param
            if (!$this->suUser) {
                $this->suUser = posix_getpwuid(fileowner($this->path))['name'];
            }
            $ret['folder-owner'] = $this->suUser;

            // uso do disco
            $ret['disk-usage'] = exec("du -sh $this->path | cut -f1");
        }
        return json_encode($ret);
    }

    protected function acaoInfo()
    {
        $data['sites'] = $this->sites();
        $data['cli'] = $this->wp('cli info');
        $data['core'] = $this->wp_core_info();
        $data['plugins'] = $this->wp('plugin list');
        $data['themes'] = $this->wp('theme list');
        $data['configs'] = $this->wp('config list');
        $data['options'] = $this->wp('option list');
        $data['users'] = $this->wp('user list');
        return $data;
    }

    /**
     * Tarefas básicas para o sites.
     *
     * retorna json para manter consistencia com wp-cli
     *
     * @return Json
     */
    protected function sites()
    {
        // verificar se pasta existe
        if (!is_dir($this->path . '/wp-content/mu-plugins')) {
            mkdir($this->path . '/wp-content/mu-plugins');
        }
        // cria index.php vazio
        if (!file_exists($this->path . '/wp-content/mu-plugins/index.php')) {
            file_put_contents($this->path . '/wp-content/mu-plugins/index.php', '<?php // Silence is golden.');
        }        
        // copiar como root
        if (file_exists(__DIR__ . '/sites-login.php')) {
            copy(__DIR__ . '/sites-login.php', $this->path . '/wp-content/mu-plugins/sites-login.php');
            $ret['mu-plugin-copy'] = 'arquivo copiado';
        }
        $ret['mu-plugin-paths'] = __DIR__ . '/sites-login.php => ' . $this->path . '/wp-content/mu-plugins/sites-login.php';

        return json_encode($ret);
    }

    // protected function acaoPlugin()
    // {
    //     $pluginAction = $this->pluginAction;
    //     $pluginName = $this->pluginName;
    //     $exec = $this->wp("plugin $pluginAction '$pluginName'", '');
    //     if (strpos($exec, 'Success') !== false) {
    //         return ['exec' => 'sucesso'];
    //     } else {
    //         $this->statusMsg = $exec;
    //         return ['exec' => 'falha'];
    //     }
    // }

    /**
     * Retorna as informações do core do WP
     *
     * @return Array
     */
    protected function wp_core_info()
    {
        $core['version'] = $this->wp('core version', '');
        $core['version'] = trim(preg_replace('/\s+/', ' ', $core['version'])); //remove EOL
        $core['updates'] = $this->wp('core check-update') ?? [];
        $core['maintenance_mode'] = $this->wp('maintenance-mode status', '');
        return json_encode($core);
    }

    /**
     * Verifica se a execução do WP-CLI gerou algum erro
     *
     * @param String|Null $string Onde será procurado o erro
     * @param String $categ Onde ocorreu o erro
     * @return Bool True se apresentou erro
     */
    protected function checkError($string, String $cmd)
    {
        if (
            stripos($string, 'PHP Fatal error') !== false
            || substr($string, 0, 5) == 'Error'
        ) {
            $this->statusMsg .= $cmd . ' error: ' . $string;
            return true;
        } else {
            $this->statusMsg = null;
            return false;
        }
    }

    /**
     * Verifica se uma string é json
     *
     * @param String $string
     * @return Bool
     */
    protected function isJson(String $string)
    {
        $json = json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Executa o comando do WP-CLI no wordpress
     *
     * @param String $cmd Comando a ser executado pelo WP-CLI
     * @param String $outFormat Formato a ser fornecido pelo WP-CLI
     * @param Bool $retry se true, corresponde a nova tentativa desativando plugins e themes
     * @return String|Json
     */
    public function wp(String $cmd, String $outFormat = 'json', $retry = false)
    {
        // usando wp instalado no sistema. Para usar o do sites,
        // precisa copiar para um lugar que o su user tenha acesso
        $wp = !empty($this->wp) ? $this->wp : 'wp';
        if (posix_getpwuid(posix_geteuid())['name'] == 'root') {
            $cmdline = ($this->suUser) ? 'sudo -u ' . $this->suUser : '';
        } else {
            $cmdline = '';
        }
        $cmdline .= " $wp $cmd";
        $cmdline .= ($this->path) ? ' --path=' . $this->path : '';
        $cmdline .= ($outFormat == 'json') ? ' --format=json' : '';
        $cmdline .= ($retry) ? ' --skip-plugins --skip-themes' : '';
        $cmdline .= " 2>/dev/null"; // redirecionando para null ao inves da saida
        // alguns plugins geram warnings junto à saida do wp plugin list aí não consegue parsear
        // vamos esconder por enquanto.

        // echo 'cmd ', $cmdline;
        // expected: WP_CLI_FORCE_USER_LOGIN=1  /home/kawabata/web/sites/app/Manager/Wordpress/wp cli info --path=/home/kawabata/web/wordpress --format=json 2>&1
        // expected sudo: sudo -u cegis WP_CLI_FORCE_USER_LOGIN=1 wp cli info --path=/home/grupos/sep/cegis/public_html --format=json 2>&1

        $exec = shell_exec($cmdline);

        // vamos verificar se retornou algum erro php,
        // se sim vamos ocultar o retorno e setar o statusMsg
        if ($this->checkError($exec, $cmd)) {
            if (!$retry) {
                return $this->wp($cmd, $outFormat, true);
            }
            if ($outFormat == 'json') {
                return json_encode([]);
            } else {
                return '';
            }
        }

        return $exec;
    }
}
