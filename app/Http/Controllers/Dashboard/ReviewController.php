<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\CarBooking;

use App\Model\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ReviewController extends DashboardController
{

    public function create($car_booking_id)
    {
        $car_booking = CarBooking::findOrFail($car_booking_id);
        return view('dashboard.booking.review')
            ->with('car_booking', $car_booking);
    }

	
	

    public function store(Request $request, $car_booking_id)
    {
        $car_booking = CarBooking::findOrFail($car_booking_id);
        $rules = [
            'review' => 'max:200',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $review = Review::updateOrCreate(
                ['car_booking_id' => $car_booking_id],
                [
                    'review' => $request->input('review'),
                    'rating' => $request->input('rating')?$request->input('rating'):0,
                    'approval_status' => "pending",
                ]
            );

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Review has been updated.");

            return redirect('/dashboard/car/booking');
        }
    }
	
	
}
