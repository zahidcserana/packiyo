<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnOrderWithLabelsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $return;

    public $returnLabels;

    public $headerImage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $return, $returnLabels)
    {
        $this->subject = $subject;
        $this->return = $return;
        $this->returnLabels = $returnLabels;
        $this->headerImage = asset('/img/email_header_img1.png');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.returnOrderWithLabels')
            ->subject($this->subject);
    }
}
