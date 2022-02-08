<?php
/**
* ManageForumTopicsRepository.php - Repository file
*
* This file is part of the ForumTopics component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\ForumTopics\Models\ForumTopicModel;
use App\Yantrana\Components\ForumTopics\Models\ForumTopicInterestModel;
use App\Yantrana\Components\ForumCategories\Models\ForumCategoryModel;
use App\Yantrana\Components\ForumTopics\Blueprints\ManageForumTopicsRepositoryBlueprint;
use File;

class ManageForumTopicsRepository extends BaseRepository implements ManageForumTopicsRepositoryBlueprint
{
    /**
     * Constructor.
     *
     * @param ForumTopic $forumTopic - forumTopic Model
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListData()
    {
        $dataTableConfig = [
            'searchable' => [
                'forum_topics.title',
                'categories.title',
            ]
        ];

        //return ForumTopicModel::dataTables($dataTableConfig)->toArray();


        $query = ForumTopicModel::leftJoin('forum_categories as categories', 'categories._id', '=', 'forum_topics.category_id')
            ->select(
                __nestedKeyValues([
                    'forum_topics' => [
                        '_id',
                        '_uid',
                        'created_at',
                        'updated_at',
                        'status',
                        'description',
                        'title',
                        'category_id',
                        'reply_count',
                        'view_count'
                    ],
                    'categories' => [
                        'title as forum_category',
                    ]
                ])
            )
            ->dataTables($dataTableConfig);
         

        return $query->toArray();
    }

    /**
     * fetch forumTopic data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return ForumTopicModel::where('_id', $idOrUid)->first();
        } else {
            return ForumTopicModel::where('_uid', $idOrUid)->first();
        }
    }

    /**
     * fetch forumTopic data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    // public function fetchForumCategoryName($categoryId)
    // {
    //     //check is numeric
    //     return ForumCategoryModel::where('id', $$categoryId)->first();
    // }

    /**
     * store new forumTopic.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function store($input)
    {

        // $input['category_id'] = implode(',', $input['category_id']);
        $forumTopic = new ForumTopicModel;

        $keyValues = [
            'title',
            'category_id',
            'description',
            'status',
            'users__id'
        ];

        // Store New ForumTopic
        if ($forumTopic->assignInputsAndSave($input, $keyValues)) {
            activityLog($forumTopic->title . ' forumTopic created. ');
            return true;
        }
        return false;
    }

    /**
     * Update ForumTopic Data
     *
     * @param object $forumTopic
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function update($forumTopic, $updateData)
    {
        // Check if information updated
        if ($forumTopic->modelUpdate($updateData)) {
            activityLog($forumTopic->title . ' forumTopic updated. ');
            return true;
        }

        return false;
    }

    /**
     * Delete forumTopic.
     *
     * @param object $forumTopic
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function delete($forumTopic)
    {
        // Check if forumTopic deleted
        if ($forumTopic->delete()) {
            activityLog($forumTopic->title . ' forumTopic deleted. ');
            return  true;
        }

        return false;
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchList()
    {
        return ForumCategoryModel::with(['forumTopics' => function ($query) 
                                    {
                                        $query->with(['forumInterests' => function ($query) 
                                            {
                                                $query->with('interest');
                                            }
                                        ]);
                                    }
                                ])->has('forumTopics')->get();
    }

    public function fetchCategoryForumList($forumCategoryUId)
    {
        return ForumCategoryModel::where('_uid',$forumCategoryUId)->with(['forumTopics' => function ($query) 
                                    {
                                        $query->with(['forumInterests' => function ($query) 
                                            {
                                                $query->with('interest');
                                            }
                                        ]);
                                    }
                                ])->has('forumTopics')->first();
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchTopicInterestList($topicId)
    {
        return ForumTopicInterestModel::where('forum_topic_id',$topicId)->pluck('interest_id')->toArray();
    }
}
