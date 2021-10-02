<?php

namespace App\Rules;

use App\Algo\BookingRepair;
use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class RepairAvailableRule implements Rule
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
        return 'Sorry, no cars are available in the given dates. Please try another date.';
    }

    public function repair_available()
    {
        $booking = new BookingRepair($this->repair_type, $this->new_arrival_date, $this->new_departure_date,$this->vehicle_type);
        return $booking->repair_available();
    }


}
