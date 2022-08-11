<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        'manager' => 'local',
        'host' => 'não setado',
        'path' => 'não setado',
    ];

    public function getConfigAttribute($value)
    {
        $value = json_decode($value, TRUE);
        foreach ($this->configDefaults as $key=>$default) {
            $value[$key] = $value[$key] ?? $default;
        }
        return $value;
    }

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
            'Programa de Pós-Graduação'
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
