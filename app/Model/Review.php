<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviews';

    protected $fillable = ['review', 'rating', 'approval_status', 'car_booking_id'];

    public function car_booking(){

        return $this->belongsTo('App\Model\CarBooking');
    }
 
}
