<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'admin' => [
        'booking' => [
            'name' =>  'Booking',
            'actions' => [
                'car_booking' => 'admin/car_booking',
				 'repair_booking' => 'admin/repair_booking',
                'service_booking' =>  'admin/service_booking'
            ],
            'icon' => 'ti-control-forward'
        ],
        
        'car_type' => [
            'name' => 'Car Type',
            'actions' => [
                'view' => 'admin/car_type',
            ],
            'icon' => 'ti-car'
        ],
		
		'repair_type' => [
            'name' => 'Repair Type',
            'actions' => [
                'view' => 'admin/repair_type',
            ],
            'icon' => 'ti-car'
        ],
		
        'facility' => [
            'name' => 'Facility',
            'actions' => [
                'view' => 'admin/facility',
            ],
            'icon' => 'ti-crown'
        ],
		
		'feature' => [
            'name' => 'Service_Menu',
            'actions' => [
                'view' => 'admin/feature',
            ],
            'icon' => 'ti-crown'
        ],
		
		
        
        'user' => [
            'name' => 'User',
            'actions' => [
                'view' => 'admin/user',
            ],
            'icon' => 'ti-user'
        ],
        'slider' => [
            'name' => 'Slider',
            'actions' => [
                'view' => 'admin/slider',
            ],
            'icon' => 'ti-layout-grid2'
        ],
        'Testimonial' => [
            'name' => 'Testimonial',
            'actions' => [
                'view' => 'admin/review',
            ],
            'icon' => 'ti-star'
        ],
        'Page' => [
            'name' => 'Page',
            'actions' => [
                'view' => 'admin/page',
            ],
            'icon' => 'ti-star'
        ],
    ],
  
    'servicer' => [
        'booking' => [
            'name' =>  'Booking',
            'actions' => [
                'car_booking' => 'servicer/car_booking',
				'repair_booking' => 'servicer/repair_booking',
                'service_booking' =>  'servicer/service_booking'
            ],
            'icon' => 'ti-control-forward'
        ],
		
		'repair_type' => [
            'name' => 'Repair Type',
            'actions' => [
                'view' => 'servicer/repair_type',
            ],
            'icon' => 'ti-car'
        ],
		
		'feature' => [
            'name' => 'Service_Menu',
            'actions' => [
                'view' => 'servicer/feature',
            ],
            'icon' => 'ti-crown'
        ],
        
        'part' => [
            'name' => 'Part Menu',
            'actions' => [
                'view' => 'servicer/part',
            ],
            'icon' => 'ti-car'
        ],
        
      

    ],

    

];
