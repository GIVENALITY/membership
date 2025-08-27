<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Member $member, public string $subjectLine, public string $bodyText)
    {
    }

    public function build()
    {
        $hotel = $this->member->hotel;
        
        $mail = $this->subject($this->subjectLine)
            ->from(config('mail.from.address'), $hotel->name)
            ->replyTo($hotel->reply_to_email ?? config('mail.from.address'), $hotel->name)
            ->view('emails.welcome_member', [
                'member' => $this->member,
                'bodyText' => nl2br(e($this->bodyText)),
            ]);

        if ($this->member->card_image_path) {
            $path = storage_path('app/public/' . $this->member->card_image_path);
            if (is_file($path)) {
                $mail->attach($path, [
                    'as' => 'MembershipCard_' . $this->member->membership_id . '.jpg',
                    'mime' => 'image/jpeg',
                ]);
            }
        }

        return $mail;
    }
} 