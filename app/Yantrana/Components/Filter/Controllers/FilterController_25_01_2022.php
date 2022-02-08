<?php
/**
* FilterController.php - Controller file
*
* This file is part of the Filter component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Filter\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Filter\FilterEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\User\Models\UserSubscription;
use App\Yantrana\Components\User\UserEngine;

class FilterController extends BaseController
{
    /**
     * @var  FilterEngine $filterEngine - Filter Engine
     */
    protected $filterEngine;
    protected $userEngine;

    /**
     * Constructor
     *
     * @param  FilterEngine $filterEngine - Filter Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(FilterEngine $filterEngine,UserEngine $userEngine)
    {
        $this->filterEngine = $filterEngine;
        $this->userEngine = $userEngine;
    }

    /**
     * Get Filter data and show filter view
     *
     * @param obj CommonUnsecuredPostRequest $request
     * 
     * return view
     *-----------------------------------------------------------------------*/
    public function getFindMatches(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->filterEngine->processFilterData($request->all());

        $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
        $current_user_mutual_like = $this->userEngine->prepareMutualLikeData();


        $current_user_mutual_like_ids = array();
        if (!empty($current_user_mutual_like['data'])) {
                foreach ($current_user_mutual_like['data']['usersData'] as $key => $value) {
                        $current_user_mutual_like_ids[] = $value['user_id'];
                }
        }
            

        $current_user_favourite_ids = array();
        if (!empty($current_user_favourite['data'])) {
                foreach ($current_user_favourite['data']['usersData'] as $key => $value) {
                        $current_user_favourite_ids[] = $value['user_id'];
                }
        }
        
        $processReaction['data']['current_user_favourite_ids'] = $current_user_favourite_ids;
        $processReaction['data']['current_user_mutual_like_ids'] = $current_user_mutual_like_ids;


        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;
        
            //echo "<pre>";print_r($processReaction['data']);exit();


        if ($request->ajax()) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->replaceView('filter.find-matches', $processReaction['data'])
            );
        } else {
            $processReaction['data']['active_tab'] = 1;
            return $this->loadProfileView('filter.filter', $processReaction['data']);
        }
    }

    /**
     * Get Filter data and show filter view
     *
     * @param obj CommonUnsecuredPostRequest $request
     * 
     * return view
     *-----------------------------------------------------------------------*/
    public function searchResults(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->filterEngine->processFilterData($request->all());

        $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
        $current_user_mutual_like = $this->userEngine->prepareMutualLikeData();


        $current_user_mutual_like_ids = array();
        if (!empty($current_user_mutual_like['data'])) {
                foreach ($current_user_mutual_like['data']['usersData'] as $key => $value) {
                        $current_user_mutual_like_ids[] = $value['user_id'];
                }
        }
            

        $current_user_favourite_ids = array();
        if (!empty($current_user_favourite['data'])) {
                foreach ($current_user_favourite['data']['usersData'] as $key => $value) {
                        $current_user_favourite_ids[] = $value['user_id'];
                }
        }
        
        $processReaction['data']['current_user_favourite_ids'] = $current_user_favourite_ids;
        $processReaction['data']['current_user_mutual_like_ids'] = $current_user_mutual_like_ids;


        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;
        

        if ($request->ajax()) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->replaceView('filter.find-matches', $processReaction['data'])
            );
        } else {
            $processReaction['data']['active_tab'] = 1;
            return $this->loadProfileView('filter.search-results', $processReaction['data']);
        }
    }
}
