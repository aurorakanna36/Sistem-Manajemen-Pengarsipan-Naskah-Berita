<?php

namespace App\Mail;

use App\Models\Guest;
use App\Models\Naskah;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusNaskahGuestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Naskah $naskah;
    public ?Guest $guest;
    public string $status;
    public ?string $catatan;
    public ?string $urlBerita;

    /**
     * Create a new message instance.
     */
    public function __construct(Naskah $naskah, ?Guest $guest, string $status, ?string $catatan = null, ?string $urlBerita = null)
    {
        $this->naskah = $naskah;
        $this->guest = $guest;
        $this->status = $status;
        $this->catatan = $catatan;
        $this->urlBerita = $urlBerita;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $judulCuplikan = \Illuminate\Support\Str::limit($this->naskah->judul, 40);

        if ($this->status === 'diterima' || $this->status === 'diterbitkan') {
            $subject = "Kabar Baik! Naskah '{$judulCuplikan}' Telah Disetujui - Harian Cakrawala";
        } elseif ($this->status === 'direvisi') {
            $subject = "Pemberitahuan: Naskah '{$judulCuplikan}' Memerlukan Revisi - Harian Cakrawala";
        } else {
            $subject = "Pemberitahuan Status Naskah '{$judulCuplikan}' - Harian Cakrawala";
        }

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
            view: 'emails.status_naskah_guest',
            with: [
                'naskah' => $this->naskah,
                'guest' => $this->guest,
                'status' => $this->status,
                'catatan' => $this->catatan,
                'urlBerita' => $this->urlBerita,
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
