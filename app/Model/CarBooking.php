<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CarBooking extends Model
{
    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'car_bookings';

    protected $fillable = ['arrival_date', 'departure_date', 'car_cost', 'status', 'payment', 'car_id', 'user_id'];

    /**
     * Get the gallery that owns the image.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

    public function car()
    {
        return $this->belongsTo('App\Model\Car');
    }

    public function review()
    {
        return $this->hasOne('App\Model\Review');
    }
}
