<?php

class Wordpress
{
    // Parametros passados para o script
    /** caminho onde o WP está instalado */
    protected $path = null;

    /** executável do WP-CLI */
    protected $wp = null;

    /** Usuário comum para o qual o root fará su */
    protected $suUser = null;

    /** MEnsagem de erro fatal */
    protected $error = null;

    /** Mensagem de erro não fatal */
    protected $statusMsg = null;

    /** acoes que a classe irá responder, somente info por enquanto */
    protected static $acoes = ['info', 'raw'];

    public function __construct($params)
    {
        // recebe os parâmetros
        foreach ($params as $key => $param) {
            $this->$key = $param;
        }

        if (!is_dir($this->path)) {
            $this->error = ['status' => 'error', 'statusMsg' => 'path não é dir: ' . $this->path];
            return false;
        }

        // obter o user proprietário da pasta se não passado por param
        if (!$this->suUser) {
            $this->suUser = posix_getpwuid(fileowner($this->path))['name'];
        }
    }

    /**
     * Executa uma ação - comando do WP-CLI
     *
     * @param String $acao
     * @return Array
     */
    public function acao($acao)
    {
        if (!in_array($acao, self::$acoes)) {
            return ['status' => 'error', 'statusMsg' => 'ação inválida: ' . $acao];
        }

        if ($this->error) {
            return $this->error;
        }

        $ret['status'] = 'success';
        if ($acao == 'info') {
            $data['cli'] = $this->wp('cli info');
            $data['core'] = $this->wp_core_info();
            $data['plugins'] = $this->wp('plugin list');
            $data['themes'] = $this->wp('theme list');
            $data['configs'] = $this->wp('config list');
            $data['options'] = $this->wp('option list');
        }
        $ret['data'] = $data;
        $ret['statusMsg'] = $this->statusMsg;
        return $ret;
    }

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
    protected function checkError($string, String $categ)
    {
        if (
            stripos($string, 'PHP Fatal error') !== false
            || substr($string, 0, 5) == 'Error'
        ) {
            $this->statusMsg .= $categ . ' error: ' . $string;
            return true;
        } else {
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
    public function wp(String $cmd = 'cli info', String $outFormat = 'json', $retry = false)
    {
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
        $cmdline .= ' 2>&1';

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

if (empty($argv[1])) {
    die(json_encode(['status' => 'error', 'msg' => 'ação inexistente']));
}
$acao = $argv[1];
$params = isset($argv[2]) ? json_decode(base64_decode($argv[2]), true) : [];

$wp = new Wordpress($params);

$ret = $wp->acao($acao);
// print_r($exec);
echo json_encode($ret);
