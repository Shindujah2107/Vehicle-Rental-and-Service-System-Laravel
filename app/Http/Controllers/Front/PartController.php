<?php

namespace App\Http\Controllers\Front;

use App\Model\Part;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class PartController extends FrontController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parts = Part::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        return view('front.part.index')->with([
            'parts' => $parts
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
        $part = Part::findOrFail($id)->where('status', 1)->get();
        return view('front.part.profile')
            ->with([
                'part' => $part,
            ]);
    }
}
