<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiKontributorMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $status;
    public ?string $catatanAdmin;
    public ?string $emailCakrawala;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $status, ?string $catatanAdmin = null, ?string $emailCakrawala = null)
    {
        $this->user = $user;
        $this->status = $status;
        $this->catatanAdmin = $catatanAdmin;
        $this->emailCakrawala = $emailCakrawala;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->status === 'verified')
            ? 'Selamat! Pendaftaran Kontributor Harian Cakrawala Diterima'
            : 'Pemberitahuan Status Pendaftaran Kontributor Harian Cakrawala';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verifikasi_kontributor',
            with: [
                'user' => $this->user,
                'status' => $this->status,
                'catatanAdmin' => $this->catatanAdmin,
                'emailCakrawala' => $this->emailCakrawala,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
