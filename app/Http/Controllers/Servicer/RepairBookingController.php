<?php

namespace App\Http\Controllers\Servicer;

use App\Model\RepairBooking;

use App\Http\Controllers\Servicer\ServicerController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RepairBookingController extends ServicerController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $repair_bookings = RepairBooking::all();
        return view('Servicer.repair_booking.view')
            ->with('repair_bookings', $repair_bookings);
    }

    public function edit($id)
    {
        $repair_booking = RepairBooking::find($id);
        return view('Servicer.repair_booking.edit')->with('repair_booking', $repair_booking);
    }

    public function update(Request $request, $id)
    {
        $repair_booking = RepairBooking::findOrFail($id);

        $rules = [
            'status' => 'in:pending,checked_in,checked_out,cancelled',
            'payment' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $repair_booking->status = $request->input('status');
        $repair_booking->payment = $request->input('payment');
        $repair_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The Repair Booking has been updated successfully.');
        return redirect('/Servicer/repair_booking');
    }

}
