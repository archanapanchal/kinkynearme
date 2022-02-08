<?php

namespace App\Yantrana\Components\User\Models;

use App\Yantrana\Base\BaseModel;

class FavouriteModal extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'favourites';


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];
}
