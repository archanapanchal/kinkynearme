<?php

/**
* Country.php - Model file
*
* This file is part of the Country component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Support\Country\Models;

use App\Yantrana\Base\BaseModel;

class Country extends BaseModel
{
    /**
     * @var string $table - The database table used by the model.
     */
    protected $table = "countries";

    /**
     * @var string $table - The database table used by the model.
     */
    public $timestamps = false;

    /**
     * @var array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [
        "_id" => "integer",
    ];

    /**
     * @var array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * The generate UID or not
     *
     * @var string
     *----------------------------------------------------------------------- */

    protected $isGenerateUID = false;
}
