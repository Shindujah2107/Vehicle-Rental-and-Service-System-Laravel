<?php

namespace App\Http\Controllers\Front;

use App\Model\CarType;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class CarTypeController extends FrontController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $car_types = CarType::whereHas('images', function ($query){
            $query->where('is_primary', true);
        })->with([
            'images' => function($query){
            $query->where('is_primary', true)->where('status', true);
        },
            'facilities' => function($query){
                $query->where('status', true);
        }
        ])
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        //dd($car_types);

        return view('front.car_type.index')->with([
            'car_types' => $car_types
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car_type = CarType::with([
            'images' => function($clientQuery) {
                $clientQuery->where('status', true);
        },
            'cars.reviews' => function($clientQuery) {
                $clientQuery->where('approval_status', 'approved');
            }
        ])
            ->where('status', true)
            ->findOrFail($id);


        //dd($car_type->getAggregatedRating());
        return view('front.car_type.profile')
            ->with([
                'car_type' => $car_type,
            ]);
    }
}
