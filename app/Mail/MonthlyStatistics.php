<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyStatistics extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $cycleStartDate;
    public $cycleEndDate;
    public $stores;

    public function __construct($user, $cycleStartDate, $cycleEndDate, $stores)
    {
        $this->userName = $user->first_name;
        $this->cycleStartDate = Carbon::parse($cycleStartDate)->format('F j, Y');
        $this->cycleEndDate = Carbon::parse($cycleEndDate)->format('F j, Y');
        $this->stores = $stores;
    }

    public function build()
    {
        return $this->subject('Monthly Statistics')
            ->view('emails.monthly_statistics');
    }
}
