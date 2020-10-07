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
        $deleta_admin = User::where('codpes',$this->deleta_admin)->first();

        $subject = "Removido um administrador de conteúdo do site {$this->site->dominio}" . config('sites.dnszone');
        $user = User::where('codpes',$this->site->owner)->first();

        return $this->view('emails.deleta_admin')
                    ->to(config('mail.reply_to.address'))
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->cc([$user->email, $deleta_admin->email])
                    ->subject($subject)
                    ->with([
                        'site'              => $this->site,
                        'name'              => $user->name,
                        'nusp'              => $user->codpes,
                        'name_deleta_admin' => $deleta_admin->name,
                        'nusp_deleta_admin' => $deleta_admin->codpes,
                    ]);
    }
}
