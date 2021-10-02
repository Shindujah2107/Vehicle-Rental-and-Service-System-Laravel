<?php

namespace App\Http\Controllers\Admin;

use App\Model\Part;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as ImageManager;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PartController extends AdminController
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
        $parts = Part::all();
        return view('admin.part.view')->with('parts', $parts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.part.add');
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
            'name' => 'required|max:100|unique:parts,name',
            'type' => 'required|in:Air filters and Intake,Auto Body Parts,Grille Guards,Headlights and Lighting,Transmission',
            'image' => 'required|mimes:jpeg, jpg, png',
            'price' => 'required|numeric|min:0',
            'description' => 'max:100',
            'status' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
        $part = new Part();
        $part->name = $request->input('name');
        $part->type = $request->input('type');
        $part->price = $request->input('price');
        $part->description = $request->input('description');
        $part->status = $request->input('status');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('', 'part');
            $part_image = ImageManager::make('storage/parts/' . $path);
            $part_image->fit(70, 70);
            $part_image->save(storage_path() . '/app/public/parts/' . $path);
            $part->image = $path;
        }

        $part->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The part has been added successfully');
        return redirect('admin/part');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $part = Part::find($id);
        return view('admin.part.edit')->with('part', $part);
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
        $part = Part::find($id);
        $rules = [
            'name' => 'required|max:100|unique:parts,name,'.$id,
            'type' => 'required|in:Air filters and Intake,Auto Body Parts,Grille Guards,Headlights and Lighting,Transmission',
            'price' => 'required|numeric|min:0',
            'description' => 'max:100',
            'status' => 'required|boolean'
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

        $part->name = $request->input('name');
        $part->type = $request->input('type');
        $part->price = $request->input('price');
        $part->description = $request->input('description');
        $part->status = $request->input('status');

        if ($request->hasFile('image')) {
            Storage::delete('public/parts/'.$part->image);

            $path = $request->file('image')->store('', 'part');
            $part_image = ImageManager::make('storage/parts/' . $path);
            $part_image->fit(70, 70);
            $part_image->save(storage_path() . '/app/public/parts/' . $path);
            $part->image = $path;
        }

        $part->save();

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The part has been updated successfully');
        return redirect('admin/part');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $part = Part::find($id);
        if($part->delete()){
            Storage::delete('public/parts/'.$part->image);

            Session::flash('flash_title', 'Success');
            Session::flash('flash_message', 'Image has been deleted');
        }

        Session::flash('flash_title', 'Success');
        Session::flash('flash_message', 'The part has been deleted successfully');
        return redirect('admin/part');

    }
}
