<?php

namespace App\Http\Controllers\Front;


use App\Model\Service;
use App\Model\ServiceBooking;
use App\Rules\ServiceCapacityRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceBooked;

class ServiceBookingController extends FrontController
{
    public function book(Request $request, $service_id)
    {


        //check here if the user is authenticated
        if (!Auth::check()) {
            return Redirect::to("/login");
        }

        $service = Service::findOrFail($service_id);
        // compare capacity with total vehicles sold
        $total_booked_vehicles = 0;
        foreach($service->service_bookings as $service_booking){
            $total_booked_vehicles += $service_booking->number_of_vehicles;
        }

        // Total available vehicles; vehicle capacity - total booked vehicles
        $available_vehicles = $service->capacity - $total_booked_vehicles;
        $rules = [
            'number_of_vehicles' => ["required","numeric", "min:1", new ServiceCapacityRule($available_vehicles)],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $service_booking = new ServiceBooking();
        $user = Auth::user();

        $service_booking->number_of_vehicles = $request->input('number_of_vehicles');
        /**
         * Find total cost by counting number of vehicles and multiplying it with service price.
         */
        $service_booking->total_cost = $service_booking->number_of_vehicles * $service->price;
        $service_booking->user_id = $user->id;
        $service_booking->service_id = $service->id;
        $service_booking->save();

        $this->send_email(Auth::user()->email);

        Session::flash('flash_title', "Success");
        Session::flash('flash_message', "Service has been Booked.");
        return redirect('/dashboard/service/booking');

    }

    private function send_email($email){
        if(empty($email)){
            $email = Auth::user()->email;
        }
        Mail::to($email)->send(new ServiceBooked());
    }
}
