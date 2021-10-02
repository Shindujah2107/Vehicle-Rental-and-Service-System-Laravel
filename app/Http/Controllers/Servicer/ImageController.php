<?php

namespace App\Http\Controllers\Servicer;

use App\Model\CarType;
use App\Model\Image;

use App\Http\Controllers\Servicer\ServicerController;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ImageController extends ServicerController
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
        return view('servicer.image.view')
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
        return view('servicer.image.add')
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
            'caption' => 'max:30',
            'image' => 'required|mimes:jpeg, jpg, png',
            'is_primary' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $car_type = CarType::find($id);
            $image = new Image();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('', 'car_type');
                $car_type_image = ImageManager::make('storage/car_types/' . $path);
                $car_type_image->fit(950, 400);
                $car_type_image->save(storage_path() . '/app/public/car_types/' . $path);
                $image->name = $path;
            }

            $image->caption = $request->input('caption');
            $image->is_primary = $request->input('is_primary');
            if($image->is_primary == true){
                $this->set_is_primary_false($id);
            }

            $image->status = $request->input('status');
            $image->car_type_id = $id;
            $image->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Photo has been added to the car gallery. Add more images.");

            return redirect('/servicer/car_type/'.$id.'/image');
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
    public function edit($id, $image_id)
    {
        $car_type = CarType::find($id);
        $image = Image::find($image_id);
        return view('servicer.image.edit')
            ->with('car_type', $car_type)
            ->with('image', $image);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $image_id, Request $request)
    {
        $rules = [
            'caption' => 'max:30',
            'is_primary' => 'required',
            'status' => 'required'
        ];
        if ($request->hasFile('image')) {
            $rules['image'] = 'mimes:jpeg,jpg,png';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {

            $car_type = CarType::find($id);
            $image = Image::find($image_id);
            if ($request->hasFile('image')) {
                Storage::delete('public/car_types/'.$image->name);

                $path = $request->file('image')->store('', 'car_type');
                $car_type_image = ImageManager::make('storage/car_types/' . $path);
                $car_type_image->fit(950, 400);
                $car_type_image->save(storage_path() . '/app/public/car_types/' . $path);
                $image->name = $path;
            }
            $image->caption = $request->input('caption');
            $image->is_primary = $request->input('is_primary');
            if($image->is_primary == true){
                $this->set_is_primary_false($id);
            }
            $image->status = $request->input('status');
            $image->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Photo has been updated.");

            return redirect('/servicer/car_type/'.$id.'/image');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $image_id)
    {
        $car_type = CarType::find($id);
        $image = Image::findOrFail($image_id);
        if($image->delete()){
            Storage::delete('public/car_types/'.$image->name);

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Image has been deleted');

            return redirect('/servicer/car_type/'.$id.'/image');
        }
        return redirect()
            ->back()
            ->withErrors(array('message' => 'Sorry, the image could not be deleted.'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_multiple($id)
    {
        $car_type = CarType::find($id);
        return view('servicer.image.add_multiple')
            ->with('car_type', $car_type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_multiple($id, Request $request)
    {
        $car_type = CarType::find($id);
        $rules = [
            'images' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {

            // Upload Photo
            if ($request->hasFile('images')) {
                $images = $request->file('images');

                foreach ($images as $file) {

                    $path = $file->store('','car_type');
                    $image = ImageManager::make('storage/car_types/'.$path);
                    $image->fit(950, 650);
                    $image->save(storage_path().'/app/public/car_types/'.$path);

                    Image::create([
                        'name' => $path,
                        'status' => $request->input('status'),
                        'car_type_id' => $car_type->id
                    ]);
                }
            }

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "All images has been added to the car_type.");

            return redirect('/dashboard/car_types/'.$id.'/image');
        }
    }

    public function set_is_primary_false($car_type_id)
    {
        $car_type = CarType::find($car_type_id);
        foreach ($car_type->images as $image){
            $image->is_primary = false;
            $image->save();
        }
    }

}
