<?php

namespace App\Mail;

use App\Models\StudentUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentDisapprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct(StudentUser $student)
    {
        $this->student = $student;
    }

    public function build()
    {
        return $this->subject("⚠️ Your SYBORG Registration")
                    ->view('emails.student_disapproved');
    }
}
