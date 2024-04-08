<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RDSInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $ec2_ip_address;
    private $phpmyadmin_url;
    private $name;
    private $passwd;

    public function __construct($ec2_ip_address, $phpmyadmin_url, $name, $passwd)
    {
        $this->ec2_ip_address = $ec2_ip_address ?? '18.180.33.240';
        $this->phpmyadmin_url = $phpmyadmin_url;
        $this->passwd = $passwd;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Thông tin tài khoản RDS")
            ->view('email.info-rds-account')
            ->with([
                'ec2_ip_address' => $this->ec2_ip_address,
                'phpmyadmin_url' => $this->phpmyadmin_url,
                'name' => $this->name,
                'password' => $this->passwd
            ]);
    }
}
