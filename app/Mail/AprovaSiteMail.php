<?php

namespace App\Mail;

use App\Site;
use App\User;
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
        return $this->view('emails.aprova_site')
                    ->to(config('mail.reply_to.address'))
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->cc([$user->email])
                    ->subject($subject)
                    ->with([
                        'site' => $this->site,
                        'name' => $user->name,
                    ]);
    }
}
