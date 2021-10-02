<?php

namespace App\Http\Controllers\Front;

use App\Model\RepairType;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class RepairTypeController extends FrontController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $repair_types = RepairType::whereHas('imagesrepairs', function ($query){
            $query->where('is_primary', true);
        })->with([
            'imagesrepairs' => function($query){
            $query->where('is_primary', true)->where('status', true);
        },
            'features' => function($query){
                $query->where('status', true);
        }
        ])
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        //dd($repair_types);

        return view('front.repair_type.index')->with([
            'repair_types' => $repair_types
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
        $repair_type = RepairType::with([
            'imagesrepairs' => function($clientQuery) {
                $clientQuery->where('status', true);
        },
            'repairs.reviewsrepairs' => function($clientQuery) {
                $clientQuery->where('approval_status', 'approved');
            }
        ])
            ->where('status', true)
            ->findOrFail($id);


        //dd($repair_type->getAggregatedRating());
        return view('front.repair_type.profile')
            ->with([
                'repair_type' => $repair_type,
            ]);
    }
}
