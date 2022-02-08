<?php

/**
* UserSettingModel.php - Model file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\UserSetting\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Country\Models\Country;

class UserSettingModel extends BaseModel
{
    /**
     * @var string $table - The database table used by the model.
     */
    protected $table = "user_settings";

    /**
     * The generate UID or not
     *
     * @var string
     *----------------------------------------------------------------------- */

    protected $isGenerateUID = false;

    /**
     * @var array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [
        "_id"            => "integer",
        "users__id"        => "integer"
    ];

    /**
     * @var array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
