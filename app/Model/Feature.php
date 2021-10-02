<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';

    protected $fillable = ['name', 'icon', 'description', 'status'];

    public function repair_types()
    {
        return $this->belongsToMany('App\Model\RepairType', 'feature_repair_type')->withTimestamps();
    }

}
