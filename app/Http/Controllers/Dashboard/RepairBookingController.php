<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\RepairBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class RepairBookingController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $repair_bookings = RepairBooking::with('repair')
            ->where('user_id', Auth::user()->id)
            ->paginate(10);

        return view('dashboard.booking.repair_booking')->with([
            'repair_bookings' => $repair_bookings
        ]);
    }

    public function cancel($id)
    {
        $repair_booking = RepairBooking::findOrFail($id);


        // If the payment is already made
        if($repair_booking->payment == true){
            return back()->withErrors('Sorry, you cannot cancel booking which has been already paid. Please, contact our staff.');
        }

        // If the user is already checked_in
        if($repair_booking->status == "checked_in"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already checked in without staff permission. Please, contact our staff.');
        }
        if($repair_booking->status == "checked_out"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already checked out without staff permission. Please, contact our staff.');
        }
        if($repair_booking->status == "cancelled"){
            return back()->withErrors('Sorry, you cannot cancel booking which is already cancelled. Please, contact our staff.');
        }

        $repair_booking->status = "cancelled";
        $repair_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The repair booking has been cancelled successfully.');
        return redirect('dashboard/repair/booking');
    }

}
