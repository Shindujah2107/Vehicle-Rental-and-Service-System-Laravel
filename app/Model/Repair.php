<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repairs';

    protected $fillable = ['repair_number', 'description', 'available', 'status', 'repair_type_id'];


    public function repair_type()
    {
        return $this->belongsTo('App\Model\RepairType');
    }

    public function repair_bookings()
    {
        return $this->hasMany('App\Model\RepairBooking');
    }

    public function reviewsrepairs()
    {
        return $this->hasManyThrough('App\Model\ReviewRepair', 'App\Model\RepairBooking');

    }

}
