<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiMagang extends Mailable
{
    use Queueable, SerializesModels;

    public $internship;

    /**
     * Create a new message instance.
     */
    public function __construct($internship)
    {
        $this->internship = $internship;
    }

    public function build()
    {
        return $this->subject('Notifikasi Pendaftaran Magang')
            ->view('emails.notifikasi-magang');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Magang',
        );
    }


}
