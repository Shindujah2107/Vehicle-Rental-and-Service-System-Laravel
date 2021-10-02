<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReviewRepair extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviewsrepairs';

    protected $fillable = ['reviewrepair', 'rating', 'approval_status', 'repair_booking_id'];

    public function repair_booking(){

        return $this->belongsTo('App\Model\RepairBooking');
    }
 
}
