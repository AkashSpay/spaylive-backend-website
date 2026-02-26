<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateTemplateMail extends Mailable
{
    use SerializesModels;

    public $candidate;
    public $subjectLine;
    public $messageBody;

    public function __construct($candidate, $subjectLine, $messageBody)
    {
        $this->candidate = $candidate;
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.candidate-template');
    }
}