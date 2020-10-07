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
        $novo_admin = User::where('codpes',$this->novo_admin)->first();

        $subject = "Adicionado novo administrador de conteúdo ao site {$this->site->dominio}" . config('sites.dnszone');
        $user = User::where('codpes',$this->site->owner)->first();

        return $this->view('emails.novo_admin')
                    ->to(config('mail.reply_to.address'))
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->cc([$user->email, $novo_admin->email])
                    ->subject($subject)
                    ->with([
                        'site'              => $this->site,
                        'name'              => $user->name,
                        'nusp'              => $user->codpes,
                        'name_novo_admin' => $novo_admin->name,
                        'nusp_novo_admin' => $novo_admin->codpes,
                    ]);
    }
}
