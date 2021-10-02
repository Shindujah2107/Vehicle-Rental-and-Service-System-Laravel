<?php

namespace App\Http\Controllers\Admin;

use App\Model\ServiceBooking;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ServiceBookingController extends AdminController
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
        $service_bookings = ServiceBooking::all();
        return view('admin.service_booking.view')
            ->with('service_bookings', $service_bookings);
    }

    public function edit($id)
    {
        $service_booking = ServiceBooking::find($id);
        return view('admin.service_booking.edit')->with('service_booking', $service_booking);
    }

    public function update(Request $request, $id)
    {
        $service_booking = ServiceBooking::findOrFail($id);

        $rules = [
            'status' => 'boolean',
            'payment' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $service_booking->status = $request->input('status');
        $service_booking->payment = $request->input('payment');
        $service_booking->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The Service Booking has been updated successfully.');
        return redirect('/admin/service_booking');

    }

}
