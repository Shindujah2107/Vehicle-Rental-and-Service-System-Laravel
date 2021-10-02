<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'service_bookings';

    protected $fillable = ['number_of_vehicles', 'total_cost', 'status', 'payment', 'service_id', 'user_id'];

    /**
     * Get the gallery that owns the image.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * Get the gallery that owns the image.
     */
    public function service()
    {
        return $this->belongsTo('App\Model\Service');
    }
}
