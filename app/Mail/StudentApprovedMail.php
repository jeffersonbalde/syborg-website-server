<?php

namespace App\Mail;

use App\Models\StudentUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentApprovedMail extends Mailable
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

        /**
     * Build the message.
     */
    public function build()
    {
        $qrUrl = null;
        if ($this->student->qr_code) {
            $qrUrl = 'https://syborg-server-wlpe4.ondigitalocean.app/' . $this->student->qr_code;
        }

        return $this->subject("✅ You're Approved to SYBORG!")
            ->view('emails.student_approved')
            ->with([
                'student' => $this->student,
                'qr_code_url' => $qrUrl,
            ]);
    }


}
