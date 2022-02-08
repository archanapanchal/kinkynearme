<?php
/**
* UserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use Illuminate\Http\Request;
use App\Yantrana\Components\User\Requests\{
    UserSignUpRequest,
    UserLoginRequest,
    UserUpdatePasswordRequest,
    UserForgotPasswordRequest,
    UserResetPasswordRequest,
    UserChangeEmailRequest,
    SendUserGiftRequest,
    ReportUserRequest,
    UserContactRequest
};

use Illuminate\Support\Facades\Redirect;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\User\UserEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Plans\ManagePlansEngine;
use Auth;
use App\Yantrana\Components\User\Models\{
    User,UserSubscription
};
use App\Yantrana\Components\User\ManageUserEngine;

class UserController extends BaseController
{
    /**
     * @var UserEngine - User Engine
     */
    protected $userEngine;
    protected $userRepository;

    /**
     * @var  ManagePlansEngine $managePlanEngine - Manage Plans Engine
     */
    protected $managePlanEngine;
    protected $manageUserEngine;

    /**
     * Constructor.
     *
     * @param UserEngine $userEngine - User Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageUserEngine $manageUserEngine,UserEngine $userEngine, ManagePlansEngine $managePlanEngine,UserRepository $userRepository)
    {
        $this->userEngine = $userEngine;
        $this->managePlanEngine         = $managePlanEngine;
        $this->userRepository         = $userRepository;
        $this->manageUserEngine = $manageUserEngine;
    }

    /**
     * Show login view.
     *---------------------------------------------------------------- */
    public function login()
    {
        return $this->loadView('user.login');
    }

    /**
     * Authenticate user based on post form data.
     *
     * @param object UserLoginRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function loginProcess(UserLoginRequest $request)
    {
        $processReaction = $this->userEngine->processLogin($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.profile_view', ['username' => getUserAuthInfo('profile.username')])
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Perform user logout action.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function logout()
    {
        if (isAdmin()) {
            $route = 'admin.user.login';
        } else {
            $route = 'user.login';
        }

        $processReaction = $this->userEngine->processLogout();
        
        return redirect()->route($route);
    }

    /**
     * Show Sign Up View.
     *
     *-----------------------------------------------------------------------*/
    public function signUp()
    {
        return $this->loadView('user.sign-up', [
            'genders' => configItem('user_settings.gender')
        ]);
    }

    /**
     * User Sign Process.
     *
     * @param object UserSignUpRequest $request
     * 
     *-----------------------------------------------------------------------*/
    public function signUpProcess(UserSignUpRequest $request)
    {
        $processReaction = $this->userEngine->userSignUpProcess($request->all());
        //check reaction code is 1 then redirect to login page
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.profile.build')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show Change Password View.
     *
     *-----------------------------------------------------------------------*/
    public function changePasswordView()
    {
        $user = Auth::user();
        $data = [];
        if ($user->password == 'NO_PASSWORD') {
            $data = [
                'userPassword' => $user->password
            ];
        }
        return $this->loadPublicView('user.change-password', $data);
    }

    /**
     * Handle change password request.
     *
     * @param object UserUpdatePasswordRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processChangePassword(UserUpdatePasswordRequest $request)
    {
        $processReaction = $this->userEngine
            ->processUpdatePassword(
                $request->only(
                    'new_password',
                    'current_password'
                )
            );

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Show Change Email View.
     *
     *-----------------------------------------------------------------------*/
    public function changeEmailView()
    {
        $user = Auth::user();
        $data = [
            'userEmail' => $user->email
        ];
        return $this->loadPublicView('user.change-email', $data);
    }

    /**
     * Handle change email request.
     *
     * @param object UserChangeEmailRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processChangeEmail(UserChangeEmailRequest $request)
    {
        $processReaction = $this->userEngine
            ->processChangeEmail(
                $request->only(
                    'new_email',
                    'current_password'
                )
            );

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Show Forgot Password View.
     *
     *-----------------------------------------------------------------------*/
    public function forgotPasswordView()
    {
        return $this->loadView('user.forgot-password');
    }

    /**
     * Handle user forgot password request.
     *
     * @param object UserForgotPasswordRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processForgotPassword(UserForgotPasswordRequest $request)
    {
        $processReaction = $this->userEngine
            ->sendPasswordReminder(
                $request->input('email')
            );

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->replaceView('user.forgot-password-success', [], '.lw-success-message')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * User Sign Process.
     *
     *-----------------------------------------------------------------------*/
    public function accountActivation(Request $request, $userUid)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $processReaction = $this->userEngine->processAccountActivation($userUid);

        // Check if account activation process succeed
        if ($processReaction['reaction_code'] === 1) {
            return redirect()->route('user.login')
                ->with([
                    'success' => 'true',
                    'message' => __tr('Your account has been activated successfully. Login with your email ID and password.'),
                ]);
        }

        // if activation process failed then
        return redirect()->route('user.login')
            ->with([
                'error' => 'true',
                'message' => __tr('Account Activation link invalid.'),
            ]);
    }

    /**
     * User Sign Process.
     *
     *-----------------------------------------------------------------------*/
    public function newEmailActivation(Request $request, $userUid, $newEmail)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $processReaction = $this->userEngine->processNewEmailActivation($userUid, $newEmail);

        // Check if account activation process succeed
        if ($processReaction['reaction_code'] === 1) {
            return redirect()->route('user.new_email.read.success')->with([
                'success' => true,
                'message' => __tr('Your new email activated successfully.'),
            ]);
        }
        // if activation process failed then
        return redirect()->route('user.new_email.read.success')->with([
            'success' => false,
            'message' => __tr('Email not updated.'),
        ]);
    }

    /**
     * User Sign Process.
     *
     *-----------------------------------------------------------------------*/
    public function updateEmailSuccessView()
    {
        return $this->loadPublicView('user.change-email-success');
    }

    /**
     * Show Reset Password View.
     *
     *-----------------------------------------------------------------------*/
    public function restPasswordView()
    {
        return $this->loadManageView('user.reset-password');
    }

    /**
     * Handle reset password request.
     *
     * @param object UserResetPasswordRequest $request
     * @param string                          $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processRestPassword(
        UserResetPasswordRequest $request,
        $reminderToken
    ) {
        $processReaction = $this->userEngine
            ->processResetPassword(
                $request->all(),
                $reminderToken
            );

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.login')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }


    /*cancel Subscription Plan Date*/
    public function settinig_profile_insert_update(CommonUnsecuredPostRequest $request){
        $request_data = $request->all();
        $user = $this->userRepository->fetch($request_data['userUId']);

        $u_id = $user['_id'];

        $Update_profile_setting = $this->userEngine->chnageProfileStatus($request_data,$u_id);

        return redirect()->route('user.profile_view',['username' => getUserAuthInfo('profile.username')])->with([
                    'successStatus'   => true,
                    'message' => $Update_profile_setting['message'],
                ]);
                
    }




    /*cancel Subscription Plan Date*/
    public function cancelUserCurrentPlan(CommonUnsecuredPostRequest $request){
        $request_data = $request->all();
        $user = $this->userRepository->fetch($request_data['userUId']);
        $u_id = $user['_id'];
        $Update_Subscription = $this->userEngine->updateSubscriptionPlanStatus($u_id);
        $Update_payment = $this->userEngine->updatePaymentPlanStatus($u_id);
        $processReaction = $this->userEngine->processLogout();
        //$this->redirectTo('user.login');

        return redirect()->route('user.login',['username' => getUserAuthInfo('profile.username')])->with([
                    'successStatus'   => true,
                    'message' => "login again",
                ]);
    }

    public function processUserSoftDelete($username){
            $UserProfiledata =  User::select('_uid')->where('username', $username)->first();
            $processReaction = $this->manageUserEngine->processSoftDeleteUser($UserProfiledata->_uid);
            return redirect()->route('user.login',['username' => getUserAuthInfo('profile.username')])->with([
                    'successStatus'   => true,
                    'message' => "login again",
                ]);
    }

   /*update Subscription Plan Date*/
   public function renewUserCurrentPlan(CommonUnsecuredPostRequest $request){

            $request_data = $request->all();

            if (!empty($request_data)) {
                    $user_current_plan = $request_data['plan_type'];
                    $renew_subscription = $request_data['renew_subscription'];

                    $user = $this->userRepository->fetch($request_data['userUId']);

                    $subscribe_plan_started = date("Y-m-d h:i:s");
                    $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +3 months"));
                    if ($user_current_plan == 1) {
                        $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +1 months"));
                    }

                    $u_id = $user['_id'];
                    $update_array_plan_date = array('subscribe_plan_started' => $subscribe_plan_started,'subscribe_plan_expiry' => $subscribe_plan_expiry );

                 


                    $Update_Subscription = $this->userEngine->updateSubscriptionPlan($u_id,$user_current_plan,$update_array_plan_date,$renew_subscription);


                    return redirect()->route('user.profile_view',['username' => getUserAuthInfo('profile.username')])->with([
                    'successStatus'   => true,
                    'message' => $Update_Subscription['message'],
                ]);
            }
                
                
            


            return redirect()->route('user.profile_view',['username' => getUserAuthInfo('profile.username')])->with([
            'successStatus'   => true
        ]);
   }



   public function editUserProfile($userName){

        $processReaction = $this->userEngine->prepareUserProfile($userName);
        $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
        $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

        $blockUser_array = array();
        foreach ($blockUserCollection as $key => $value) {
            $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
        }
        $PlanByType = $this->userEngine->fetchPlanByType();

        
            //echo "<pre>efea";print_r($UserSubscription_plan);exit();

        if (empty($UserSubscription_plan)) {
            $fetchPlan['data'] = "";
            $explode_expiry_at[0] = "";
        }else{
            $fetchPlan = $this->userEngine->fetchPlan($UserSubscription_plan['plan_id']);
            $explode_expiry_at = explode(" ", $UserSubscription_plan['expiry_at']);
        }

        $processReaction['data']['planbytype'] = "";
        if (!empty($PlanByType['data'])) {
            $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
        }



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

        return $this->loadProfileView('user.edit_profile',$processReaction['data']);
   }

   public function updateUserProfile(Request $request){

        $UserProfiledata =  User::where('_id',getUserID())->get()->toArray();
        $userName  =  $UserProfiledata[0]['username'];

        $update_profile_data = $this->userEngine->updateUserProfileDetail($request->all());

        $processReaction = $this->userEngine->prepareUserProfile($userName);
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

        return $this->loadProfileView('user.profile', $processReaction['data']);


   }



    /**
     * Get User profile view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getUserProfile($userName,Request $request)
    {   
        $search = $request['search'] ??"";
        $search_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
        } 

    
        $processReaction = $this->userEngine->prepareUserProfile($userName);
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
            
       //echo "<pre>";print_r($processReaction);exit();
        $processReaction['data']['planbytype'] = "";
        if (!empty($PlanByType['data'])) {
            $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
        }

        /*$session_nosubsciption_plan = session()->get('nosubsciption_plan');

         $user_restriction = 1;
        if ($session_nosubsciption_plan == '0') {
            $user_restriction = 0;
        }*/

        $explode_expiry_at = "0000-00-00 00:00:00";
        if (!empty($UserSubscription_plan)) {
        $explode_expiry_at = explode(" ", $UserSubscription_plan['expiry_at']);
        }


        $processReaction['data']['plan_deatail'] = "";
        if (!empty($fetchPlan['data'])) {
            $processReaction['data']['plan_deatail'] = array('title' => $fetchPlan['data']['planData']['title'], 'price' => $fetchPlan['data']['planData']['price'] , 'plan_type' => $fetchPlan['data']['planData']['plan_type'],'status' => $fetchPlan['data']['planData']['status'],'created_at' => $UserSubscription_plan['created_at']->toDateString(),'expiry_at' => $explode_expiry_at[0],'renewal_sts' => $UserSubscription_plan['renewal_sts']);
        }else{
            $processReaction['data']['plan_deatail'] = "";
        }


            //echo "<pre>";print_r($processReaction['data']['plan_deatail']);exit();

        $processReaction['data']['block_user_collection'] = "";
        if (count($blockUserCollection) != 0) {
            $processReaction['data']['block_user_collection'] = $blockUser_array;
        }

        // check if record does not exists
        if($processReaction['reaction_code'] == 18) {
            return redirect()->route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]);
        }

            //echo "<pre>";print_r($processReaction['data']);exit();
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['is_profile_page'] = true;
        $processReaction['data']['active_tab'] = 6;
        return $this->loadProfileView('user.profile', $processReaction['data']);
    }

    /**
     * Get User profile view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getUserProfileData($userName)
    {
        $processReaction = $this->userEngine->prepareUserProfile($userName);

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle user like dislike request.
     *
     * @param object UserResetPasswordRequest $request
     * @param string                          $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userLikeDislike($toUserUid, $like)
    {
        $processReaction = $this->userEngine->processUserLikeDislike($toUserUid, $like);

        //check reaction code equal to 1
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Handle user favourite request.
     *
     * @param object UserResetPasswordRequest $request
     * @param string                          $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userFavourite($toUserUid, $favourite)
    {

        $processReaction = $this->userEngine->processUserFavourite($toUserUid, $favourite);

        //check reaction code equal to 1
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Get User my like view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getMyLikeView()
    {
        //get page requested
        $page = request()->input('page');
        //get liked people data by parameter like '1'
        $processReaction = $this->userEngine->prepareUserLikeDislikedData(1);

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        $processReaction['data']['active_tab'] = 2;

        //load default view
        return $this->loadProfileView('user.my-liked', $processReaction['data']);
    }

    /**
     * Get User my Disliked view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getMyDislikedView()
    {
        //get page requested
        $page = request()->input('page');
        //get liked people data by parameter like '1'
        $processReaction = $this->userEngine->prepareUserLikeDislikedData(0);

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        //load default view
        return $this->loadPublicView('user.my-disliked', $processReaction['data']);
    }

    /**
     * Get User my Favourite view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getMyFavouriteView()
    {   

            
        //get page requested
        $page = request()->input('page');
        //get favourite people data by parameter like '1'
        $processReaction = $this->userEngine->prepareUserFavouriteData(1);

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

        $processReaction['data']['active_tab'] = 3;

        //load default view
        return $this->loadProfileView('user.my-favourite', $processReaction['data']);
    }

    /**
     * Get User my Disliked view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getWhoLikedMeView()
    {
        //get page requested
        $page = request()->input('page');
        //get liked people data by parameter like '1'
        $processReaction = $this->userEngine->prepareUserLikeMeData();

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        //load default view
        return $this->loadPublicView('user.who-liked-me', $processReaction['data']);
    }

    /**
     * Get mutual like view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getMutualLikeView()
    {
        //get page requested
        $page = request()->input('page');
        //get mutual like data
        $processReaction = $this->userEngine->prepareMutualLikeData();
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

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        $processReaction['data']['active_tab'] = 2;
        //load default view
        return $this->loadProfileView('user.mutual-like', $processReaction['data']);
    }

    /**
     * Get profile visitors view.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getProfileVisitorView()
    {
        //get page requested
        $page = request()->input('page');
        //get liked people data by parameter like '1'
        $processReaction = $this->userEngine->prepareProfileVisitorsData();

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        //load default view
        return $this->loadPublicView('user.profile-visitor', $processReaction['data']);
    }

    /**
     * Handle send user gift request.
     *
     * @param object SendUserGiftRequest $request
     * @param string $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userSendGift(SendUserGiftRequest $request, $sendUserUId)
    {
        $processReaction = $this->userEngine->processUserSendGift($request->all(), $sendUserUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Handle report user request.
     *
     * @param object ReportUserRequest $request
     * @param string $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function reportUser(ReportUserRequest $request, $sendUserUId)
    {
        $processReaction = $this->userEngine->processReportUser($request->all(), $sendUserUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Handle report user request.
     *
     * @param object blockUser $request
     * @param string $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function blockUser(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userEngine->processBlockUser($request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Get block user view and user list.
     *
     * @param string $userName
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function blockUserList()
    {
        //get page requested
        $page = request()->input('page');
        //get profile visitors data
        $processReaction = $this->userEngine->prepareBlockUserData();

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.blocked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        return $this->loadPublicView('user.block-user.list', $processReaction['data']);
    }

    /**
     * Handle report user request.
     *
     * @param object blockUser $userUid
     * @param string $reminderToken
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUnblockUser($userUid)
    {

        $processReaction = $this->userEngine->processUnblockUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    public function unblockUserFromProfile(CommonUnsecuredPostRequest $request)
    {   
        
        $userUid = $request->input('userUid');
        $processReaction = $this->userEngine->processUnblockUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * process Boost Profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processBoostProfile()
    {
        $processReaction = $this->userEngine->processBoostProfile();

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * process Boost Profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function loadProfileUpdateWizard()
    {
        $processReaction = $this->userEngine->checkProfileStatus();

        return $this->loadView('user.profile.update-wizard', $processReaction['data']);
    }

    /**
     * process Boost Profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function checkProfileUpdateWizard()
    {
        $processReaction = $this->userEngine->checkProfileStatus();

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * process Boost Profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function finishWizard()
    {
        $processReaction = $this->userEngine->finishWizard();

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * User Contact View.
     *
     *-----------------------------------------------------------------------*/
    public function getContactView()
    {
        $user = Auth::user();
        $contactData = [];
        //check is not empty
        if ($user) {
            $contactData = [
                'userFullName' => $user->first_name . ' ' . $user->last_name,
                'contactEmail' => $user->email
            ];
        }
        return $this->loadView('user.contact', $contactData);
    }

    /**
     * Handle process contact request.
     *
     * @param object UserContactRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function contactProcess(UserContactRequest $request)
    {
        $processReaction = $this->userEngine->processContact($request->all());
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * get booster price and period
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function getBoosterInfo()
    {
        $processReaction = $this->userEngine->getBoosterInfo();
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Handle process contact request.
     *
     * @param object CommonUnsecuredPostRequest $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function deleteAccount(CommonUnsecuredPostRequest $request)
    {
        $processReaction = $this->userEngine->processDeleteAccount($request->all());

        if ($processReaction['reaction_code'] == 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.login')
            );
        }

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * process Build Profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function prepareProfileBuild()
    {
        $processReaction = $this->userEngine->prepareBuildProfile();

        // check if record does not exists
        if ($processReaction['reaction_code'] == 18) {
            return redirect()->route('landing_page');
        }

        return $this->loadView('user.profile.build', $processReaction['data']);
    }

    /**
     * Profile Build Process.
     *
     * @param object UserSignUpRequest $request
     * 
     *-----------------------------------------------------------------------*/
    public function processProfileBuild(Request $request)
    {
        $processReaction = $this->userEngine->processProfileBuild($request->all());

        // check if record does not exists
        if ($processReaction['reaction_code'] == 18) {
            return redirect()->route('landing_page');
        }

        //check reaction code is 1 then redirect to login page
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                //$this->redirectTo('landing_page')
                $this->redirectTo('user.subscription.plans')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Display Subscription Plans.
     *
     *-----------------------------------------------------------------------*/
    public function prepareSubscriptionPlans()
    {
        $processReaction = $this->userEngine->fetchPlanByType();

        if ($processReaction['reaction_code'] == 18) {
            return redirect()->route('landing_page');
        }

        return $this->loadView('user.subscription.plans', $processReaction['data']);
    }

    /**
     * Display Subscription Plans.
     *
     *-----------------------------------------------------------------------*/
    public function prepareSubscriptionPlan($planId)
    {
        $processReaction = $this->userEngine->fetchPlan($planId);

        if ($processReaction['reaction_code'] == 18) {
            return redirect()->route('landing_page');
        } else if ($processReaction['reaction_code'] == 19) {
            return redirect()->route('user.subscription.plans');
        }

        return $this->loadView('user.subscription.plan', $processReaction['data']);
    }

    /**
     * Process Subscription Plans.
     *
     *-----------------------------------------------------------------------*/
    public function processSubscriptionPlan($planId)
    {
        $processReaction = $this->userEngine->processPlan($planId);

        if ($processReaction['reaction_code'] == 18) {
            return redirect()->route('landing_page');
        } else if ($processReaction['reaction_code'] == 19) {
            return redirect()->route('user.subscription.plans');
        }

        return redirect()->route('user.login')->with([
            'successStatus'   => true,
            'message' => $processReaction['message'],
        ]);
    }


    /**
     * View All Conversation
     *
     * @return  void
     *-----------------------------------------------------------------------*/
    public function messages()
    {
        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        $processReaction['data']['active_tab'] = 4;
        return $this->loadProfileView('user.messages', $processReaction['data']);
    }
}
