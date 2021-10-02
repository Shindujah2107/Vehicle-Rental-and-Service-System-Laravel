<?php

namespace App\Http\Controllers\Servicer;

use App\Model\CarBooking;

use App\Http\Controllers\Servicer\ServicerController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CarBookingController extends ServicerController
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
        $car_bookings = CarBooking::all();
        return view('servicer.car_booking.view')
            ->with('car_bookings', $car_bookings);
    }

    public function edit($id)
    {
        $car_booking = CarBooking::find($id);
        return view('servicer.car_booking.edit')->with('car_booking', $car_booking);
    }

    public function update(Request $request, $id)
    {
        $car_booking = CarBooking::findOrFail($id);

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

        $car_booking->status = $request->input('status');
        $car_booking->payment = $request->input('payment');
        $car_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The Car Booking has been updated successfully.');
        return redirect('/servicer/car_booking');
    }

}
