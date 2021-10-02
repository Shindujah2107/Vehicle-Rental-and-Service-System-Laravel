<?php

namespace App\Http\Controllers\Admin;

use App\Model\Feature;
use App\Model\RepairType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RepairTypeController extends AdminController
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
        $repair_types = RepairType::with('features:name')->get();
        return view('admin.repair_type.view')->with('repair_types', $repair_types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $features = Feature::all()->where('status', true);
        return view('admin.repair_type.add')
            ->with('features', $features);
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
            'name' => 'required|max:50|unique:repair_types,name',
            'cost_per_day' => 'required|numeric|min:0',
            'model' => 'numeric|min:0',
            'discount_percentage' => 'integer|between:0,100',
            'no_of_seats' => 'integer|min:1',
           'fueltype' => 'required|in:petrol,diesel,CNG',
            'description' => 'max:800',
            'feature' => 'array',
            'repair_service' => 'boolean',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $repair_type = new RepairType();
        $repair_type->name = $request->input('name');
        $repair_type->cost_per_day = $request->input('cost_per_day');
        $repair_type->model = $request->input('model');
        $repair_type->discount_percentage = $request->input('discount_percentage');
        $repair_type->no_of_seats = $request->input('no_of_seats');
        $repair_type->fueltype = $request->input('fueltype');
        $repair_type->description = $request->input('description');
        $repair_type->repair_service = $request->input('repair_service');
        $repair_type->status = $request->input('status');
        $repair_type->save();

        if($request->has('feature')){
            $repair_type->facilities()->attach(array_keys($request->input('feature')));
        }

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The repair_type has been added successfully');
        return redirect('admin/repair_type');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $features = Feature::all()->where('status', true);
        $repair_type = RepairType::find($id);
        return view('admin.repair_type.edit')->with([
            'repair_type' => $repair_type,
            'features' => $features
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
        $repair_type = RepairType::find($id);
        $rules = [
            'name' => 'required|max:50|unique:repair_types,name,'.$id,
            'cost_per_day' => 'required|numeric|min:0',
            'model' => 'numeric|min:0',
            'discount_percentage' => 'integer|between:0,100',
            'no_of_seats' => 'numeric',
            'fueltype' => 'required|in:petrol,diesel,CNG',
            'description' => 'max:800',
            'feature' => 'array',
            'repair_service' => 'boolean',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $repair_type->name = $request->input('name');
        $repair_type->cost_per_day = $request->input('cost_per_day');
        $repair_type->model = $request->input('model');
        $repair_type->discount_percentage = $request->input('discount_percentage');
        $repair_type->no_of_seats = $request->input('no_of_seats');
        $repair_type->fueltype = $request->input('fueltype');
        $repair_type->description = $request->input('description');
        $repair_type->repair_service = $request->input('repair_service');
        $repair_type->status = $request->input('status');
        $repair_type->save();

        $repair_type->features()->sync(array_keys($request->input('feature')));

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The repair_type has been updated successfully');
        return redirect('admin/repair_type');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $repair_type = RepairType::find($id);

        // Delete repairs
        foreach ($repair_type->repairs as $repair) {
            // Delete repair bookings
            foreach ($repair->repair_bookings as $booking) {
                $booking->delete();
            }
            $repair->delete();
        }

        // Delete images
        foreach ($repair_type->imagesrepairs as $imagerepair) {
            if ($imagerepair->delete()) {
                if (Storage::disk('repair_type')->exists($imagerepair->name)) {
                    Storage::delete('public/repair_types/' . $imagerepair->name);
                }
            }
        }
        // TO_DO_DEM Clear all Facilities by Eloquent remove pivot records

        $repair_type->delete();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The repair_type has been deleted successfully');
        return redirect('admin/repair_type');

    }
}
