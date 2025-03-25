<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Exception extends Model
    {
        use HasFactory;

        protected $fillable = ['store_id', 'email_hash', 'phone_hash', 'type'];

        /**
         * Automatikusan hash-elés mentés előtt.
         */
        protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                if (isset($model->attributes['email'])) {
                    $model->email_hash = hash('sha256', $model->attributes['email']);
                    unset($model->attributes['email']); // Az eredeti adatot ne tároljuk el
                }

                if (isset($model->attributes['phone'])) {
                    $model->phone_hash = hash('sha256', $model->attributes['phone']);
                    unset($model->attributes['phone']); // Az eredeti adatot ne tároljuk el
                }
            });
        }

        public function store()
        {
            return $this->belongsTo(Store::class, 'store_id', 'store_id');
        }
    }
