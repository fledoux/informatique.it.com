<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordGlobalMail extends Mailable
{
	use Queueable, SerializesModels;

	public $title;
	public $content;
	public $token;
	public $user;

	/**
	 * Create a new message instance.
	 */
	public function __construct($user, $token)
	{
		$this->user = $user;
		$this->token = $token;
		
		// Titre fixe
		$this->title = __('passwords.Reset Password');

		// Construire le contenu avec le lien de réinitialisation
		$resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
		
		// Récupérer la salutation et le prénom
		$greeting = \App\Helpers\Helper::getGreeting();
		$firstname = $user->firstname ?? explode(' ', $user->name)[0];

		$this->content = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; line-height: 1.6;'>
                <p style='margin-bottom: 1rem;'>
                    {$greeting} {$firstname},
                </p>
                
                <p style='margin-bottom: 1.5rem;'>
                    " . __('passwords.You are receiving this email because we received a password reset request for your account') . ".
                </p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetUrl}' 
                       style='display: inline-block; padding: 12px 24px; background-color: #ff4c00 !important; color: #ffffff !important; text-decoration: none; border-radius: 0.375rem; font-weight: 600;'>
                        " . __('passwords.Reset Password') . "
                    </a>
                </div>
                
                <div style='background-color: #fff3cd !important; border: 1px solid #ffeaa7; border-radius: 0.375rem; padding: 15px; margin: 20px 0;'>
                    <p style='color: #856404 !important; font-size: 14px; margin: 0;'>
                        <strong>⚠️ " . __('passwords.Important') . " :</strong> 
                        " . __('passwords.This password reset link will expire in') . " 
                        " . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . " 
                        " . __('passwords.minutes') . ".
                    </p>
                </div>
                
                <p style='color: #6c757d; font-size: 14px; margin-bottom: 1.5rem;'>
                    " . __('passwords.If you did not request a password reset, no further action is required') . ".
                </p>
                
                <p style='margin-bottom: 0;'>
                    " . __('global.ThanksCrew') . "
                </p>
            </div>
        ";
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: __('passwords.Reset Password'),
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			view: 'emails.global',
			with: [
				'title' => $this->title,
				'content' => $this->content,
			]
		);
	}
}
