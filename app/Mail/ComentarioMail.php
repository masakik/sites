<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Comentario;
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
        // emails dos envolvidos nos comentários
        // quem abriu o chamado sempre recebe email
        $to = [$this->comentario->chamado->user->email];

        // Responsável pelo site
        $codpes = $this->comentario->chamado->site->owner;
        $owner = User::where('codpes', $codpes)->first();
        if ($owner) {
            $to[] = $owner->email;
        }

        foreach($this->comentario->chamado->comentarios as $comment){
            $to[] = $comment->user->email;
        }
        array_push($to, config('mail.reply_to.address'));
        $to = array_unique($to);

        // Monta título do email
        if($this->comentario->chamado->status == 'fechado' ) {
            $subject = "Chamado {$this->comentario->chamado->site->id}/{$this->comentario->chamado->id}
                        fechado ({$this->comentario->chamado->site->dominio}" . config('sites.dnszone') . ")";
        } else {
            $subject = "Novo comentário no chamado {$this->comentario->chamado->site->id}/{$this->comentario->chamado->id}
                        ({$this->comentario->chamado->site->dominio}" . config('sites.dnszone') . ")";
        }

        return $this->view('emails.comentario')
                    ->to($to)
                    ->from(config('mail.from.address'))
                    ->replyTo(config('mail.reply_to.address'))
                    ->subject($subject)
                    ->with([
                        'comentario' => $this->comentario,
                        'user' => $this->user,
                    ]);

    }
}
