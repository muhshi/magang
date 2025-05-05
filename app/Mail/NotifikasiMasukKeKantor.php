<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiMasukKeKantor extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $internship;

    public function __construct($internship)
    {
        $this->internship = $internship;
    }

    public function build()
    {
        $letterFilePath = public_path("storage/{$this->internship->letter_file}");
        $photoFilePath = public_path("storage/{$this->internship->photo_file}");
        // dd(file_exists($letterFilePath), file_exists($photoFilePath), $letterFilePath, $photoFilePath);
        return
            $this->subject('Pendaftaran Magang Baru')
                ->attach(public_path("storage/{$this->internship->photo_file}"))
                ->attach(public_path("storage/{$this->internship->letter_file}"))
                ->view('emails.kantor-notif');
    }
}
