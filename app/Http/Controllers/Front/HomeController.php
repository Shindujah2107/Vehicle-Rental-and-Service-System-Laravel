<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\FrontController;
use App\Model\Service;
use App\Model\Part;
use App\Model\Review;
use Illuminate\Http\Request;
use App\Model\Slider;
use App\Model\CarType;
use App\Model\RepairType;

class HomeController extends FrontController
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
        $slider_images = Slider::where('status', 1)->get();
        $car_types = CarType::whereHas('images', function ($query){
           $query->where('is_primary', true);
        })->with([
            'images' => function($query){
            $query->where('is_primary', true)->where('status', true);
        },
            'cars' => function($query){
                $query->where('status', true);
            }])
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();
			
			$repair_types = RepairType::whereHas('imagesrepairs', function ($query){
           $query->where('is_primary', true);
        })->with([
            'imagesrepairs' => function($query){
            $query->where('is_primary', true)->where('status', true);
        },
            'repairs' => function($query){
                $query->where('status', true);
            }])
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $services = Service::where('status', 1)
            ->orderBy('date', 'desc')
            ->limit('4')
            ->get();

        $parts = Part::where('status', 1)->get();

        $reviews = Review::where('approval_status', "approved")
            ->orderBy('updated_at', 'desc')
            ->limit('4')
            ->get();

        return view('front.home')->with([
            'slider_images' => $slider_images,
            'car_types' => $car_types,
			   'repair_types' => $repair_types,
            'services' => $services,
            'parts' => $parts,
            'reviews' => $reviews,
        ]);
    }
}
