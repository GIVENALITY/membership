<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\EmailLog;
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
        
        // Determine reply-to email
        $replyToEmail = $hotel->reply_to_email ?? $hotel->email ?? config('mail.from.address');
        $replyToName = $hotel->name;
        
        // Log the email
        EmailLog::create([
            'hotel_id' => $hotel->id,
            'email_type' => 'welcome',
            'subject' => $this->subjectLine,
            'content' => $this->bodyText,
            'recipient_email' => $this->member->email,
            'recipient_name' => $this->member->first_name . ' ' . $this->member->last_name,
            'member_id' => $this->member->id,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'email_type' => 'welcome',
                'member_created_at' => $this->member->created_at
            ]
        ]);
        
        $mail = $this->subject($this->subjectLine)
            ->from(config('mail.from.address'), $hotel->name)
            ->replyTo($replyToEmail, $replyToName)
            ->view('emails.welcome_member', [
                'member' => $this->member,
                'bodyText' => $this->bodyText,
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