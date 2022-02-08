<?php
/**
* Kinks.php - Model file
*
* This file is part of the Kinks component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Kinks\Models;

use App\Yantrana\Base\BaseModel;

class KinkModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'kinks';

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
