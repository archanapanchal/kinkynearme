<?php
/**
* ForumCategories.php - Model file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\ForumCategories\Models\ForumCategoryModel;
use App\Yantrana\Components\User\Models\UserProfile;
use App\Yantrana\Components\User\Models\User;

class ForumTopicModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'forum_topics';

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


    public function forumInterests()
    {
        return $this->hasMany('App\Yantrana\Components\ForumTopics\Models\ForumTopicInterestModel', 'forum_topic_id', '_id');
    }

    public function category()
    {
        return $this->hasOne('App\Yantrana\Components\ForumCategories\Models\ForumCategoryModel', '_id', 'category_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Yantrana\Components\ForumTopics\Models\ForumTopicReplyModel', 'forum_topic_id', '_id')->where('status',1);
    }

    /**
     * Get the profile record associated with the user.
     */
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'users__id', '_id');
    }

    public function userDetail()
    {
        return $this->hasOne(User::class, '_id', 'users__id');
    }
    
}
