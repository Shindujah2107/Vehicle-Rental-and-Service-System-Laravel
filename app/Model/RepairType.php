<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RepairType extends Model
{
    protected $table = 'repair_types';

    protected $fillable = ['name', 'cost_per_day','description', 'repair_service', 'status'];

    public function imagesrepairs()
    {
        return $this->hasMany('App\Model\ImageRepair');
    }

    public function repairs()
    {
        return $this->hasMany('App\Model\Repair');
    }

    public function features()
    {
        return $this->belongsToMany('App\Model\Feature', 'feature_repair_type')->withTimestamps();
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->cost_per_day - (($this->cost_per_day/100) * $this->discount_percentage);
    }

    public function getFinalPriceAttribute()
    {
        $after_service_charge = $this->discountedPrice + (($this->discountedPrice/100) * config('app.service_charge_percentage'));
        $after_vat = $after_service_charge + (($after_service_charge/100) * config('app.vat_percentage'));
        return $after_vat;
    }


    public function getRatingsCount(){
        $rating_count = 0;
        foreach($this->repairs as $repair){
            foreach($repair->reviewsrepairs as $reviewrepair){
                if($reviewrepair->approval_status == 'approved'){
                    if($reviewrepair->rating != 0) {
                        $rating_count++;
                    }
                }
            }
        }
        return $rating_count;
    }

    public function getAggregatedRating(){
        $total_rating = 0;
        $rating_count = 0;
        foreach($this->repairs as $repair){
            foreach($repair->reviewsrepairs as $reviewrepair){
                if($reviewrepair->approval_status == 'approved'){
                    if($reviewrepair->rating != 0) {
                        $total_rating = $total_rating + $reviewrepair->rating;
                        $rating_count++;
                    }
                }
            }
        }

        if($total_rating > 0 && $rating_count > 0){
            return $total_rating/$rating_count;
        } else{
            return 0;
        }
    }

}
