<?php

namespace App\Http\Controllers\Front;

use App\Algo\BookingRepair;
use App\Model\RepairBooking;
use App\Model\RepairType;
use App\Rules\RepairAvailableRule;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\RepairBooked;


class RepairBookingController extends FrontController
{
    public function book(Request $request, $repair_type_id)
    {
        //check here if the user is authenticated
        if (!Auth::check()) {
            return Redirect::to("/login");
        }

        $rules = [
            'booktime'=>'required',
			'arrival_date' => 'required|date|date_format:Y/m/d|after_or_equal:today',
            'departure_date' => 'required|date|date_format:Y/m/d|after_or_equal:'.$request->input('arrival_date'),
			 'vehicletype' => 'required|in:Rs.5000,Rs.6000,Rs.7000,Rs.8000',
        ];

        $repair_type = RepairType::findOrFail($repair_type_id);
	
        $new_arrival_date = $request->input('arrival_date');
        $new_departure_date = $request->input('departure_date');
		$vehicle_type=$request->input('vehicletype');
		$new_booktime = $request->input('booktime');
		
        $rules['booking_validation'] = [new RepairAvailableRule($repair_type,$new_arrival_date, $new_departure_date,$vehicle_type,$new_booktime )];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $repair_booking = new RepairBooking();
        $user = Auth::user();
		
        $repair_booking->booktime = $request->input('booktime');
        $repair_booking->arrival_date = $request->input('arrival_date');
        $repair_booking->departure_date = $request->input('departure_date');
		   $repair_booking->vehicletype = $request->input('vehicletype');
        /**
         * Find total cost by counting number of days and multiplying it with cost of repairs.
         */
        $startTime = Carbon::parse($repair_booking->arrival_date);
        $finishTime = Carbon::parse($repair_booking->departure_date);
        $no_of_days = $finishTime->diffInDays($startTime) ? $finishTime->diffInDays($startTime) : 1;
        $repair_booking->repair_cost = $no_of_days * $repair_type ->finalPrice;
        $repair_booking->user_id = $user->id;
        /**
         * Select random repair for booking of given repair type
         */

        $booking = new BookingRepair($repair_type,$new_arrival_date, $new_departure_date,$vehicle_type,$new_booktime);
        //dd($booking->available_repair_number());
        $repair_booking->repair_id = $booking->available_repair_number();
        $repair_booking->user_id = $user->id;
        $repair_booking->save();

       

        Session::flash('flash_title', "Success");
        Session::flash('flash_message', "Repair has been Booked.");
        return redirect('/dashboard/repair/booking');

    }

   
}
