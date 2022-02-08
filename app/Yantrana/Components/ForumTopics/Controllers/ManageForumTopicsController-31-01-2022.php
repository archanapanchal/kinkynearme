<?php
/**
* ManageForumTopicsController.php - Controller file
*
* This file is part of the ForumTopics component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\ForumTopics\ManageForumTopicsEngine;
use App\Yantrana\Components\ForumTopics\Models\ForumTopicModel;
use App\Yantrana\Components\ForumTopics\Models\ForumTopicInterestModel;
use App\Yantrana\Components\ForumCategories\ManageForumCategoriesEngine;
use App\Yantrana\Components\ForumTopics\Requests\{ManageForumTopicsAddRequest, ManageForumTopicsEditRequest};
use Carbon\Carbon;
use YesSecurity;


class ManageForumTopicsController extends BaseController
{
    /**
     * @var ManageForumTopicsEngine - ManageForumTopics Engine
     */
    protected $manageForumTopicsEngine;
    protected $manageForumCategoriesEngine;

    /**
     * Constructor.
     *
     * @param ManageForumTopicsEngine $manageForumTopicsEngine - ManageForumTopics Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageForumTopicsEngine $manageForumTopicsEngine, ManageForumCategoriesEngine $manageForumCategoriesEngine)
    {
        $this->manageForumTopicsEngine = $manageForumTopicsEngine;
        $this->manageForumCategoriesEngine = $manageForumCategoriesEngine;
    }

    /**
     * Show ForumTopic List View.
     *
     *-----------------------------------------------------------------------*/
    public function forumTopicListView()
    {
        return $this->loadManageView('forum-topics.manage.list');
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getDatatableData()
    {
        return $this->manageForumTopicsEngine->prepareForumTopicList();
    }

    /**
     * Show ForumTopic Add View.
     *
     *-----------------------------------------------------------------------*/
    public function forumTopicAddView()
    {
        return $this->loadManageView('forum-topics.manage.add', [
                    'forumCatgories' => $this->manageForumCategoriesEngine->prepareParentForumCategoryList(),
                    'interestsList' => $this->manageForumTopicsEngine->getInterestsList(),
                ]);
    }

    /**
     * Handle add new forumTopic request.
     *
     * @param ManageForumTopicsAddRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processAddForumTopic(ManageForumTopicsAddRequest $request)
    {

        $processReaction = $this->manageForumTopicsEngine
            ->prepareForAddNewForumTopic($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {

            if(!empty($request['interest_id'])){
                foreach ($request['interest_id'] as $key => $value) {
                    $lastInsertedTopic = ForumTopicModel::orderBy('_id','desc')->first();

                    $createArr = [
                        '_uid' => YesSecurity::generateUid(),
                        'forum_topic_id' => $lastInsertedTopic->_id,
                        'interest_id' => $value
                    ];
                    ForumTopicInterestModel::create($createArr);
                }
            }
            
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.forum-topic.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show ForumTopic Edit View.
     *
     *-----------------------------------------------------------------------*/
    public function forumTopicEditView($forumTopicUId)
    {
        $processReaction = $this->manageForumTopicsEngine->prepareUpdateData($forumTopicUId);

        $processReaction['data']['forumCategories'] = $this->manageForumCategoriesEngine->prepareParentForumCategoryList($forumTopicUId);

        $processReaction['data']['interestsList'] = $this->manageForumTopicsEngine->getInterestsList();
        return $this->loadManageView('forum-topics.manage.edit', $processReaction['data']);
    }

    /**
     * Handle edit new forumTopic request.
     *
     * @param ManageForumTopicsEditRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processEditForumTopic(ManageForumTopicsEditRequest $request, $forumTopicUId)
    {
            // print_r($request->all());exit();
        $processReaction = $this->manageForumTopicsEngine
            ->prepareForEditNewForumTopic($request->all(), $forumTopicUId);

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {

            if(!empty($request['interest_id'])){
                    ForumTopicInterestModel::where('forum_topic_id',$request['topic_id'])->delete();
                foreach ($request['interest_id'] as $key => $value) {

                    $createArr = [
                        '_uid' => YesSecurity::generateUid(),
                        'forum_topic_id' => $request['topic_id'],
                        'interest_id' => $value
                    ];
                    ForumTopicInterestModel::create($createArr);
                }
            }

            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.forum-topic.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Handle delete forumTopic data request.
     *
     * @param int $forumTopicUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function delete($forumTopicUId)
    {
        $processReaction = $this->manageForumTopicsEngine->processDelete($forumTopicUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }


    /**
     * Show ForumTopic List View.
     *
     *-----------------------------------------------------------------------*/
    public function forumTopicReplyListView($forumTopicUId)
    {
        $processReaction = $this->manageForumTopicsEngine->prepareUpdateData($forumTopicUId);
        $processReaction['data']['forumTopicUId'] = $forumTopicUId;
        $processReaction['data']['title'] = $processReaction['data']['forumTopicEditData']['title'];

        return $this->loadManageView('forum-topics.manage.reply-list',$processReaction['data']);
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getReplyDatatableData($forumTopicUId)
    {
        return $this->manageForumTopicsEngine->prepareForumTopicReplyList($forumTopicUId);
    }

    /**
     * Handle delete forumTopic data request.
     *
     * @param int $forumTopicUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function deleteForumTopicReply($forumTopicReplyUId)
    {
        $processReaction = $this->manageForumTopicsEngine->processReplyDelete($forumTopicReplyUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo(url()->previous())
        );
    }

    public function changeReplyStatus($forumTopicReplyUId)
    {
        $processReaction = $this->manageForumTopicsEngine->processReplyStatusChange($forumTopicReplyUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo(url()->previous())
        );
    }
}
