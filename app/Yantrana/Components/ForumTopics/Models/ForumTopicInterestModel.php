<?php
/**
* ForumCategories.php - Model file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Models;

use App\Yantrana\Base\BaseModel;

class ForumTopicInterestModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'forum_topic_interests';

    /**
     * @var array - The attributes that should be casted to native types..
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = ['uid','forum_topic_id','interest_id'];

    public function interest()
    {
        return $this->hasOne('App\Yantrana\Components\Kinks\Models\KinkModel', '_id', 'interest_id');
    }
}
