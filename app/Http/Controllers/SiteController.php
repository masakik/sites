<?php

namespace App\Http\Controllers;

use App\Mail\AprovaSiteMail;
use App\Mail\DeletaAdminMail;
use App\Mail\NovoAdminMail;
use App\Mail\SiteMail;
use App\Mail\TrocaResponsavelMail;
use App\Manager\Wordpress\Wordpress;
use App\Models\Site;
use App\Models\User;
use App\Rules\Domain;
use App\Services\SiteManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('sites.create'); // verificar porque isso não funciona
        \UspTheme::activeUrl('sites');
        $dnszone = config('sites.dnszone');

        # todos sites
        $sites = Site::allowed();

        // 1. query com a busca
        if (isset($request->dominio) || isset($request->status) || isset($request->categoria)) {
            $dominio = explode('.', $request->dominio);
            $sites->where('dominio', 'LIKE', '%' . $dominio[0] . '%');

            if (!is_null($request->status)) {
                $sites->where('status', $request->status);
            }
            if (!is_null($request->categoria)) {
                $sites->where('categoria', $request->categoria);
            }
        }

        // Dica de ouro para debugar SQL gerado:
        //dd($sites->toSql());

        // Executa a query
        $sites = $sites->orderBy('dominio')->paginate(15);

        // Busca o status dos sites

        foreach ($sites as $site) {
            if ($site->status != 'Solicitado') {
                $site->status = SiteManager::verificaStatus($site);
                $site->save();
            }
        }

        $this->novoToken();
        $hashlogin = $user = \Auth::user()->temp_token;

        $str = 'Illuminate\Support\Str';

        return view('sites/index', compact('sites', 'dnszone', 'hashlogin', 'str'));
    }

    /**
     * gera um token de login no drupal
     */
    private function novoToken()
    {
        $hashlogin = Str::random(32);
        $user = \Auth::user();
        $user->temp_token = $hashlogin;
        $user->save();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('sites.create');
        \UspTheme::activeUrl('sites/create');

        return view('sites/create', ['dnszone' => config('sites.dnszone')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('sites.create');
        $user = \Auth::user();

        $request->validate([
            'dominio' => ['required', 'unique:sites', new Domain],
            'categoria' => ['required', Rule::in(Site::categorias())],
            'justificativa' => ['required'],
        ]);

        $site = new Site;
        $site->dominio = strtolower($request->dominio);
        $site->categoria = $request->categoria;
        $site->justificativa = $request->justificativa;
        $site->status = 'Solicitado';

        $site->owner = $user->codpes;
        $site->save();

        Mail::send(new SiteMail($site, $user));
        $request->session()->flash('alert-info', 'Solicitação em andamento');
        return redirect("/sites/$site->id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Site $site)
    {
        $this->authorize('sites.view', $site);

        // pegando dados do WP via ajax
        if (isset($request->get)) {
            if ($request->get == 'wp_detalhes') {
                $wp = new Wordpress($site);
                $wp->info();
                $html = view('sites.ajax.wp-detalhes', compact('wp', 'site'))->render();
                return $html;
            }
            if ($request->get == 'html_detalhes') {
                return 'nada a exibir por enquanto';
            }
        }

        if ($site->status != 'Solicitado') {
            $site->status = SiteManager::verificaStatus($site);
            $site->save();
        }

        if ($site->config['manager'] == 'drupal') {
            $this->novoToken(); // gera novo token em $user->temp_token
        }

        return view('sites/show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        $this->authorize('sites.update', $site);
        return view('sites/edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $this->authorize('sites.update', $site);

        if (isset($request->acao) && $request->acao == 'changeOwner') {
            // troca de responsável
            $request->validate([
                'codpes' => ['required', 'codpes', 'integer'],
            ]);
            Mail::send(new TrocaResponsavelMail($site, $request->codpes));
            $site->owner = $request->codpes;
            $request->session()->flash('alert-info', 'Responsável alterado com sucesso');
            $site->save();
            return back();
        }

        if (isset($request->categoria) || isset($request->justificativa)) {
            // site update
            $request->validate([
                'categoria' => ['required'],
                'justificativa' => ['required'],
                'dominio' => ['nullable'],
            ]);

            $site->categoria = $request->categoria;
            $site->justificativa = $request->justificativa;
            $site->dominio = $request->dominio ? $request->dominio : $site->dominio;
            $request->session()->flash('alert-info', 'Site atualizado com sucesso');
        }

        if (isset($request->acao) && $request->acao == 'addAdmin') {
            // adiciona admin
            $request->validate([
                'codpes' => ['required', 'codpes', 'integer'],
            ]);
            $site->addAdmin($request->codpes);
            Mail::send(new NovoAdminMail($site, $request->codpes));
            $request->session()->flash('alert-info', 'Administrador adicionado com sucesso');
        }

        if (isset($request->acao) && $request->acao == 'deleteAdmin') {
            // remove admin
            $request->validate([
                'codpes' => ['required', 'codpes', 'integer'],
            ]);
            $site->deleteAdmin($request->codpes);
            Mail::send(new DeletaAdminMail($site, $request->codpes));
            $request->session()->flash('alert-info', 'Administrador removido com sucesso');
        }

        if (isset($request->aprovar)) {
            // aprovar
            $this->authorize('admin');
            $site->status = 'Aprovado - Em Processamento';
            SiteManager::instala($site);
            Mail::send(new AprovaSiteMail($site));

            $request->session()->flash('alert-info', 'Site aprovado com sucesso');
        }

        if (isset($request->voltar_solicitacao)) {
            $this->authorize('admin');
            $site->status = 'Solicitado';
            $request->session()->flash('alert-info', 'Site aprovado com sucesso');
        }

        if (isset($request->acao) && $request->acao == 'config') {
            $this->authorize('admin');
            $request->validate([
                'manager' => 'required',
                'host' => ['nullable'],
                'port' => ['nullable', 'integer'],
                'path' => ['nullable'],
            ]);

            $config['manager'] = $request->manager;
            $config['host'] = $request->host;
            $config['port'] = $request->port;
            $config['path'] = $request->path;
            $site->config = $config;

            $request->session()->flash('alert-info', 'Config atualizado com sucesso');
        }

        $site->save();

        return redirect("/sites/$site->id");
    }

    /**
     * Show the form for editing the owner.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    // public function changeowner(Site $site)
    // {
    //     $this->authorize('sites.update', $site);
    //     return view('sites/changeowner', compact('site'));
    // }

    // public function novoAdmin(Site $site)
    // {
    //     $this->authorize('sites.update', $site);
    //     return view('sites/novoadmin', compact('site'));
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $this->authorize('admin');

        if ($site->status == "Aprovado - Desabilitado") {
            SiteManager::deleta($site);
        }
        $site->delete();

        request()->session()->flash('alert-info', 'Deleção do site em andamento.');
        return redirect('/sites');
    }

    /**
     * Aparentemente não usa em lugar algum
     */
    public function check(Request $request)
    {
        $request->validate([
            'temp_token' => ['required', 'alpha_num'],
            'codpes' => ['required', 'integer'],
            'site' => ['required'],
        ]);

        $user = User::where('codpes', $request->codpes)->first();

        // verifica se token secreto é válido
        if ($request->secretkey != config('sites.deploy_secret_key')) {
            return response()->json([false, 'Secret Token Inválido']);
        }

        // verifica se o temp_token está válido
        if ($request->temp_token != $user->temp_token) {
            return response()->json([false, 'Temp Token Inválido']);
        }

        // verifica se site existe
        $remover = config('sites.dnszone');
        if (config('app.env') != 'production') {
            $remover .= ':8088';
        }
        $dominio = str_replace($remover, '', $request->site);
        $site = Site::where('dominio', $dominio)->first();
        if ($site) {
            // verifica se o número usp em questão pode fazer logon no site
            $all = $site->owner . ',' . $site->numeros_usp . ',' . config('sites.admins');
            if (in_array($request->codpes, explode(",", $all))) {
                return response()->json([true, $user->email]);
            }
            return response()->json([false, 'Usuário sem permissão']);
        }
        return response()->json([false, 'Site não existe']);
    }

    public function installSite(Request $request, Site $site)
    {
        $this->authorize('admin');
        siteManager::instala($site);

        $request->session()->flash('alert-info', 'Criação do site em andamento.');
        return back();
    }

    public function disableSite(Request $request, Site $site)
    {
        $this->authorize('admin');
        siteManager::desabilita($site);

        $request->session()->flash('alert-info', 'Desabilitação do site em andamento.');
        return back();
    }

    public function enableSite(Request $request, Site $site)
    {
        $this->authorize('admin');
        siteManager::habilita($site);

        $request->session()->flash('alert-info', 'Habilitação do site em andamento.');
        return back();
    }
    /**
     * Realiza o login do usuário na administração do site remoto
     *
     * @return Redireciona o usuário para o site
     */
    public function login(Request $request, Site $site)
    {
        $this->authorize('sites.update', $site);
        $wp = new Wordpress($site);
        $user = Auth::user();
        $url = $wp->getLoginUrl($user);
        $context = ['codpes' => $user->codpes, 'site_id' => $site->id, 'site_url' => $site->url];
        if ($url) {
            Log::channel('sites')->info("Usuário efetuou login remoto", $context);
            return redirect($url);
        } else {
            $request->session()->flash('alert-danger', 'Erro ao gerar token de login remoto!');
            Log::channel('sites')->info('Erro ao gerar token de login remoto', $context);
            return back();
        }
    }

    /**
     * Executa ações em plugins do wordpress
     */
    public function WpPlugin(Request $request, Site $site)
    {
        $this->authorize('sites.update', $site);
        $request->validate([
            'acao' => 'nullable', Rule::in(['activate', 'deactivate', 'install', 'delete']),
            'plugin_name' => 'nullable|string|max:150',
        ]);

        $wp = new Wordpress($site);
        if ($wp->plugin($request->acao, $request->plugin_name) == 'sucesso') {
            $request->session()->flash('alert-info', 'WP plugin: ação ' . $request->acao . ' realizado com sucesso.');
        } else {
            $request->session()->flash('alert-danger', 'WP plugin: houve problemas com a acao ' . $request->acao);
        }
        return back();
    }

    public function gerenciador(Request $request, Site $site)
    {
        $this->authorize('sites.update', $site);
        $request->validate([
            'acao' => 'nullable', Rule::in(['refresh']),
        ]);
        if ($request->acao == 'refresh') {
            Session::put('wp-info-refresh', true);
            return back();
        }
    }

    /**
     * Gera listagem dos sites mostrando as pendências para fins gerenciais
     */
    public function relatorio()
    {
        $this->authorize('admin');
        \UspTheme::activeUrl('sites/relatorio');

        $sites = Site::orderBy('dominio', 'ASC')->orderBy('categoria', 'ASC')->get();
        return view('sites.relatorio', compact('sites'));
    }
}
