<?php

namespace App\Http\Controllers\Servicer;

use App\Model\CarType;
use App\Model\Car;

use App\Http\Controllers\Servicer\ServicerController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CarController extends ServicerController
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
    public function index($id)
    {
        $car_type = CarType::find($id);
        return view('servicer.car.view')
            ->with('car_type', $car_type);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $car_type = CarType::find($id);
        return view('servicer.car.add')
            ->with('car_type', $car_type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $rules = [
            'car_number' => 'required|numeric|max:99999|unique:cars,car_number',
            'description' => 'max:200',
            'status' => 'boolean|required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $car_type = CarType::find($id);
            $car = new Car();
            $car->car_number = $request->input('car_number');
            $car->description = $request->input('description');
            $car->status = $request->input('status');

            $car->car_type()->associate($car_type);
            $car->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Car has been added. Add more cars.");

            return redirect('/servicer/car_type/'.$id.'/car');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $car_id)
    {
        $car_type = CarType::find($id);
        $car = Car::find($car_id);
        return view('servicer.car.edit')
            ->with('car_type', $car_type)
            ->with('car', $car);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $car_id, Request $request)
    {
        $rules = [
            'car_number' => 'required|numeric|max:99999|unique:cars,car_number,'.$car_id,
            'description' => 'max:200',
            'status' => 'boolean|required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $car = Car::find($car_id);
            $car->car_number = $request->input('car_number');
            $car->description = $request->input('description');
            if($request->has('available')){
                $car->available = $request->input('available');
            }
            $car->status = $request->input('status');
            $car->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Car has been updated.");

            return redirect('/servicer/car_type/'.$id.'/car');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $car_id)
    {
        $car = Car::findOrFail($car_id);

        // Delete car bookings
        foreach ($car->car_bookings as $booking) {
            $booking->delete();
        }

        if($car->delete()){

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Car has been deleted');

            return redirect('/servicer/car_type/'.$id.'/car');
        }
        return redirect()
            ->back()
            ->withErrors(array('message' => 'Sorry, the car could not be deleted.'));

    }

}
