<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = ['name', 'icon', 'description', 'status'];

    public function car_types()
    {
        return $this->belongsToMany('App\Model\CarType', 'facility_car_type')->withTimestamps();
    }

}
