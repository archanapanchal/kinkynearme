<?php
/**
* UserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Redirect;
use App\Yantrana\Components\ForumTopics\ManageForumTopicsEngine;
use App\Yantrana\Components\ForumCategories\ManageForumCategoriesEngine;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\ForumTopics\Repositories\ManageForumTopicsRepository;
use App\Yantrana\Components\ForumTopics\Requests\{ManageForumTopicsAddRequest, ManageForumTopicsEditRequest};
use App\Yantrana\Components\User\Models\{
    UserSubscription
};
use App\Yantrana\Components\ForumTopics\Models\{
    ForumTopicModel,ForumTopicInterestModel
};
use YesSecurity;

class UserForumController extends BaseController
{
    /**
     * @var  ManageForumTopicsEngine $manageForumTopicsEngine - Manage ForumTopics Engine
     */
    protected $manageForumTopicsEngine;
    protected $manageForumTopicsRepository;
    protected $manageForumCategoriesEngine;
    protected $mediaEngine;

    /**
     * Constructor.
     *
     * @param UserEngine $userEngine - User Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageForumTopicsEngine $manageForumTopicsEngine,ManageForumTopicsRepository $manageForumTopicsRepository, ManageForumCategoriesEngine $manageForumCategoriesEngine,MediaEngine $mediaEngine)
    {
        $this->manageForumTopicsEngine = $manageForumTopicsEngine;
        $this->manageForumTopicsRepository = $manageForumTopicsRepository;
        $this->manageForumCategoriesEngine = $manageForumCategoriesEngine;
        $this->mediaEngine = $mediaEngine;
    }

    /* discussion forum view front-end */
    public function discussionForum(Request $request)
    {   

        if(getUserPlan(Auth::user()->_id) == config('constant.trial')){
            abort(404);
        }

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        $processReaction['data']['active_tab'] = 6;
        $processReaction['data']['forumCategoriesTopics'] = $this->manageForumTopicsEngine->fetchForumTopicList()->toArray();
        // echo "<pre>";
        // print_r($processReaction['data']['forumCategoriesTopics']);exit();
        $processReaction['data']['forumCatgories'] = $this->manageForumCategoriesEngine->prepareParentForumCategoryList();
        $processReaction['data']['interestsList'] = $this->manageForumTopicsEngine->getInterestsList();
        return $this->loadProfileView('user.discussion-forum', $processReaction['data']);
    }


    /**
     * Handle process contact request.
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function createUserTopic(ManageForumTopicsAddRequest $request)
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
                $this->redirectTo('user.view.discussion-forum')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    public function updateUserTopic(ManageForumTopicsEditRequest $request, $forumTopicUId)
    {

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
                $this->redirectTo(url()->previous())
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
    public function deleteTopic($forumTopicUId)
    {
        $processReaction = $this->manageForumTopicsEngine->forumTopicDelete($forumTopicUId);

        if ($processReaction['reaction_code'] === 1) {

            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo(url()->previous())
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /* discussion forum view front-end */
    public function allDiscussionForum($categoryForumUId)
    {

        if(getUserPlan(Auth::user()->_id) == config('constant.trial')){
            abort(404);
        }

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        $processReaction['data']['active_tab'] = 6;
        $processReaction['data']['forumCategoriesTopics'] = $this->manageForumTopicsEngine->fetchCategoryForumTopicList($categoryForumUId)->toArray();

        $processReaction['data']['forumCatgories'] = $this->manageForumCategoriesEngine->prepareParentForumCategoryList();
        $processReaction['data']['interestsList'] = $this->manageForumTopicsEngine->getInterestsList();
        return $this->loadProfileView('user.all-discussion-forum', $processReaction['data']);
    }



    public function topicDetail($forumTopicUId)
    {

        if(getUserPlan(Auth::user()->_id) == config('constant.trial')){
            abort(404);
        }
        
        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        $processReaction['data']['active_tab'] = 6;

        $processReaction['data']['forumTopicDetail'] = $this->manageForumTopicsEngine->fetchForumTopicDetail($forumTopicUId)->toArray();

        // echo "<pre>";
        // print_r($processReaction['data']['forumTopicDetail']);exit();

        return $this->loadProfileView('user.forum-topic-detail', $processReaction['data']);
    }

    public function replyToTopic(Request $request)
    {
        
        $processReaction = $this->manageForumTopicsEngine
            ->prepareForAddReplyToForumTopic($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo(url()->previous())
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }

    }

    public function incrementUserForumViewCount(Request $request)
    {
        if ($request->ajax()) {
            $processReaction = $this->manageForumTopicsEngine
            ->prepareForAddViewCountToForumTopic($request->all());
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }


    /**
     * Upload Forum Temp Media.
     *
     * @param object Request $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadForumTempMedia(Request $request)
    {
        $processReaction = $this->mediaEngine
            ->processUploadForumImage($request->all(), 'forum_images');


        return $this->processResponse($processReaction, [], [], true);
    }
}
