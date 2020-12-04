<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeletaAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $site;
    public $user;
    public $deleta_admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site, $deleta_admin)
    {
        $this->site = $site;
        $this->deleta_admin = $deleta_admin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {       
        $subject = "Removido um administrador de conteúdo do site {$this->site->dominio}" . config('sites.dnszone');
        $user = User::where('codpes',$this->site->owner)->first();
        $deleta_admin = User::where('codpes',$this->deleta_admin)->first();

        $to = array();
        if ($user){
            $owner_nusp = $user->codpes;
            $owner_nome = $user->name;
            array_push($to, $user->email);
        }
        else{
            $owner_nusp = "Usuário ainda não fez login";
            $owner_nome = "Usuário ainda não fez login";   
            array_push($to, config('mail.reply_to.address'));
        }

        if($deleta_admin){
            $admin_nusp = $deleta_admin->codpes;
            $admin_nome = $deleta_admin->name;
            array_push($to, $deleta_admin->email);
        }
        else{
            $admin_nusp = $this->deleta_admin;
            $admin_nome = "Usuário ainda não fez login";
        }

        return $this->view('emails.deleta_admin')
                    ->to($to)
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->subject($subject)
                    ->with([
                        'site'              => $this->site,
                        'name'              => $owner_nome,
                        'nusp'              => $owner_nusp,
                        'name_deleta_admin' => $admin_nome,
                        'nusp_deleta_admin' => $admin_nusp,
                    ]);
    }
}
