<?php
/**
* Plans.php - Model file
*
* This file is part of the Plans component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Plans\Models;

use App\Yantrana\Base\BaseModel;

class PlanModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'plans';

    /**
     * @var array - The attributes that should be casted to native types..
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
