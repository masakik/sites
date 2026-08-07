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
use Illuminate\Pagination\LengthAwarePaginator;
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
        # todos sites com filtros
        $sites = Site::query()
            ->allowed()
            ->when(!empty($request->dominio), function ($query) use ($request) {
                $dominio = explode('.', $request->dominio);
                $query->where('dominio', 'LIKE', '%' . $dominio[0] . '%');
            })
            ->when(!is_null($request->status), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when(!is_null($request->categoria), function ($query) use ($request) {
                $query->where('categoria', $request->categoria);
            });

        // Dica de ouro para debugar SQL gerado:
        //dd($sites->toSql());

        // Executa a query
        $sites = $sites
            ->withCount(['chamados as open_chamados_count' => function ($query) {
                $query->where('status', 'aberto');
            }])
            ->orderBy('dominio')
            ->paginate(15);

        // Busca o status dos sites

        foreach ($sites as $site) {
            if ($site->status != 'Solicitado') {
                $this->refreshSiteStatus($site);
            }
        }

        // $this->novoToken();
        // $hashlogin = $user = \Auth::user()->temp_token;

        $this->prepareSitesForView($sites);

        return view('sites.index', [
            'sites' => $sites,
            'categories' => Site::categorias(),
            'statuses' => Site::status(),
            'filters' => $request->only(['dominio', 'status', 'categoria']),
            'hasLocalTickets' => config('sites.chamados') === 'local',
        ]);
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

        return view('sites.create', [
            'dnszone' => config('sites.dnszone'),
            'categories' => Site::categorias(),
        ]);
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
                $html = view('sites.ajax.wp-detalhes', [
                    'wp' => $wp,
                    'site' => $site,
                    'loginData' => $site->loginData(Auth::user()),
                ])->render();
                return $html;
            }
            if ($request->get == 'html_detalhes') {
                return 'nada a exibir por enquanto';
            }
        }

        if ($site->status != 'Solicitado') {
            $this->refreshSiteStatus($site);
        }

        if ($site->config['manager'] == 'drupal') {
            $this->novoToken(); // gera novo token em $user->temp_token
        }

        $site->load('chamados');
        $this->prepareSitesForView(collect([$site]));

        return view('sites.show', [
            'site' => $site,
            'hasLocalTickets' => config('sites.chamados') === 'local',
            'managerDetailsView' => $this->managerDetailsView($site),
        ]);
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
        return view('sites.edit', [
            'site' => $site,
            'categories' => Site::categorias(),
        ]);
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
    //     return view('sites.changeowner', compact('site'));
    // }

    // public function novoAdmin(Site $site)
    // {
    //     $this->authorize('sites.update', $site);
    //     return view('sites.novoadmin', compact('site'));
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
            'acao' => 'nullable',
            Rule::in(['activate', 'deactivate', 'install', 'delete']),
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
            'acao' => 'nullable',
            Rule::in(['refresh']),
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
        $wordpress = [];

        // O relatório também exibe o status persistido. Atualize-o antes de
        // montar as linhas, para não mostrar um valor antigo diferente da
        // página individual do site.
        foreach ($sites as $site) {
            if ($site->status !== 'Solicitado') {
                $this->refreshSiteStatus($site);
            }
        }

        // As informações detalhadas do WordPress são mantidas no cache pelo
        // gerenciador e atualizadas diariamente. O info() só consulta o remoto
        // quando ainda não existe uma cópia em cache.
        foreach ($sites as $site) {
            if (($site->config['manager'] ?? '') !== 'wordpress') {
                continue;
            }

            $wp = new Wordpress($site);
            $wp->info();
            $wordpress[$site->id] = $wp;
        }

        $rows = $sites->map(function (Site $site) use ($wordpress) {
            $wp = $wordpress[$site->id] ?? null;
            $remoteUsers = collect($wp->users ?? [])
                ->map(function ($user) {
                    $login = $user['user_login'] ?? '';
                    $roles = $user['roles'] ?? '';
                    $roles = is_array($roles) ? implode(', ', $roles) : $roles;

                    return $login . ($roles ? ' (' . $roles . ')' : '');
                })
                ->filter();
            $users = $remoteUsers->implode(', ');
            $activePluginsList = collect($wp->plugins ?? [])
                ->where('status', 'active')
                ->pluck('name')
                ->filter();
            $activePlugins = $activePluginsList->implode(', ');

            return [
                'site' => $site,
                'url' => $site->url,
                'short_url' => Str::limit($site->url, 15),
                'manager' => $site->config['manager'],
                'host' => $site->config['host'],
                'short_host' => Str::limit($site->config['host'], 15),
                'port' => $site->config['port'],
                'path' => $site->config['path'],
                'manager_status' => $site->manager_status,
                'remote_login' => $site->config['remoteLogin'] ?? '-',
                'users' => $users,
                'users_count' => $remoteUsers->count(),
                'wordpress_version' => $wp->core['version'] ?? '-',
                'active_plugins' => $activePlugins,
                'active_plugins_count' => $activePluginsList->count(),
                'php_version' => $wp->cli['php_version'] ?? '-',
            ];
        });

        return view('sites.relatorio', compact('rows'));
    }

    /**
     * Prepara dados compartilhados pelas views de listagem e detalhe.
     */
    private function prepareSitesForView($sites): void
    {
        $items = $sites instanceof LengthAwarePaginator ? $sites->getCollection() : collect($sites);
        $numbers = $items
            ->flatMap(fn (Site $site) => $site->administratorNumbers())
            ->unique()
            ->values();
        $users = User::whereIn('codpes', $numbers)->get()->keyBy('codpes');
        $user = Auth::user();

        $items->each(function (Site $site) use ($users, $user) {
            $site->setAttribute('administrators', $site->administratorDetails($users));
            $site->setAttribute('login_data', $site->loginData($user));
        });
    }

    private function managerDetailsView(Site $site): ?string
    {
        return match ($site->config['manager']) {
            'wordpress' => 'sites.show.card-wordpress',
            'html' => 'sites.show.card-html',
            default => null,
        };
    }

    /**
     * Atualiza o status somente quando o gerenciador conseguiu verificá-lo.
     * Falhas de comunicação com o Aegir não devem marcar o site como offline.
     */
    private function refreshSiteStatus(Site $site): void
    {
        $status = SiteManager::verificaStatus($site);

        if ($status === null) {
            return;
        }

        $site->status = $status;
        $site->save();
    }
}
