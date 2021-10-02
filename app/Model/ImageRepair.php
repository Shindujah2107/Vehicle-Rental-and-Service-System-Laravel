<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImageRepair extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imagesrepairs';

    protected $fillable = ['name', 'caption', 'is_primary', 'repair_type_id'];

    /**
     * Get the gallery that owns the image.
     */
    public function repair_type()
    {
        return $this->belongsTo('App\Model\RepairType');
    }
}
