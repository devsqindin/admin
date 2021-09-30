<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->from('poderesponder@desbankei.com.br','Desbankei')
        ->subject('[Desbankei] Alteração de e-mail da conta')
        ->markdown('desbankei.new_email',['user'=>$this->user]);
    }
}
