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
        $res = $client->request('GET','/aegir/saas/site.json', ['query' => ['api-key' => 'SEU-TOKEN']]);
        $sites_aegir = json_decode($res->getBody());
        //dd($sites_aegir);

        $dnszone = env('DNSZONE');
        $sites = Site::all();
        return view('admin/lista-sites', compact('sites','dnszone','sites_aegir'));
    }
}
