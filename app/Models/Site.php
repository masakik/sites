<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        'manager' => 'wordpress',
        'host' => 'localhost',
        'port' => '2221',
        'path' => '/home/dominio',
        'suUser' => null,
        'status' => '?', // mostra se tem erros no site
        'statusMsg' => '',
    ];

    public function getConfigAttribute($value)
    {
        $value = json_decode($value, true);
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
    public function addAdmin($codpes) {
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
    public function deleteAdmin($codpes) {
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
