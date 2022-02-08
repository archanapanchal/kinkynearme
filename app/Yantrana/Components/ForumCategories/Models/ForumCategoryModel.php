<?php
/**
* ForumCategories.php - Model file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumCategories\Models;

use App\Yantrana\Base\BaseModel;

class ForumCategoryModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'forum_categories';

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


    public function forumTopics()
    {
        return $this->hasMany('App\Yantrana\Components\ForumTopics\Models\ForumTopicModel', 'category_id', '_id');
    }
}
