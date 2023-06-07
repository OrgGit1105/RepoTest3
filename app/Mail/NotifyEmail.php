<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyEmail extends Mailable
{
  use Queueable, SerializesModels;

  public $details;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($details)
  {
    $this->details = $details;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject('Error: Failed to get digitaco data (Retirement risk prediction system)')
      ->view('email.send-notify');
  }
}
