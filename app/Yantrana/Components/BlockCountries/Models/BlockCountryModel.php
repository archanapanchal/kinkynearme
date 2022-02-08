<?php
/**
* BlockCountries.php - Model file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries\Models;

use App\Yantrana\Base\BaseModel;

class BlockCountryModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'block_countries';

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
