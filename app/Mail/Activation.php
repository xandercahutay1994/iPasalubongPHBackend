<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Activation extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */ 
    public function __construct($email, $type)
    {
        $this->email = $email;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('xandercahutay1994@gmail.com')
            ->view('email.activation')
            ->with(array('email' => $this->email, 'type' => $this->type));
    }
}
