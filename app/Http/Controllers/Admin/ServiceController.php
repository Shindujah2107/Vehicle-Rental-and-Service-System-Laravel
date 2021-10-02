<?php

namespace App\Http\Controllers\Admin;

use App\Model\Service;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as ImageManager;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ServiceController extends AdminController
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
        $services = Service::all();
        return view('admin.service.view')->with('services', $services);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.service.add');
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
            'name' => 'required|max:50|unique:services,name',
            'image' => 'required|mimes:jpeg, jpg, png',
            'date' => 'required|date|date_format:Y/m/d|after_or_equal:today',
            'venue' => 'required|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'max:200',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
        $service = new Service();
        $service->name = $request->input('name');
        $service->date = $request->input('date');
        $service->venue = $request->input('venue');
        $service->price = $request->input('price');
        $service->description = $request->input('description');
        $service->capacity = $request->input('capacity');
        $service->status = $request->input('status');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('', 'service');
            $service_image = ImageManager::make('storage/services/' . $path);
            $service_image->fit(500, 450);
            $service_image->save(storage_path() . '/app/public/services/' . $path);
            $service->image = $path;
        }

        $service->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The service has been added successfully');
        return redirect('admin/service');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::find($id);
        return view('admin.service.edit')->with('service', $service);
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
        $service = Service::find($id);

        $rules = [
            'name' => 'required|max:50|unique:services,name,'.$id,
            'date' => 'required|date|date_format:Y/m/d|after_or_equal:'.$service->date,
            'venue' => 'required|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'max:200',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        $service->name = $request->input('name');
        $service->date = $request->input('date');
        $service->venue = $request->input('venue');
        $service->price = $request->input('price');
        $service->description = $request->input('description');
        $service->capacity = $request->input('capacity');
        $service->status = $request->input('status');

        if ($request->hasFile('image')) {
            Storage::delete('public/services/'.$service->image);

            $path = $request->file('image')->store('', 'service');
            $service_image = ImageManager::make('storage/services/' . $path);
            $service_image->fit(500, 450);
            $service_image->save(storage_path() . '/app/public/services/' . $path);
            $service->image = $path;
        }

        $service->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The service has been updated successfully');
        return redirect('admin/service');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::find($id);

        // Delete service bookings
        foreach ($service->service_bookings as $booking) {
            $booking->delete();
        }

        if($service->delete()){
            Storage::delete('public/services/'.$service->image);

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Image has been deleted');
        }

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The service has been deleted successfully');
        return redirect('admin/service');

    }
}
