<?php
/**
* ManageForumTopicsEngine.php - Main component file
*
* This file is part of the ForumTopics component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\ForumTopics\Repositories\ManageForumTopicsRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Components\UserSetting\Repositories\UserSettingRepository;
use App\Yantrana\Components\Kinks\Repositories\ManageKinksRepository;
use App\Yantrana\Components\ForumTopics\Models\ForumTopicInterestModel;

class ManageForumTopicsEngine extends BaseEngine
{

    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var ManageForumTopicsRepository - ManageForumTopics Repository
     */
    protected $manageForumTopicsRepository;

    /**
     * @var  ManageKinksRepository $manageKinksRepository - UserSetting Repository
     */
    protected $manageKinksRepository;




    /**
     * Constructor.
     *
     * @param ManageForumTopicsRepository $manageForumTopicsRepository - ManageForumTopics Repository
     *-----------------------------------------------------------------------*/
    public function __construct(ManageForumTopicsRepository $manageForumTopicsRepository,UserSettingRepository $userSettingRepository, ManageKinksRepository $manageKinksRepository)
    {
        $this->manageForumTopicsRepository = $manageForumTopicsRepository;
        $this->manageKinksRepository        = $manageKinksRepository;
    }

    /**
     * get forumTopic list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareForumTopicList()
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetchListData();

        // echo "<pre>";
        // print_r($forumTopicCollection);exit();

        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'users__id',
            'forum_category',
            'description',
            // 'replies',
            'view_count',
            'reply_count',
            'created_at' => function ($forumTopicData) {
                return formatDate($forumTopicData['created_at']);
            },
            'updated_at' => function ($forumTopicData) {
                return formatDate($forumTopicData['updated_at']);
            },
        ];

        // echo "<pre>";
        // print_r($forumTopicCollection);exit();

        return $this->dataTableResponse($forumTopicCollection, $requireColumns);
    }

    /**
     * Process add new forumTopic.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddNewForumTopic($inputData)
    {

        $storeData = [
            'title'         => $inputData['title'],
            'category_id'         => $inputData['category_id'],
            'description'         => $inputData['description'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id(),
            'image'     => $inputData['forum_images'],

        ];

        //Check if forumTopic added
        if ($this->manageForumTopicsRepository->store($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic added successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('ForumTopic not added.'));
    }

    /**
     * get forumTopic edit data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUpdateData($forumTopicUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetch($forumTopicUId);

        //if is empty
        if (__isEmpty($forumTopicCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic does not exist'));
        }

        $ForumImageUrl = '';
        $forumImageFolderPath = getPathByKey('forum_images', ['{_uid}' => authUID()]);
        $ForumImageUrl = getMediaUrl($forumImageFolderPath, $forumTopicCollection->image);

        $forumTopicEditData = [];
        if (!__isEmpty($forumTopicCollection)) {
            $forumTopicEditData = [
                '_id'             => $forumTopicCollection['_id'],
                '_uid'             => $forumTopicCollection['_uid'],
                'title'         => $forumTopicCollection['title'],
                'category_id'         => $forumTopicCollection['category_id'],
                'description'         => $forumTopicCollection['description'],
                'created_at'     => formatDate($forumTopicCollection['created_at']),
                'updated_at'     => formatDate($forumTopicCollection['updated_at']),
                'status'         => $forumTopicCollection['status'],
                'image' => $ForumImageUrl,
            ];
        }

        $getInterestData = $this->manageForumTopicsRepository->fetchTopicInterestList($forumTopicCollection['_id']);

        return $this->engineReaction(1, [
            'forumTopicEditData' => $forumTopicEditData,
            'getInterestData' => $getInterestData
        ]);
    }

    /**
     * Process add new forumTopic.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForEditNewForumTopic($inputData, $forumTopicUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetch($forumTopicUId);

        //if is empty
        if (__isEmpty($forumTopicCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic does not exist'));
        }
        //update data
        $updateData = [
            'title'         => $inputData['title'],
            'category_id'         => $inputData['category_id'],
            'description'         => $inputData['description'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'image'     => ($inputData['forum_images']) ? $inputData['forum_images'] : $forumTopicCollection->image  ,
        ];

        //Check if forumTopic updated
        if ($this->manageForumTopicsRepository->update($forumTopicCollection, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('ForumTopic not updated.'));
    }

    /**
     * Process delete.
     *
     * @param int forumTopicUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDelete($forumTopicUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetch($forumTopicUId);

        //if is empty
        if (__isEmpty($forumTopicCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic does not exist.'));
        }

        //Check if forumTopic deleted
        if ($this->manageForumTopicsRepository->delete($forumTopicCollection)) {
            return $this->engineReaction(1, [
                'forumTopicUId' => $forumTopicCollection->_uid
            ], __tr('ForumTopic deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('ForumTopic not deleted.'));
    }

    public function forumTopicDelete($forumTopicUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetch($forumTopicUId);

        //if is empty
        if (__isEmpty($forumTopicCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic does not exist.'));
        }

        //Check if forumTopic deleted
        if ($this->manageForumTopicsRepository->delete($forumTopicCollection)) {

            $getInterest = ForumTopicInterestModel::where('forum_topic_id',$forumTopicCollection->_id)->get();

            if(!empty($getInterest)){
                ForumTopicInterestModel::where('forum_topic_id',$forumTopicCollection->_id)->delete();
            }

            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('ForumTopic not deleted.'));
    }


    /**
     * Get parent forum categories
     *
     * @param int forumTopicUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function getInterestsList()
    {
        return $this->manageKinksRepository->fetchList();
    }


    public function fetchForumTopicDetail($forumTopicUId)
    {
        $forumTopics = $this->manageForumTopicsRepository->fetchTopicDetail($forumTopicUId);

        return $forumTopics;
    }


    
    public function fetchForumTopicList()
    {
        $forumTopics = $this->manageForumTopicsRepository->fetchList();

        return $forumTopics;
    }

    public function fetchCategoryForumTopicList($categoryForumUId)
    {
        $forumTopics = $this->manageForumTopicsRepository->fetchCategoryForumList($categoryForumUId);

        return $forumTopics;
    }

        /**
     * Process add new resply to forumTopic.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddReplyToForumTopic($inputData)
    {

        $storeData = [
            'forum_topic_id'         => $inputData['topic_id'],
            'comment'         => $inputData['comment'],
            'status'        => 1,
            // 'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id()
        ];

        //Check if forumTopic added
        if ($this->manageForumTopicsRepository->reply($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Reply added successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Reply not added.'));
    }

    public function prepareForAddViewCountToForumTopic($inputData)
    {
        
        if ($this->manageForumTopicsRepository->updateUserForumTopicViewCount($inputData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumTopic view count added successfully'));
        }
    }

    /**
     * get forumTopic list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareForumTopicReplyList($forumTopicUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetchTopicReplyListData($forumTopicUId);

        // echo "<pre>";
        // print_r($forumTopicCollection);exit();

        $requireColumns = [
            '_id',
            '_uid',
            'forum_topic',
            'username',
            'reply',
            'status' => function ($forumTopicData) {
                return configItem('status_codes', $forumTopicData['status']);
            },
            'created_at' => function ($forumTopicData) {
                return formatDate($forumTopicData['created_at']);
            },
            'updated_at' => function ($forumTopicData) {
                return formatDate($forumTopicData['updated_at']);
            },
        ];

        return $this->dataTableResponse($forumTopicCollection, $requireColumns);
    }

    /**
     * Process delete.
     *
     * @param int forumTopicUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processReplyDelete($forumTopicReplyUId)
    {
        $forumTopicCollection = $this->manageForumTopicsRepository->fetchTopicReply($forumTopicReplyUId);


        // print_r($forumTopicCollection->forum_topic_id);exit;
        //if is empty
        if (__isEmpty($forumTopicCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Reply does not exist.'));
        }

        //Check if forumTopic deleted
        if ($this->manageForumTopicsRepository->deleteForumTopicReply($forumTopicCollection)) {
             return $this->engineReaction(1, ['show_message' => true], __tr('Reply deleted successfully'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('Reply not deleted.'));
    }

    public function processReplyStatusChange($forumTopicReplyUId)
    {
        $forumTopicReplyCollection = $this->manageForumTopicsRepository->fetchTopicReply($forumTopicReplyUId);

        //if is empty
        if (__isEmpty($forumTopicReplyCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Forum Reply does not exist'));
        }

        $status = ($forumTopicReplyCollection->status == 1) ? 2 : 1;
        

        //update data
        $updateData = [
            'status'         => $status
        ];

        //Check if forumTopic updated
        if ($this->manageForumTopicsRepository->statusUpdate($forumTopicReplyUId, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Status updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Status not updated.'));
    }

    public function searchRequestFromHomeForForum($searchData,$checkUserStatusForAdmin = false)
    {

        $responce_forum_data = $this->manageForumTopicsRepository->searchFromHome($searchData);

        return $responce_forum_data;
        
    }
}
