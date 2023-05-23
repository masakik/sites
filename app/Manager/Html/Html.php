<?php
namespace App\Manager\Html;

use App\Manager\Manager;

class Html extends Manager
{
    // vamos ver os arquivos no site
    public $files;

    public function __construct($site)
    {
        parent::__construct($site);
        $this->info();
    }

    public function info() {

    }

    public function exec(String $acao)
    {

    }
}
