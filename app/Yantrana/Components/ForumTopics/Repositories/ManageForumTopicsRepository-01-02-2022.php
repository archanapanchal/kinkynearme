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
use App\Yantrana\Components\ForumTopics\Models\ForumTopicReplyModel;
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
            // ->with('replies')
            // ->leftJoin('forum_topic_replies as replies', 'forum_topics._id', '=', 'replies.forum_topic_id')
            ->select(
                __nestedKeyValues([
                    'forum_topics' => [
                        '_id',
                        '_uid',
                        'users__id',
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
                        '_id as cat_id',
                        '_uid as category_uid',
                        'title as forum_category',
                    ],
                    // 'replies' => [
                    //     'count(*) as count_reply'
                    // ],
                ])
            )
            // ->groupBy('forum_topics.title')
            ->dataTables($dataTableConfig);
        //     echo "<pre>";
        // print_r($query->toArray());exit();

        return $query->toArray();
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchTopicReplyListData($forumTopicUId)
    {
        $dataTableConfig = [
            'searchable' => [
                'replies.comment',
                'user.username',
            ]
        ];

        //return ForumTopicModel::dataTables($dataTableConfig)->toArray();


        $query = ForumTopicModel::
                leftJoin('forum_topic_replies as replies', 'forum_topics._id', '=', 'replies.forum_topic_id')
                ->leftJoin('users as user', 'replies.users__id', '=', 'user._id')
            ->select(
                __nestedKeyValues([
                    'forum_topics' => [
                        '_id as forum_topic_id',
                        '_uid as forum_topic_uid',
                        'users__id as forum_topic_user_id',
                        'title as forum_topic'
                    ],
                    'replies' => [
                        '_id',
                        '_uid',
                        'users__id as reply_user_id',
                        'comment as reply',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                    'user' => [
                        'username',
                    ]
                ])
            )
            ->where('forum_topics._uid',$forumTopicUId)
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
    public function fetchTopicReply($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return ForumTopicReplyModel::where('_id', $idOrUid)->first();
        } else {
            return ForumTopicReplyModel::where('_uid', $idOrUid)->first();
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
            'users__id',
            'image'
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
     * Update ForumTopic Data
     *
     * @param object $forumTopic
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function statusUpdate($forumTopicReplyUId, $updateData)
    {
        // Check if information updated

        $updateStatus = ForumTopicReplyModel::where('_uid',$forumTopicReplyUId)->update($updateData);

        if ($updateStatus == 1) {
            activityLog($forumTopicReplyUId . ' Reply status updated. ');
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
                                        $query->with('replies');
                                        $query->with('userDetail');
                                    }
                                ])->has('forumTopics')->get();
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchTopicDetail($forumTopicUId)
    {

        return ForumTopicModel::with('category')
                                        ->with(['forumInterests' => function ($query) 
                                            {
                                                $query->with('interest');
                                            }
                                        ])
                                        ->where('_uid', $forumTopicUId)
                                        ->with(['replies' => function ($query) 
                                            {
                                                $query->with(['userDetail' => function ($query) 
                                                    {
                                                        $query->with('userProfile');
                                                    }
                                                ]);
                                            }
                                        ])
                                        ->first();

                                        
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchTopicDetailById($forumTopicId)
    {

        return ForumTopicModel::where('_id', $forumTopicId)->first();

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
                                        $query->with('replies');
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



     /**
     * store new forumTopic.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function reply($input)
    {
        $forumTopic = new ForumTopicReplyModel;

        $keyValues = [
            'forum_topic_id',
            'comment',
            'status',
            'users__id'
        ];

        // Store New ForumTopic
        if ($forumTopic->assignInputsAndSave($input, $keyValues)) {
            $getReplyCount = ForumTopicReplyModel::where('forum_topic_id',$input['forum_topic_id'])->count();
            ForumTopicModel::where('_id',$input['forum_topic_id'])->update(['reply_count'=>$getReplyCount]);
            activityLog($this->fetchTopicDetailById($input['forum_topic_id'])->title . ' replyToForumTopic created. ');
            return true;
        }
        return false;
    }

    public function updateUserForumTopicViewCount($input)
    {

        $topicDetail = ForumTopicModel::where('_id', $input['topic_id'])->first();

        $viewCount =  $topicDetail->view_count + 1;

        ForumTopicModel::where('_id', $input['topic_id'])->update(['view_count'=>$viewCount]);

        return true;
    }

    /**
     * Delete forumTopic.
     *
     * @param object $forumTopic
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function deleteForumTopicReply($forumTopicReply)
    {
        
        $url = url()->previous();
        $keys = parse_url($url); // parse the url
        $url_parts = explode("/", $keys['path']); // splitting the path
        $forum_id = $url_parts[3];
        
        // Check if forumTopic deleted
        if ($forumTopicReply->delete()) {
            $getTopicData = ForumTopicModel::where('_uid',$forum_id)->first();
            ForumTopicModel::where('_uid',$forum_id)->update(['reply_count'=>($getTopicData->reply_count-1)]);
            return  true;  
        }

        return false;
    }


    public function searchFromHome($searchData)
    {
        return ForumTopicModel::with('category')
                                        ->with(['forumInterests' => function ($query) 
                                            {
                                                $query->with('interest');
                                            }
                                        ])
                                        ->Where('forum_topics.title', 'like', '%' . $searchData . '%')
                                        ->get()->toArray();
    }
}
