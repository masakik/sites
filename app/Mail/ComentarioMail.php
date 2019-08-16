<?php

namespace App\Mail;

use App\User;
use App\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComentarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $comentario;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Comentario $comentario, User $user)
    {
        $this->comentario = $comentario;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->comentario->chamado->status == 'fechado' ) {
            return $this->view('emails.comentario')
                        ->from(config('sites.email_principal'))
                        ->to([config('sites.email_principal'),$this->comentario->chamado->user->email])
                        ->subject("Chamado #{$this->comentario->id} fechado ({$this->comentario->chamado->site->dominio}" . config('sites.dnszone') . ")");
        }
            return $this->view('emails.comentario')
                        ->from(config('sites.email_principal'))
                        ->to([config('sites.email_principal'),$this->comentario->chamado->user->email])
                        ->subject("Novo comentário no chamado #{$this->comentario->id} ({$this->comentario->chamado->site->dominio}" . config('sites.dnszone') . ")");
    }
}
