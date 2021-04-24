<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoverPassword extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.recoverPassword')->subject('Recuperação de senha!')->with([
            'user' => $this->user,
        ]);
    }
}
