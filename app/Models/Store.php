<?php

    // app/Models/Store.php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Store extends Model
    {
        use HasFactory;
        protected $primaryKey = "store_id";
        protected $fillable = [
            'user_id',
            'platform_id',
            'domain',
            'lost_package_cost',
            'subscription_id',
            'billing_id',
            'api_key',
            'api_secret',
        ];


        public function billingInfo()
        {
            return $this->belongsTo(BillingInfo::class, 'billing_id', 'billing_id');
        }

        public function subscription()
        {
            return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
        }
        public function platform()
        {
            return $this->belongsTo(Platform::class, 'platform_id', 'platform_id');
        }

        public function feedbacks()
        {
            return $this->hasMany(Feedback::class, 'store_id', 'store_id');
        }

        public function queries()
        {
            return $this->hasMany(Query::class, 'store_id', 'store_id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'id', 'user_id');
        }
    }
