<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\ServiceBooking;
use Illuminate\Support\Facades\Auth;


class ServiceBookingController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service_bookings = ServiceBooking::with('service')
            ->where('user_id', Auth::user()->id)
            ->paginate(10);

        return view('dashboard.booking.service_booking')->with([
            'service_bookings' => $service_bookings
        ]);
    }

    public function cancel($id)
    {
        $service_booking = ServiceBooking::findOrFail($id);

        // If the payment is already made
        if($service_booking->payment == true){
            return back()->withErrors('Sorry, you cannot cancel booking which has been already paid. Please, contact company staff.');
        }
        if($service_booking->status == false){
            return back()->withErrors('Sorry, you cannot cancel booking which is already cancelled. Please, contact company staff.');
        }
        $service_booking->status = false;
        $service_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The service booking has been cancelled successfully');
        return redirect('dashboard/service/booking');
    }

}
