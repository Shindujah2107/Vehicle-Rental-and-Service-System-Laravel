<?php

namespace App\Http\Controllers\Front;

use App\Model\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ServiceController extends FrontController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        return view('front.service.index')->with([
            'services' => $services
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
        $service = Service::findOrFail($id)->where('status', 1)->get();
        return view('front.service.profile')
            ->with([
                'service' => $service,
            ]);
    }
}
