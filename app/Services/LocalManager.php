<?php

namespace App\Services;

use App\Models\Site;

class LocalManager
{
    public static function verificaStatus($site)
    {
        if ($site->status == 'Servidor Offline') {
            $site->status = 'Aprovado - Habilitado';
            $site->save();
        }
        return $site->status;
    }

    public static function desabilita($site)
    {
        $site->status = 'Aprovado - Desabilitado';
        $site->save();
    }

    public static function habilita($site)
    {
        $site->status = 'Aprovado - Habilitado';
        $site->save();
    }

    public function instala($site)
    {
        $site->status = 'Aprovado - Habilitado';
        $site->save();
    }

    public function deleta($site)
    {
        //
    }
}
