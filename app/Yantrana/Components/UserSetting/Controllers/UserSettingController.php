<?php
/**
* UserSettingController.php - Controller file
*
* This file is part of the UserSetting component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\UserSetting\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\UserSetting\Requests\{
    UserBasicSettingAddRequest,
    UserProfileSettingAddRequest,
    UserSettingRequest,
    UserProfileWizardRequest
};
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\UserSetting\UserSettingEngine;
use App\Yantrana\Components\User\UserEngine;
use App\Yantrana\Components\User\Repositories\UserRepository;

class UserSettingController extends BaseController
{
    /**
     * @var  UserSettingEngine $userSettingEngine - UserSetting Engine
     */
    protected $userSettingEngine;
    protected $userEngine;
    protected $userRepository;

    /**
     * Constructor
     *
     * @param  UserSettingEngine $userSettingEngine - UserSetting Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(UserSettingEngine $userSettingEngine,UserEngine $userEngine,UserRepository $userRepository)
    {
        $this->userSettingEngine = $userSettingEngine;
        $this->userEngine         = $userEngine;
        $this->userRepository = $userRepository;
    }

    /**
     * Show user setting view.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function getUserSettingView($pageType)
    {
        $processReaction = $this->userSettingEngine->prepareUserSettings($pageType);

        abort_if($processReaction['reaction_code'] == 18, 404, $processReaction['message']);

        return $this->loadPublicView('user.settings.settings', $processReaction['data']);
    }

    /**
     * Get UserSetting Data.
     *
     * @param string $pageType
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processStoreUserSetting(UserSettingRequest $request, $pageType)
    {
        $processReaction = $this->userSettingEngine
            ->processUserSettingStore($pageType, $request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process store user basic settings.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserBasicSetting(UserBasicSettingAddRequest $request)
    {
        $processReaction = $this->userSettingEngine->processStoreUserBasicSettings($request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process profile Update Wizard.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function profileUpdateWizard(UserProfileWizardRequest $request)
    {
        $processReaction = $this->userSettingEngine->processStoreProfileWizard($request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process store user basic settings.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processLocationData(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userSettingEngine->processStoreLocationData($request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process upload profile image.
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadProfileImage(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userSettingEngine->processUploadProfileImage($request->all(), 'profile');

        return $this->processResponse($processReaction, [], [], true);
    }

    public function deleteProfileImage($username){

            $processReaction = $this->userSettingEngine->processDeleteProfileImage($username, 'profile');


            $processReaction = $this->userEngine->prepareUserProfile($username);
            $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
            $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

            $blockUser_array = array();
            foreach ($blockUserCollection as $key => $value) {
                $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
            }
            $PlanByType = $this->userEngine->fetchPlanByType();

            
            if (empty($UserSubscription_plan)) {
                $fetchPlan['data'] = "";
            }else{
                $fetchPlan = $this->userEngine->fetchPlan($UserSubscription_plan['plan_id']);
            }
              
            $processReaction['data']['planbytype'] = "";
            if (!empty($PlanByType['data'])) {
                $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
            }
            if(empty($UserSubscription_plan['expiry_at'])){
                $UserSubscription_plan['expiry_at'] = '';
            }
            $explode_expiry_at = explode(" ", $UserSubscription_plan['expiry_at']);

            $processReaction['data']['plan_deatail'] = "";
            if (!empty($fetchPlan['data'])) {
                $processReaction['data']['plan_deatail'] = array('title' => $fetchPlan['data']['planData']['title'], 'price' => $fetchPlan['data']['planData']['price'] , 'plan_type' => $fetchPlan['data']['planData']['plan_type'],'status' => $fetchPlan['data']['planData']['status'],'created_at' => $UserSubscription_plan['created_at']->toDateString(),'expiry_at' => $explode_expiry_at[0],'renewal_sts' => $UserSubscription_plan['renewal_sts']);
            }else{
                $processReaction['data']['plan_deatail'] = "";
            }

            $processReaction['data']['block_user_collection'] = "";
            if (count($blockUserCollection) != 0) {
                $processReaction['data']['block_user_collection'] = $blockUser_array;
            }

            // check if record does not exists
            if ($processReaction['reaction_code'] == 18) {
                return redirect()->route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]);
            }
            $processReaction['data']['is_profile_page'] = true;
            $processReaction['data']['active_tab'] = 6;

            return back();

            //return $this->redirectTo('user.edit.profile_view',$processReaction['data']);
            //return $this->processResponse($processReaction, [], [], true);
    }

    public function deletePortfolioImage($username,CommonUnsecuredPostRequest $request)
    {
           
            $image_name = $request->input('image_name');

            $processReaction = $this->userSettingEngine->processDeletePortfolioImage($image_name, 'portfolio');

            $processReaction = $this->userEngine->prepareUserProfile($username);
            $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
            $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

            $blockUser_array = array();
            foreach ($blockUserCollection as $key => $value) {
                $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
            }
            $PlanByType = $this->userEngine->fetchPlanByType();

            
            if (empty($UserSubscription_plan)) {
                $fetchPlan['data'] = "";
            }else{
                $fetchPlan = $this->userEngine->fetchPlan($UserSubscription_plan['plan_id']);
            }
              
            $processReaction['data']['planbytype'] = "";
            if (!empty($PlanByType['data'])) {
                $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
            }
            if(!empty($UserSubscription_plan['expiry_at'])){
                $UserSubscription__plan_expiry = $UserSubscription_plan['expiry_at'];
            } else {
                $UserSubscription__plan_expiry = '';
            }
            $explode_expiry_at = explode(" ", $UserSubscription__plan_expiry);

            $processReaction['data']['plan_deatail'] = "";
            if (!empty($fetchPlan['data'])) {
                $processReaction['data']['plan_deatail'] = array('title' => $fetchPlan['data']['planData']['title'], 'price' => $fetchPlan['data']['planData']['price'] , 'plan_type' => $fetchPlan['data']['planData']['plan_type'],'status' => $fetchPlan['data']['planData']['status'],'created_at' => $UserSubscription_plan['created_at']->toDateString(),'expiry_at' => $explode_expiry_at[0],'renewal_sts' => $UserSubscription_plan['renewal_sts']);
            }else{
                $processReaction['data']['plan_deatail'] = "";
            }

            $processReaction['data']['block_user_collection'] = "";
            if (count($blockUserCollection) != 0) {
                $processReaction['data']['block_user_collection'] = $blockUser_array;
            }

            // check if record does not exists
            if ($processReaction['reaction_code'] == 18) {
                return redirect()->route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]);
            }
            $processReaction['data']['is_profile_page'] = true;
            $processReaction['data']['active_tab'] = 6;
            return back();
            
    }

    /**
     * Process upload cover image.
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadCoverImage(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userSettingEngine->processUploadCoverImage($request->all(), 'cover_image');

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Process user profile settings
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserProfileSetting(UserProfileSettingAddRequest $request)
    {
        $processReaction = $this->userSettingEngine->processStoreUserProfileSetting($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Show user photos view.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function getUserPhotosSetting()
    {
        $processReaction = $this->userSettingEngine->prepareUserPhotosSettings();

        return $this->loadPublicView('user.settings.photos', $processReaction['data']);
    }

    /**
     * Upload multiple photos
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    /*10-01-2022*/
    public function uploadPhotos(CommonUnsecuredPostRequest $request,$userid)
    {
            
        $processReaction = $this->userSettingEngine->processUploadPhotos($request->all(),$userid);

        return $this->processResponse($processReaction, [], [], true);
    }
    public function uploadPhotosfront(CommonUnsecuredPostRequest $request)
    {
            
            
        $processReaction = $this->userSettingEngine->processUploadPhotosFront($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }
    public function uploadVideo(CommonUnsecuredPostRequest $request)
    {  
        $processReaction = $this->userSettingEngine->processUploadVideo($request);
        return $processReaction;
    }

    public function deleteVideo($video_id)
    {
        $processReaction = $this->userSettingEngine->processDeleteUserVideo($video_id);
        return back();
    }

    /**
     * Upload multiple photos
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function deleteUserPhotos($photoUid)
    {   
        $Uid = $_GET['userId'];
           
        $processReaction = $this->userSettingEngine->processDeleteUserPhotos($photoUid,$Uid);

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Search Cities
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function searchStaticCities(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userSettingEngine->searchStaticCities($request->get('search_query'));

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process store user city
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processStoreCity(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userSettingEngine->processStoreCity($request->get('selected_city_id'));

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
