<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrocaResponsavelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $site;
    public $user;
    //public $responsavel_anterior;
    public $novo_responsavel;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site, $novo_responsavel)
    {
        $this->site = $site;
        $this->novo_responsavel = $novo_responsavel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $novo_responsavel = User::where('codpes',$this->novo_responsavel)->first();

        $subject = "Troca do Reponsável do Site {$this->site->dominio}" . config('sites.dnszone');
        $user = User::where('codpes',$this->site->owner)->first();

        return $this->view('emails.troca_responsavel')
                    ->to(config('mail.reply_to.address'))
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->cc([$user->email, $novo_responsavel->email])
                    ->subject($subject)
                    ->with([
                        'site' => $this->site,
                        'name' => $user->name,
                        'name_novo_responsavel' => $novo_responsavel->name,
                    ]);
    }
}
