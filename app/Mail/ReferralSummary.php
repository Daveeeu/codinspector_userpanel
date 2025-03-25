<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $cycleStartDate;
    public $cycleEndDate;
    public $referrals;
    public $validityDays;

    public function __construct($user, $cycleStartDate, $cycleEndDate, $referrals, $validityDays)
    {
        $this->userName = $user->first_name;
        $this->cycleStartDate = Carbon::parse($cycleStartDate)->format('F j, Y');
        $this->cycleEndDate = Carbon::parse($cycleEndDate)->format('F j, Y');
        $this->referrals = $referrals;
        $this->validityDays = $validityDays;
    }

    public function build()
    {
        return $this->subject('Your Monthly Referral Summary')
            ->view('emails.referral_summary');
    }
}
