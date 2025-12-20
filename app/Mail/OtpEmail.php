<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $type;

    /**
     * Create a new message instance.
     * 
     * @param string $otp  Kode OTP 6 digit
     * @param string $type Tipe OTP: 'register', 'login', 'reset_password'
     */
    public function __construct(string $otp, string $type = 'register')
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'register'       => 'Verifikasi Email - Klinik ZIP',
            'login'          => 'Kode OTP Login - Klinik ZIP',
            'reset_password' => 'Reset Password - Klinik ZIP',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Kode OTP - Klinik ZIP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp'  => $this->otp,
                'type' => $this->type,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}