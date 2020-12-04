<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Chamado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChamadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chamado;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Chamado $chamado, User $user)
    {
        $this->chamado = $chamado;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $to = [$this->user->email];
        // Responsável pelo site
        $codpes = $this->chamado->site->owner;
        $owner = User::where('codpes', $codpes)->first();
        if ($owner) {
            $to[] = $owner->email;
        }
        array_push($to, config('mail.reply_to.address'));
        $to = array_unique($to);
        $subject = "Atualização do chamado {$this->chamado->site->id}/{$this->chamado->id} para o site {$this->chamado->site->dominio}" . config('sites.dnszone');

        return $this->view('emails.chamado')
                    ->to($to)
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->subject($subject)
                    ->with([
                        'chamado' => $this->chamado,
                        'user' => $this->user,
                    ]);
    }
}
