<?php

namespace App\Services;

use App\Models\Site;

class LocalManager
{
    protected static function getSite($dominio)
    {
        $dominio = str_replace(config('sites.dnszone'), '', $dominio);
        return Site::where('dominio', $dominio)->first();
    }

    public static function verificaStatus($dominio)
    {
        return SELF::getSite($dominio)->status;
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
