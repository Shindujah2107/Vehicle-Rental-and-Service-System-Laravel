<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RepairBooking extends Model
{
    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'repair_bookings';

    protected $fillable = ['vehicletype','arrival_date', 'departure_date', 'repair_cost', 'status', 'payment', 'repair_id', 'user_id'];

    /**
     * Get the gallery that owns the image.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

    public function repair()
    {
        return $this->belongsTo('App\Model\Repair');
    }

    public function review()
    {
        return $this->hasOne('App\Model\ReviewRepair');
    }
}
