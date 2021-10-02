<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cars';

    protected $fillable = ['car_number', 'description', 'available', 'status', 'car_type_id'];


    public function car_type()
    {
        return $this->belongsTo('App\Model\CarType');
    }

    public function car_bookings()
    {
        return $this->hasMany('App\Model\CarBooking');
    }

    public function reviews()
    {
        return $this->hasManyThrough('App\Model\Review', 'App\Model\CarBooking');

    }

}
