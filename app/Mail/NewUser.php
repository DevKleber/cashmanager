<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUser extends Mailable
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
        return $this->view('emails.newUser')->subject('Dados de acesso ao PECUS!')->with([
            'user' => $this->user,
        ]);
    }
}
