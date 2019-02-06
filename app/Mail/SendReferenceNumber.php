<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReferenceNumber extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $referenceNum;
    public $payment;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $referenceNum, $payment)
    {
        $this->email = $email;
        $this->referenceNum = $referenceNum;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('xandercahutay1994@gmail.com')
            ->view('email.sendReference')
            ->with(array('email' => $this->email, 'referenceNum' => $this->referenceNum, 'payment' => $this->payment));
    }
}
