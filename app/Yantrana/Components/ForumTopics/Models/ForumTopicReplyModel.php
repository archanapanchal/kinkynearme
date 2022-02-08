<?php
/**
* ForumCategories.php - Model file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Models;

use App\Yantrana\Base\BaseModel;

class ForumTopicReplyModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'forum_topic_replies';

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


    public function userDetail()
    {
        return $this->hasOne('App\Yantrana\Components\User\Models\User', '_id', 'users__id');
    }
    
}
