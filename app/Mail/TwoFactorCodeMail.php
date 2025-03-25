<?php
    namespace App\Mail;

    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class TwoFactorCodeMail extends Mailable
    {
        use SerializesModels;

        public $twoFactorCode;
        public $userName;

        /**
         * Create a new message instance.
         *
         * @param  string  $twoFactorCode
         * @return void
         */
        public function __construct($twoFactorCode,$user)
        {
            $this->twoFactorCode = $twoFactorCode;
            $this->userName = $user->first_name;
        }

        /**
         * Build the message.
         *
         * @return $this
         */
        public function build()
        {
            return $this->subject('Your Two-Factor Authentication Code')
                ->view('emails.two_factor_code');
        }
    }
