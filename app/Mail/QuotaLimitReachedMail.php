<?php

    namespace App\Mail;

    use Carbon\Carbon;
    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class QuotaLimitReachedMail extends Mailable
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
            return $this->subject('Notice: Your quota has been fully used')
                ->view('emails.quota_limit_reached');
        }
    }
