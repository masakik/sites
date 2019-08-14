<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Site extends Model
{
    public function scopeAllowed($query)
    {
        $user = Auth::user();
        if (!Gate::allows('admin')) {
            $query->OrWhere('owner', '=', $user->codpes);
            // melhorar essa query!!! está insegura
            $query->OrWhere('numeros_usp', 'LIKE', '%'.$user->codpes.'%');
            return $query;
        }
        return $query;
    }

    public function chamados()
    {
        return $this->hasMany('App\Chamado');
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}
