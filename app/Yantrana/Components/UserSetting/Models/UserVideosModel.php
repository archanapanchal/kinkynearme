<?php

/**
* UserPhotosModel.php - Model file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\UserSetting\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Country\Models\Country;

class UserVideosModel extends BaseModel
{
    /**
     * @var string $table - The database table used by the model.
     */
    protected $table = "user_videos";

    /**
     * @var array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [
        "id"            => "integer"
    ];

    /**
     * @var array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
