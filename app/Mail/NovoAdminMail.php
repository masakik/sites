<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NovoAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $site;
    public $user;
    public $novo_admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site, $novo_admin)
    {
        $this->site = $site;
        $this->novo_admin = $novo_admin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        $subject = "Adicionado novo administrador de conteúdo ao site {$this->site->dominio}" . config('sites.dnszone');
        $user = User::where('codpes',$this->site->owner)->first();
        $novo_admin = User::where('codpes',$this->novo_admin)->first();

        $cc = array();
        if ($user){
            $owner_nusp = $user->codpes;
            $owner_nome = $user->name;
            array_push($cc, $user->email);
        }
        else{
            $owner_nusp = "Usuário ainda não fez login";
            $owner_nome = "Usuário ainda não fez login";   
        }

        if($novo_admin){
            $admin_nusp = $novo_admin->codpes;
            $admin_nome = $novo_admin->name;
            array_push($cc, $novo_admin->email);
        }
        else{
            $admin_nusp = $this->novo_admin;
            $admin_nome = "Usuário ainda não fez login";
        }
            
        array_push($cc, config('mail.from.address'));
  
        return $this->view('emails.novo_admin')
                    ->to(config('mail.reply_to.address'))
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->cc($cc)
                    ->subject($subject)
                    ->with([
                        'site'            => $this->site,
                        'name'            => $owner_nome,
                        'nusp'            => $owner_nusp,
                        'name_novo_admin' => $admin_nome,
                        'nusp_novo_admin' => $admin_nusp,
                    ]);
    }
}
