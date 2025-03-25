<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeletedStore extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $storeDomain;


    public function __construct($user, $store)
    {
        $this->userName = $user->first_name;
        $this->storeDomain = $store->domain;
    }

    public function build()
    {
        return $this->subject('Store Deleted')
            ->view('emails.store_deleted');
    }
}
