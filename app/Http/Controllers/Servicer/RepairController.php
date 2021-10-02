<?php

namespace App\Http\Controllers\Servicer;

use App\Model\RepairType;
use App\Model\Repair;

use App\Http\Controllers\Servicer\ServicerController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RepairController extends ServicerController
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
        $repair_type = RepairType::find($id);
        return view('Servicer.repair.view')
            ->with('repair_type', $repair_type);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $repair_type = RepairType::find($id);
        return view('Servicer.repair.add')
            ->with('repair_type', $repair_type);
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
            'repair_number' => 'required|unique:repairs,repair_number',
            'repair_num' => 'required|unique:repairs,repair_num',
			 
            'description' => 'max:200',
            'status' => 'boolean|required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $repair_type = RepairType::find($id);
            $repair = new Repair();
            $repair->repair_number = $request->input('repair_number');
            $repair->repair_num = $request->input('repair_num');
			
            $repair->description = $request->input('description');
            $repair->status = $request->input('status');

            $repair->repair_type()->associate($repair_type);
            $repair->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Repair has been added. Add more repairs.");

            return redirect('/Servicer/repair_type/'.$id.'/repair');
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
    public function edit($id, $repair_id)
    {
        $repair_type = RepairType::find($id);
        $repair = Repair::find($repair_id);
        return view('Servicer.repair.edit')
            ->with('repair_type', $repair_type)
            ->with('repair', $repair);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $repair_id, Request $request)
    {
        $rules = [
            'repair_number' => 'required|unique:repairs,repair_number,'.$repair_id,
            'repair_num' => 'required|unique:repairs,repair_num,'.$repair_id,
		
            'description' => 'max:200',
            'status' => 'boolean|required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $repair = Repair::find($repair_id);
            $repair->repair_number = $request->input('repair_number');
            $repair->repair_num = $request->input('repair_num');
		
            $repair->description = $request->input('description');
            if($request->has('available')){
                $repair->available = $request->input('available');
            }
            $repair->status = $request->input('status');
            $repair->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Repair has been updated.");

            return redirect('/Servicer/repair_type/'.$id.'/repair');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $repair_id)
    {
        $repair = Repair::findOrFail($repair_id);

        // Delete repair bookings
        foreach ($repair->repair_bookings as $booking) {
            $booking->delete();
        }

        if($repair->delete()){

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Repair has been deleted');

            return redirect('/Servicer/repair_type/'.$id.'/repair');
        }
        return redirect()
            ->back()
            ->withErrors(array('message' => 'Sorry, the repair could not be deleted.'));

    }

}
