<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $user;
    public $userBrowserInfo;
    public $userIpInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $user = null, $userBrowserInfo = null, $userIpInfo = null)
    {
        $this->code = $code;
        $this->user = $user;
        $this->userBrowserInfo = $userBrowserInfo;
        $this->userIpInfo = $userIpInfo;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Forget Password',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.auth.forget-password',
            with: [
                'code' => $this->code,
                'user' => $this->user,
                'operating_system' => $this->userBrowserInfo['os_platform'],
                'browser' => $this->userBrowserInfo['browser'],
                'ip' => $this->userIpInfo['ip'],
                'time' => $this->userIpInfo['time']
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
