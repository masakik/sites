<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Site;
use GuzzleHttp\Client;

class AdminController extends Controller
{
    public function listaSites()
    {
        $client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br/',
        ]);
        $res = $client->request('GET','/aegir/saas/site.json', ['query' => ['api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ']]);
        $sites_aegir = json_decode($res->getBody());

        $dnszone = env('DNSZONE');
        $sites = Site::all()->sortBy('dominio');
        return view('admin/lista-sites', compact('sites','dnszone','sites_aegir'));
    }

    public function listaTodosSites()
    {
        $dnszone = env('DNSZONE');
        $sites = Site::all()->sortBy('dominio');
        return view('admin/lista-todos-sites', compact('sites','dnszone'));
    }

    public function cloneSite(Site $site)
    {
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      $site_modelo = env('SITE_MODELO');
      $id_node_bd = env('ID_NODE_BD');
      $id_node_plataforma = env('ID_NODE_PLATAFORMA');
      $cliente_email = \Auth::user()->email;
      $cliente_nome = \Auth::user()->name;      

      $client = new Client([
           'base_uri' => 'http://aegir.fflch.usp.br'
      ]);

      $res = $client->request('POST','/aegir/saas/task/', [
          'form_params' => [
              'target' => $site_modelo,
              'type' => 'clone',
              'options[new_uri]' => $alvo,
              'options[database]' => $id_node_bd,
              'options[target_platform]' => $id_node_plataforma,
              'options[client_email]' => $cliente_email,
              'options[client_name]' => $cliente_nome,
              'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
          ]
      ]);

      return redirect('/sites');
    }

    public function disableSite(Site $site)
    {
        $dnszone = env('DNSZONE');
        $alvo = $site->dominio . $dnszone;

        $client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br'
        ]);

        $res = $client->request('POST','/aegir/saas/task/', [
            'form_params' => [ 
                'target' => $alvo,
                'type' => 'disable',
                'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
            ]   
        ]);

        //$result = json_decode($res->getBody()->getContents());
        return redirect('/');
    }

    public function enableSite(Site $site)
    {
        $dnszone = env('DNSZONE');
        $alvo = $site->dominio . $dnszone;

        $client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br'
        ]);

        $res = $client->request('POST','/aegir/saas/task/', [
            'form_params' => [ 
                'target' => $alvo,
                'type' => 'enable',
                'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
            ]
        ]);
        return redirect('/');
    }

    public function deleteSite(Site $site)
    {
        $dnszone = env('DNSZONE');
        $alvo = $site->dominio . $dnszone;

        $client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br'
        ]);

        $res = $client->request('POST','/aegir/saas/task/', [
            'form_params' => [
                'target' => $alvo,
                'type' => 'delete',
                'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
            ]
        ]);
        return redirect('/');
    }
}

