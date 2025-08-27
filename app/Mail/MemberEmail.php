<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Member;

class MemberEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $emailData;
    public $isCustomRecipient;

    /**
     * Create a new message instance.
     */
    public function __construct($member, array $emailData)
    {
        $this->member = $member;
        $this->emailData = $emailData;
        $this->isCustomRecipient = !($member instanceof Member);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hotel = $this->member->hotel ?? auth()->user()->hotel;
        
        return new Envelope(
            subject: $this->emailData['subject'],
            from: new Address(
                $hotel->email ?? config('mail.from.address'),
                $hotel->name ?? config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    $hotel->reply_to_email ?? $hotel->email ?? config('mail.from.address'),
                    $hotel->name ?? config('mail.from.name')
                )
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-email',
            with: [
                'member' => $this->member,
                'emailData' => $this->emailData,
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
