<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClosedInvoice extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $fatura;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$fatura)
    {
        $this->user = $user;
        $this->fatura = $fatura;
    }

    public function build()
    {
        return $this->from('poderesponder@desbankei.com.br','Desbankei')
        ->subject('[Desbankei] Sua fatura está fechada!')
        ->markdown('desbankei.closed_invoice',['user'=>$this->user,'fatura'=>$this->fatura])->attach('../'.$this->fatura->url, [
            'as' => 'fatura.pdf',
            'mime' => 'application/pdf',
        ]);
    }
}
