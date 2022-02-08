<?php
/**
* Configuration.php - Model file
*
* This file is part of the Configuration component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Configuration\Models;

use App\Yantrana\Base\BaseModel;
use Cache;

class ConfigurationModel extends BaseModel
{
    /**
     * @var  string $table - The database table used by the model.
     */
    protected $table = "configurations";

    /**
     * The generate UID or not
     *
     * @var string
     *----------------------------------------------------------------------- */

    protected $isGenerateUID = false;

    /**
     * @var  array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [];

    /**
     * @var  array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Caching Ids related to this model which may need to clear on add/update/delete.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $cacheIds = [
        'cache.app.setting.all'
    ];
}
