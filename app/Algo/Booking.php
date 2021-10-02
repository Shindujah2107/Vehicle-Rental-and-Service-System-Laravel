<?php

namespace App\Algo;

use Carbon\Carbon;

class Booking
{
    protected $car_type;
    protected $new_arrival_date;
    protected $new_departure_date;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($car_type, $new_arrival_date, $new_departure_date)
    {
        $this->car_type = $car_type;
        $this->new_arrival_date = $new_arrival_date;
        $this->new_departure_date = $new_departure_date;
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
        return $this->car_available();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry, no cars are available in the given dates. Please try another date.';
    }

    public function car_available()
    {
        $this->cars_exist();
        foreach ($this->car_type->cars as $car) {

            if ($this->car_bookings_exist($car)) {
                if($this->car_bookings_check($car->car_bookings) == false)
                    continue;
            }

            return true;
        }
    }

    public function available_car_number()
    {
        $this->cars_exist();
        foreach ($this->car_type->cars as $car) {

            if ($this->car_bookings_exist($car)) {
                if($this->car_bookings_check($car->car_bookings) == false)
                    continue;
            }
            return $car->car_number;
        }
    }

    protected function cars_exist()
    {
        if (count($this->car_type->cars) > 0) {
            return true;
        }
        $this->message = "Sorry, there are no car of given type available.";
        return false;
    }

    protected function car_bookings_exist($car)
    {
        if (count($car->car_bookings) > 0) {
            return true;
        }
    }

    protected function car_bookings_check($car_bookings)
    {
        foreach ($car_bookings as $car_booking) {
            $old_arrival_date = Carbon::parse($car_booking->arrival_date)->format('Y/m/d');
            $old_departure_date = Carbon::parse($car_booking->departure_date)->format('Y/m/d');
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
