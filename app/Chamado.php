<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chamado extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }
}
