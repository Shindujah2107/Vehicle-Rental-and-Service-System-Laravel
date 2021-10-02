<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = ['name', 'image', 'date', 'car_name', 'price', 'capacity', 'description', 'available', 'status'];


    public function service_bookings()
    {
        return $this->hasMany('App\Model\ServiceBooking');
    }

}
