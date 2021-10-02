<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Dashboard\DashboardController;
use App\Model\CarBooking;
use App\Model\RepairBooking;
use App\Model\ServiceBooking;
use App\Model\CarType;
use App\Model\RepairType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends DashboardController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $car_bookings = CarBooking::with('car')
            ->where('user_id', Auth::user()->id)
            ->limit(5)
            ->orderBy('created_at', 'asc')
            ->get();
		 $total_car_bookings =  CarBooking::where('user_id', Auth::user()->id)->count();	
		$repair_bookings = RepairBooking::with('repair')
            ->where('user_id', Auth::user()->id)
            ->limit(5)
            ->orderBy('created_at', 'asc')
            ->get();	
			
       
		$total_repair_bookings =  RepairBooking::where('user_id', Auth::user()->id)->count();
        $service_bookings = ServiceBooking::where('user_id', Auth::user()->id)
            ->limit(5)
            ->orderBy('created_at', 'asc')
            ->get();
        $total_service_bookings =  ServiceBooking::where('user_id', Auth::user()->id)->count();

        $total_pending_payments = CarBooking::where('user_id', Auth::user()->id)->where('payment', 0)->count()
                                + RepairBooking::where('user_id', Auth::user()->id)->where('payment', 0)->count();

								
								

        $car_booking_with_reviews =  CarBooking::whereHas('review', function ($query) {
            $query->where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->limit('5');
        })->get();
		
		 $repair_booking_with_reviewsrepairs =  RepairBooking::whereHas('review', function ($query) {
            $query->where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->limit('5');
        })->get();
        return view('dashboard.home')->with([
            'car_bookings' => $car_bookings,
			'repair_bookings' => $repair_bookings,
            'total_car_bookings' => $total_car_bookings,
			'total_repair_bookings' => $total_repair_bookings,
            'service_bookings' => $service_bookings,
            'total_service_bookings' => $total_service_bookings,
            'total_pending_payments' => $total_pending_payments,
            'car_booking_with_reviews' => $car_booking_with_reviews,
			'repair_booking_with_reviewsrepairs' => $repair_booking_with_reviewsrepairs,
        ]);
    }
}
