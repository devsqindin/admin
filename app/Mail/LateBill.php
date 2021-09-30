<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LateBill extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $dados;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$dados)
    {
        $this->user = $user;
        $this->dados = $dados;
    }

    public function build()
    {
        return $this->from('poderesponder@desbankei.com.br','Desbankei')
        ->subject('[Desbankei] Antecipação de fatura')
        ->markdown('desbankei.latebill',['user'=>$this->user,'dados'=>$this->dados]);
    }
}
