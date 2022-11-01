<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		if(isset($this->data['replyTo']) && !empty($this->data['replyTo'])){
			return $this->from('no-reply@artfora.net')->replyTo($this->data['replyTo'], $this->data['username'])->subject($this->data['subject'])->view('emails.view')->with('data', $this->data);
		} else {
			return $this->from('no-reply@artfora.net')->subject($this->data['subject'])->view('emails.view')->with('data', $this->data);
		}
    }
}

?>