<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AprovaSiteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $site;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Site {$this->site->dominio}" . config('sites.dnszone') . " aprovado";
        $user = User::where('codpes',$this->site->owner)->first();

        $to = array();
        if ($user){
            $owner_nusp = $user->codpes;
            $owner_nome = $user->name;
            array_push($to, $user->email);
        }
        else {
            $owner_nusp = "Usuário ainda não fez login";
            $owner_nome = "Usuário ainda não fez login";
            array_push($to, config('mail.from.address'));
        }

        $user = User::where('codpes',$this->site->owner)->first();
            return $this->view('emails.aprova_site')
                        ->to($to)
                        ->from(config('mail.from.address'))
                        ->replyTo(config('mail.reply_to.address'))
                        ->subject($subject)
                        ->with([
                            'site' => $this->site,
                            'name' => $owner_nome,
                            'nusp' => $owner_nusp,
                        ]);
    }
}
