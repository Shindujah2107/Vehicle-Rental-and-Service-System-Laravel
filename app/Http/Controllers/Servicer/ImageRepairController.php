<?php

namespace App\Http\Controllers\Servicer;

use App\Model\RepairType;
use App\Model\ImageRepair;

use App\Http\Controllers\Servicer\ServicerController;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ImageRepairController extends ServicerController
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
        return view('servicer.imagerepair.view')
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
        return view('servicer.imagerepair.add')
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
            'caption' => 'max:30',
            'imagerepair' => 'required|mimes:jpeg, jpg, png',
            'is_primary' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {
            $repair_type = RepairType::find($id);
            $imagerepair = new ImageRepair();
            if ($request->hasFile('imagerepair')) {
                $path = $request->file('imagerepair')->store('', 'repair_type');
                $repair_type_imagerepair = ImageManager::make('storage/repair_types/' . $path);
                $repair_type_imagerepair->fit(950, 400);
                $repair_type_imagerepair->save(storage_path() . '/app/public/repair_types/' . $path);
                $imagerepair->name = $path;
            }

            $imagerepair->caption = $request->input('caption');
            $imagerepair->is_primary = $request->input('is_primary');
            if($imagerepair->is_primary == true){
                $this->set_is_primary_false($id);
            }

            $imagerepair->status = $request->input('status');
            $imagerepair->repair_type_id = $id;
            $imagerepair->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Photo has been added to the repair gallery. Add more images.");

            return redirect('/servicer/repair_type/'.$id.'/imagerepair');
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
    public function edit($id, $imagerepair_id)
    {
        $repair_type = RepairType::find($id);
        $imagerepair = ImageRepair::find($imagerepair_id);
        return view('servicer.imagerepair.edit')
            ->with('repair_type', $repair_type)
            ->with('imagerepair', $imagerepair);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $imagerepair_id, Request $request)
    {
        $rules = [
            'caption' => 'max:30',
            'is_primary' => 'required',
            'status' => 'required'
        ];
        if ($request->hasFile('imagerepair')) {
            $rules['imagerepair'] = 'mimes:jpeg,jpg,png';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {

            $repair_type = RepairType::find($id);
            $imagerepair = ImageRepair::find($imagerepair_id);
            if ($request->hasFile('imagerepair')) {
                Storage::delete('public/repair_types/'.$imagerepair->name);

                $path = $request->file('imagerepair')->store('', 'repair_type');
                $repair_type_imagerepair = ImageManager::make('storage/repair_types/' . $path);
                $repair_type_imagerepair->fit(950, 400);
                $repair_type_imagerepair->save(storage_path() . '/app/public/repair_types/' . $path);
                $imagerepair->name = $path;
            }
            $imagerepair->caption = $request->input('caption');
            $imagerepair->is_primary = $request->input('is_primary');
            if($imagerepair->is_primary == true){
                $this->set_is_primary_false($id);
            }
            $imagerepair->status = $request->input('status');
            $imagerepair->save();

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "Photo has been updated.");

            return redirect('/servicer/repair_type/'.$id.'/imagerepair');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $imagerepair_id)
    {
        $repair_type = RepairType::find($id);
        $imagerepair = ImageRepair::findOrFail($imagerepair_id);
        if($imagerepair->delete()){
            Storage::delete('public/repair_types/'.$imagerepair->name);

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Image has been deleted');

            return redirect('/servicer/repair_type/'.$id.'/imagerepair');
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
        $repair_type = RepairType::find($id);
        return view('servicer.imagerepair.add_multiple')
            ->with('repair_type', $repair_type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_multiple($id, Request $request)
    {
        $repair_type = RepairType::find($id);
        $rules = [
            'imagesrepairs' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all)
                ->withErrors($validator);
        } else {

            // Upload Photo
            if ($request->hasFile('imagesrepairs')) {
                $imagesrepairs = $request->file('imagesrepairs');

                foreach ($imagesrepairs as $file) {

                    $path = $file->store('','repair_type');
                    $imagerepair = ImageManager::make('storage/repair_types/'.$path);
                    $imagerepair->fit(950, 650);
                    $imagerepair->save(storage_path().'/app/public/repair_types/'.$path);

                    ImageRepair::create([
                        'name' => $path,
                        'status' => $request->input('status'),
                        'repair_type_id' => $repair_type->id
                    ]);
                }
            }

            Session::flash('flash_title', "Success");
            Session::flash('flash_message', "All images has been added to the repair_type.");

            return redirect('/dashboard/repair_types/'.$id.'/imagerepair');
        }
    }

    public function set_is_primary_false($repair_type_id)
    {
        $repair_type = RepairType::find($repair_type_id);
        foreach ($repair_type->imagesrepairs as $imagerepair){
            $imagerepair->is_primary = false;
            $imagerepair->save();
        }
    }

}
