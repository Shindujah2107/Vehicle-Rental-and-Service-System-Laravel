<?php

namespace App\Algo;

use Carbon\Carbon;

class BookingRepair
{
    protected $repair_type;
    protected $new_arrival_date;
    protected $new_departure_date;
	protected $vehicle_type;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($repair_type, $new_arrival_date, $new_departure_date,$vehicle_type)
    {
        $this->repair_type = $repair_type;
        $this->new_arrival_date = $new_arrival_date;
        $this->new_departure_date = $new_departure_date;
		$this->vehicle_type = $vehicle_type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->repair_available();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry, no repairs are available in the given dates. Please try another date.';
    }

    public function repair_available()
    {
        $this->repairs_exist();
        foreach ($this->repair_type->repairs as $repair) {

            if ($this->repair_bookings_exist($repair)) {
                if($this->repair_bookings_check($repair->repair_bookings) == false)
                    continue;
            }

            return true;
        }
    }

    public function available_repair_number()
    {
        $this->repairs_exist();
        foreach ($this->repair_type->repairs as $repair) {

            if ($this->repair_bookings_exist($repair)) {
                if($this->repair_bookings_check($repair->repair_bookings) == false)
                    continue;
            }
            return $repair->repair_number;
        }
    }

    protected function repairs_exist()
    {
        if (count($this->repair_type->repairs) > 0) {
            return true;
        }
        $this->message = "Sorry, there are no repair of given type available.";
        return false;
    }

    protected function repair_bookings_exist($repair)
    {
        if (count($repair->repair_bookings) > 0) {
            return true;
        }
    }

    protected function repair_bookings_check($repair_bookings)
    {
        foreach ($repair_bookings as $repair_booking) {
            $old_arrival_date = Carbon::parse($repair_booking->arrival_date)->format('Y/m/d');
            $old_departure_date = Carbon::parse($repair_booking->departure_date)->format('Y/m/d');
            if ($this->new_arrival_date < $old_arrival_date) {
                if ($this->new_departure_date > $old_arrival_date)
                    return false;
            } elseif ($this->new_arrival_date > $old_arrival_date) {
                if ($this->new_arrival_date < $old_departure_date) {
                    return false;
                }
            } elseif ($this->new_arrival_date == $old_arrival_date) {
                return false;
            }
        }
        return true;
    }
}
