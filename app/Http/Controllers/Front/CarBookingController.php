<?php

namespace App\Http\Controllers\Front;

use App\Algo\Booking;
use App\Model\CarBooking;
use App\Model\CarType;
use App\Rules\CarAvailableRule;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\CarBooked;


class CarBookingController extends FrontController
{
    public function book(Request $request, $car_type_id)
    {
        //check here if the user is authenticated
        if (!Auth::check()) {
            return Redirect::to("/login");
        }

        $rules = [
            'pickuptime'=>'required',
			'dropofftime'=>'required',
            'arrival_date' => 'required|date|date_format:Y/m/d|after_or_equal:today',
            'departure_date' => 'required|date|date_format:Y/m/d|after_or_equal:'.$request->input('arrival_date'),
        ];

        $car_type = CarType::findOrFail($car_type_id);
        $new_arrival_date = $request->input('arrival_date');
        $new_departure_date = $request->input('departure_date');
		$new_pickuptime = $request->input('pickuptime');
	    $new_dropofftime = $request->input('dropofftime');
        $rules['booking_validation'] = [new CarAvailableRule($car_type, $new_arrival_date, $new_departure_date,$new_pickuptime, $new_dropofftime )];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $car_booking = new CarBooking();
        $user = Auth::user();
		
        $car_booking->pickuptime = $request->input('pickuptime');
		$car_booking->dropofftime = $request->input('dropofftime');
        $car_booking->arrival_date = $request->input('arrival_date');
        $car_booking->departure_date = $request->input('departure_date');
        /**
         * Find total cost by counting number of days and multiplying it with cost of cars.
         */
        $startTime = Carbon::parse($car_booking->arrival_date);
        $finishTime = Carbon::parse($car_booking->departure_date);
        $no_of_days = $finishTime->diffInDays($startTime) ? $finishTime->diffInDays($startTime) : 1;
        $car_booking->car_cost = $no_of_days * $car_type->finalPrice;
        $car_booking->user_id = $user->id;
        /**
         * Select random car for booking of given car type
         */

        $booking = new Booking($car_type, $new_arrival_date, $new_departure_date,$new_pickuptime,$new_dropofftime );
        //dd($booking->available_car_number());
        $car_booking->car_id = $booking->available_car_number();
        $car_booking->user_id = $user->id;
        $car_booking->save();

       

        Session::flash('flash_title', "Success");
        Session::flash('flash_message', "Car has been Booked.");
        return redirect('/dashboard/car/booking');

    }

   
}
