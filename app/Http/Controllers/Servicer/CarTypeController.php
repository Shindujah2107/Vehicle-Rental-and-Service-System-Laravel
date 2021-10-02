<?php

namespace App\Http\Controllers\Servicer;

use App\Model\Facility;
use App\Model\CarType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CarTypeController extends ServicerController
{

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
        $car_types = CarType::with('facilities:name')->get();
        return view('servicer.car_type.view')->with('car_types', $car_types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $facilities = Facility::all()->where('status', true);
        return view('servicer.car_type.add')
            ->with('facilities', $facilities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:50|unique:car_types,name',
            'cost_per_day' => 'required|numeric|min:0',
            'model' => 'numeric|min:0',
            'discount_percentage' => 'integer|between:0,100',
            'engine' => 'integer|min:1',
            'fueltype' => 'required|max:10',
            'description' => 'max:800',
            'facility' => 'array',
            'car_service' => 'boolean',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $car_type = new CarType();
        $car_type->name = $request->input('name');
        $car_type->cost_per_day = $request->input('cost_per_day');
        $car_type->model = $request->input('model');
        $car_type->discount_percentage = $request->input('discount_percentage');
        $car_type->engine = $request->input('engine');
        $car_type->fueltype = $request->input('fueltype');
        $car_type->description = $request->input('description');
        $car_type->car_service = $request->input('car_service');
        $car_type->status = $request->input('status');
        $car_type->save();

        if($request->has('facility')){
            $car_type->facilities()->attach(array_keys($request->input('facility')));
        }

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The car_type has been added successfully');
        return redirect('servicer/car_type');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $facilities = Facility::all()->where('status', true);
        $car_type = CarType::find($id);
        return view('servicer.car_type.edit')->with([
            'car_type' => $car_type,
            'facilities' => $facilities
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $car_type = CarType::find($id);
        $rules = [
            'name' => 'required|max:50|unique:car_types,name,'.$id,
            'cost_per_day' => 'required|numeric|min:0',
            'model' => 'numeric|min:0',
            'discount_percentage' => 'integer|between:0,100',
            'engine' => 'numeric',
            'fueltype' => 'required|max:10',
            'description' => 'max:800',
            'facility' => 'array',
            'car_service' => 'boolean',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $car_type->name = $request->input('name');
        $car_type->cost_per_day = $request->input('cost_per_day');
        $car_type->model = $request->input('model');
        $car_type->discount_percentage = $request->input('discount_percentage');
        $car_type->engine = $request->input('engine');
        $car_type->fueltype = $request->input('fueltype');
        $car_type->description = $request->input('description');
        $car_type->car_service = $request->input('car_service');
        $car_type->status = $request->input('status');
        $car_type->save();

        $car_type->facilities()->sync(array_keys($request->input('facility')));

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The car_type has been updated successfully');
        return redirect('servicer/car_type');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $car_type = CarType::find($id);

        // Delete cars
        foreach ($car_type->car as $car) {
            // Delete car bookings
            foreach ($car->car_bookings as $booking) {
                $booking->delete();
            }
            $car->delete();
        }

        // Delete images
        foreach ($car_type->images as $image) {
            if ($image->delete()) {
                if (Storage::disk('car_type')->exists($image->name)) {
                    Storage::delete('public/car_types/' . $image->name);
                }
            }
        }
        // TO_DO_DEM Clear all Facilities by Eloquent remove pivot records

        $car_type->delete();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The car_type has been deleted successfully');
        return redirect('servicer/car_type');

    }
}
