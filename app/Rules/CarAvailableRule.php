<?php

namespace App\Rules;

use App\Algo\Booking;
use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class CarAvailableRule implements Rule
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
        $booking = new Booking($this->car_type, $this->new_arrival_date, $this->new_departure_date);
        return $booking->car_available();
    }


}
