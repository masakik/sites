<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Uspdev\Replicado\Pessoa;

class Site extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];

    protected $configDefaults = [
        'manager' => '',
        'host' => 'localhost',
        'port' => '2221',
        'path' => '/home/dominio',
        'suUser' => null,
        'status' => '?', // mostra se tem erros no site
        'statusMsg' => '',
        'remoteLogin' => false,
    ];

    public $managers = [
        'wordpress',
        'html/php',
        'redirecionador',
        'drupal',
    ];
    
    public function getManagersAttribute($value) {
        $valuesFromDB = Site::whereNotNull('config->manager')->select('config->manager as manager')->groupBy('manager')->get();
        array_push($value, $valuesFromDB);
        // dd($value->pluck('manager'));
        return $value->pluck('manager');
        // https://stackoverflow.com/questions/47616701/laravel-eloquent-json-field-selecting-attribute-produces-extra-double-quote
    }

    public function getConfigAttribute($value)
    {
        $value = is_array($value) ? $value : (json_decode($value, true) ?: []);
        foreach ($this->configDefaults as $key => $default) {
            $value[$key] = $value[$key] ?? $default;
        }
        return $value;
    }

    /**
     * Acessor para criar o atributo url
     */
    public function getUrlAttribute()
    {
        return $this->dominio . config('sites.dnszone');
    }

    /**
     * Retorna os dados usados para exibir o status do gerenciador.
     */
    public function getManagerStatusAttribute()
    {
        return match ($this->config['status']) {
            'erro' => [
                'value' => 'erro',
                'icon' => 'fas fa-exclamation-circle text-warning',
                'title' => Str::limit($this->config['statusMsg'], 200),
            ],
            '?' => [
                'value' => '?',
                'icon' => 'fas fa-question-circle text-secondary',
                'title' => 'Não verificado',
            ],
            default => [
                'value' => $this->config['status'],
                'icon' => 'fas fa-check-circle text-success',
                'title' => 'Parece estar tudo certo',
            ],
        };
    }

    /**
     * Retorna o host truncado para a listagem de sites.
     */
    public function getShortHostAttribute(): string
    {
        return Str::limit($this->config['host'], 10);
    }

    /**
     * Retorna os dados usados pelo badge de status do site.
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'Solicitado' => ['class' => 'badge-primary', 'label' => 'Aguardando aprovação'],
            'Aprovado - Desabilitado' => ['class' => 'badge-warning', 'label' => 'Desabilitado'],
            'Aprovado - Habilitado' => ['class' => 'badge-success', 'label' => 'Habilitado'],
            default => ['class' => 'badge-secondary', 'label' => $this->status],
        };
    }

    /**
     * Retorna as ações administrativas disponíveis para o status atual.
     */
    public function getAdminActionsAttribute()
    {
        return match ($this->status) {
            'Solicitado' => ['aprovar', 'delete'],
            'Aprovado - Habilitado' => ['desabilitar'],
            'Aprovado - Desabilitado' => ['habilitar', 'delete'],
            'Aprovado - Em Processamento' => ['voltar'],
            default => [],
        };
    }

    /**
     * Retorna os números USP dos administradores do site sem entradas vazias.
     */
    public function administratorNumbers(): array
    {
        return collect(explode(',', (string) $this->numeros_usp))
            ->map(fn ($number) => trim($number))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Monta os dados dos administradores para a camada de apresentação.
     * A coleção de usuários pode ser fornecida pelo controller para evitar N+1.
     */
    public function administratorDetails(?Collection $users = null): Collection
    {
        $numbers = $this->administratorNumbers();
        $users ??= User::whereIn('codpes', $numbers)->get()->keyBy('codpes');

        return collect($numbers)->map(function ($number) use ($users) {
            $user = $users->get($number);

            return [
                'codpes' => $number,
                'name' => $user?->name,
                'email' => $user?->email,
            ];
        });
    }

    /**
     * Retorna o total de chamados abertos do site.
     */
    public function getOpenChamadosCountAttribute(): int
    {
        if (array_key_exists('open_chamados_count', $this->attributes)) {
            return (int) $this->attributes['open_chamados_count'];
        }

        if ($this->relationLoaded('chamados')) {
            return $this->chamados->where('status', 'aberto')->count();
        }

        return $this->chamados()->where('status', 'aberto')->count();
    }

    /**
     * Monta as opções de login remoto para o usuário atual.
     */
    public function loginData($user): ?array
    {
        if (in_array($this->status, ['Solicitado', 'Aprovado - Em Processamento'])) {
            return null;
        }

        if ($this->config['manager'] === 'wordpress') {
            return [
                'type' => 'wordpress',
                'available' => (bool) $this->config['remoteLogin'],
            ];
        }

        if ($this->config['manager'] === 'drupal') {
            $port = config('app.env') === 'production' ? '' : ':8088';
            $path = '/loginbytoken/?temp_token=' . $user->temp_token . '&codpes=' . $user->codpes;

            return [
                'type' => 'drupal',
                'url' => 'https://' . $this->url . $port . $path,
            ];
        }

        return null;
    }

    /**
     * Acessor para criar ownerName
     */
    public function getOwnerNameAttribute()
    {
        return Pessoa::dump($this->owner)['nompes'] ?? 'Usuário ainda não fez login';
    }

    /**
     * Acessor para criar ownerEmail
     */
    public function getOwnerEmailAttribute()
    {
        return Pessoa::email($this->owner);
    }

    /**
     * Adiciona um codpes à lista numeros_usp sem salvar o objeto
     */
    public function addAdmin($codpes)
    {
        $numeros_usp = explode(',', $this->numeros_usp);
        if (!in_array($codpes, $numeros_usp)) {
            array_push($numeros_usp, $codpes);
        }
        $numeros_usp = array_map('trim', $numeros_usp);
        $numeros_usp = implode(',', $numeros_usp);
        $this->numeros_usp = $numeros_usp;
        return true;
    }

    /**
     * Remove um codpes da lista numeros_usp sem salvar o objeto
     */
    public function deleteAdmin($codpes)
    {
        $numeros_usp = explode(',', $this->numeros_usp);
        if (in_array($codpes, $numeros_usp)) {
            $key = array_search($codpes, $numeros_usp);
            unset($numeros_usp[$key]);
        }
        $numeros_usp = array_map('trim', $numeros_usp);
        $numeros_usp = implode(',', $numeros_usp);
        $this->numeros_usp = $numeros_usp;
        return true;
    }

    // public function config($array = [])
    // {
    //     $config = $this->config;
    //     if (empty($array)) {
    //         return $config;
    //     }
    //     foreach ($array as $k => $v) {
    //         $config[$k] = $v;
    //     }
    //     $this->config = $config;
    // }

    /**
     * Escopo que permite filtrar os sites a serem exibidos pelo codpes de quem estiver logado
     *
     * Deve ser chamado $sites = Site::allowed();, pode incluir outras entradas de query
     */
    public function scopeAllowed($query)
    {
        $user = Auth::user();
        if (!Gate::allows('admin')) {
            $query->OrWhere('owner', '=', $user->codpes);
            // melhorar essa query!!! está insegura
            $query->OrWhere('numeros_usp', 'LIKE', '%' . $user->codpes . '%');
            return $query;
        }
        return $query;
    }

    /**
     * Relacionamento com chamados
     */
    public function chamados()
    {
        return $this->hasMany('App\Models\Chamado');
    }

    /**
     * Relacionamento com users
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function categorias()
    {
        return [
            'Grupo de estudo',
            'Grupo de pesquisa',
            'Departamento',
            'Administrativo',
            'Centro',
            'Associação',
            'Laboratório',
            'Comissão',
            'Evento',
            'Programa de Pós-Graduação',
        ];
    }

    public static function status()
    {
        return [
            'Aprovado - Em Processamento',
            'Aprovado - Habilitado',
            'Aprovado - Desabilitado',
            'Solicitado',
        ];
    }
}
