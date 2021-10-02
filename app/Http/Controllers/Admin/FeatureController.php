<?php

namespace App\Http\Controllers\Admin;

use App\Model\Feature;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FeatureController extends AdminController
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
        $features = Feature::all();
        return view('admin.feature.view')->with('features', $features);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.feature.add');
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
            'name' => 'required|max:50|unique:features,name',
            'icon' => 'required|max:15',
            'description' => 'max:200',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
        $feature = new Feature();
        $feature->name = $request->input('name');
        $feature->icon = $request->input('icon');
        $feature->description = $request->input('description');
        $feature->status = $request->input('status');
        $feature->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The feature has been added successfully');
        return redirect('admin/feature');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feature = Feature::find($id);
        return view('admin.feature.edit')->with('feature', $feature);
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
        $feature = Feature::find($id);
        $rules = [
            'name' => 'required|max:50|unique:features,name,'.$id,
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $feature->name = $request->input('name');
        $feature->status = $request->input('status');
        $feature->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The feature has been updated successfully');
        return redirect('admin/feature');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feature = Feature::find($id);
        $feature->delete();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The feature has been deleted successfully');
        return redirect('admin/feature');

    }
}
