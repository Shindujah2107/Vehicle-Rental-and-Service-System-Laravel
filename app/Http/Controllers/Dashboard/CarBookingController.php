<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\CarBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class CarBookingController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $car_bookings = CarBooking::with('car')
            ->where('user_id', Auth::user()->id)
            ->paginate(10);

        return view('dashboard.booking.car_booking')->with([
            'car_bookings' => $car_bookings
        ]);
    }

    public function cancel($id)
    {
        $car_booking = CarBooking::findOrFail($id);


        // If the payment is already made
        if($car_booking->payment == true){
            return back()->withErrors('Sorry, you cannot cancel booking which has been already paid. Please, contact our staff.');
        }

        // If the user is already checked_in
        if($car_booking->status == "checked_in"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already checked in without staff permission. Please, contact our staff.');
        }
        if($car_booking->status == "checked_out"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already checked out without staff permission. Please, contact our staff.');
        }
        if($car_booking->status == "cancelled"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already cancelled. Please, contact our staff.');
        }

        $car_booking->status = "cancelled";
        $car_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The car booking has been cancelled successfully.');
        return redirect('dashboard/car/booking');
    }

}
