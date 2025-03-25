<?php

    namespace App\Mail;

    use Carbon\Carbon;
    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class QuotaWarningMail extends Mailable
    {
        use Queueable, SerializesModels;

        public $userName;
        public $storeDomain;
        public $renewalDate;

        public function __construct($user, $store, $renewalDate)
        {
            $this->userName = $user->first_name;
            $this->storeDomain = $store->domain;
            $this->renewalDate = Carbon::parse($renewalDate)->format('F j, Y');
        }

        public function build()
        {
            return $this->subject('Warning: You have used 80% of your quota')
                ->view('emails.quota_warning');
        }
    }
