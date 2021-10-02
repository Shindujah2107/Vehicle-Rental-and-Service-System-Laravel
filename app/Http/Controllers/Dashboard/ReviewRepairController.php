<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\RepairBooking;

use App\Model\ReviewRepair;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ReviewRepairController extends DashboardController
{

    public function create($repair_booking_id)
    {
        $repair_booking = RepairBooking::findOrFail($repair_booking_id);
        return view('dashboard.booking.reviewrepair')
            ->with('repair_booking', $repair_booking);
    }

	
	

    public function store(Request $request, $repair_booking_id)
    {
        $repair_booking = RepairBooking::findOrFail($repair_booking_id);
        $rules = [
            'reviewrepair' => 'max:200',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $reviewrepair = ReviewRepair::updateOrCreate(
                ['repair_booking_id' => $repair_booking_id],
                [
                    'reviewrepair' => $request->input('reviewrepair'),
                    'rating' => $request->input('rating')?$request->input('rating'):0,
                    'approval_status' => "pending",
                ]
            );

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Review has been updated.");

            return redirect('/dashboard/repair/booking');
        }
    }
	
	
}
