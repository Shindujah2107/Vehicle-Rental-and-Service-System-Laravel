<?php

namespace App\Http\Controllers\Servicer;

use App\Http\Controllers\Servicer\ServicerController;
use Illuminate\Http\Request;

class HomeController extends ServicerController
{
    //
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('servicer.home');
    }

    public function login()
{
    return view('servicer.login');
}
}
