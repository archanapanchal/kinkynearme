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
            'forum_category',
            'description',
            'reply_count',
            'view_count',
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
            'users__id'     => Auth::id()
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
}
