<?php

namespace App\Services;

use App\Aegir\Aegir;
use App\Models\Site;
use App\Jobs\deletaSiteAegir;
use App\Jobs\instalaSiteAegir;
use App\Jobs\habilitaSiteAegir;
use App\Jobs\desabilitaSiteAegir;

class SiteManager
{

    protected static $manager;

    protected static function getManager()
    {
        if (is_null(SELF::$manager)) {
            switch (config('sites.siteManager')) {
                case 'aegir':
                    SELF::$manager = new Aegir;
                    break;
                default:
                    SELF::$manager = new LocalManager;
            }
        }
        return SELF::$manager;
    }

    public static function verificaStatus(Site $site)
    {
        switch (config('sites.siteManager')) {
            case 'aegir':
                return SELF::getManager()->verificaStatus($site->dominio.config('sites.dnszone'));
                break;
            default:
                return SELF::getManager()->verificaStatus($site);
        }
    }

    public static function desabilita(Site $site)
    {
        switch (config('sites.siteManager')) {
            case 'aegir':
                desabilitaSiteAegir::dispatch($site->dominio . config('sites.dnszone'));
                break;
            default:
                SELF::getManager()->desabilita($site);
        }
    }

    public static function habilita(Site $site)
    {
        switch (config('sites.siteManager')) {
            case 'aegir':
                habilitaSiteAegir::dispatch($site->dominio . config('sites.dnszone'));
                break;
            default:
                SELF::getManager()->habilita($site);
        }
    }

    public static function instala(Site $site)
    {
        switch (config('sites.siteManager')) {
            case 'aegir':
                instalaSiteAegir::dispatch($site->dominio . config('sites.dnszone'));
                break;
            default:
                SELF::getManager()->instala($site);
        }
    }

    public static function deleta(Site $site) {
        switch (config('sites.siteManager')) {
            case 'aegir':
                deletaSiteAegir::dispatch($site->dominio . config('sites.dnszone'));
                break;
            default:
                SELF::getManager()->deleta($site);
        }
    }
}
