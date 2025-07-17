<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiMagang extends Mailable implements ShouldQueue
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
        // Mulai dengan membuat email dasar
        $email = $this->subject('Update Status Pendaftaran Magang Anda')
            ->view('emails.notifikasi-magang');

        // Jika diterima dan ada file surat, lampirkan
        if (
            $this->internship->status === 'accepted' &&
            !empty($this->internship->acceptance_letter_file) &&
            file_exists(public_path("storage/{$this->internship->acceptance_letter_file}"))
        ) {
            $email->attach(
                public_path("storage/{$this->internship->acceptance_letter_file}"),
                ['as' => 'Surat Penerimaan Magang.pdf'] // Anda bisa memberi nama file lampiran
            );
        }

        return $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Magang BPS Demak',
        );
    }


}
