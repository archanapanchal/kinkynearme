<?php
/**
* UserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
use App\Yantrana\Components\ForumTopics\ManageForumTopicsEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Plans\ManagePlansEngine;
use Auth;
use App\Yantrana\Components\User\Models\{
    User,UserSubscription
};
use App\Yantrana\Components\User\ManageUserEngine;
use App\Yantrana\Components\UserSetting\Models\UserSpecificationModel;
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
    protected $manageForumTopicsEngine;

    /**
     * Constructor.
     *
     * @param UserEngine $userEngine - User Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageUserEngine $manageUserEngine,UserEngine $userEngine, ManagePlansEngine $managePlanEngine,UserRepository $userRepository, ManageForumTopicsEngine $manageForumTopicsEngine)
    {
        ini_set('memory_limit', '-1');
        $this->userEngine = $userEngine;
        $this->managePlanEngine         = $managePlanEngine;
        $this->userRepository         = $userRepository;
        $this->manageUserEngine = $manageUserEngine;
        $this->manageForumTopicsEngine = $manageForumTopicsEngine;
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
        //echo "<pre>"; print_r($request->all()); exit;
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
          /*  return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('user.login')
            );*/
            return redirect()->route('user.login');
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


    public function createCookieFlag(CommonUnsecuredPostRequest $request){
        $request_data = $request->all();
        $Update_Subscription = $this->userEngine->manageCookieFlag($request_data);
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



   public function editUserProfile($userName,Request $request)
   {
         /*search*/
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }/*end search*/

        $processReaction = $this->userEngine->prepareUserProfile($userName);
        $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
        $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

        $blockUser_array = array();
        foreach ($blockUserCollection as $key => $value) {
            $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
        }
        $PlanByType = $this->userEngine->fetchPlanByType();
         /*search*/
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['advance_search'] = 0;
        /*end search*/
        
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

        //echo "<pre>";print_r($processReaction['data']);exit();


        return $this->loadProfileView('user.edit_profile',$processReaction['data']);
   }

   public function updateUserProfile(Request $request){

    //echo "<pre>"; print_r($request->all()); exit;
    $id = getUserID();

    $this->validate($request, [
          'fname' => 'required',
          'lname' => 'required',
          'username' => 'required|unique:users,username,'.$id.',_id',
          'email' => 'required|unique:users,email,'.$id.',_id',
          'dob' => 'required',
          'gender' => 'required',
          'ethnicity' => 'required',
          /*'relationships' => 'required',
          'looking_for' => 'required',
          'smoke' => 'required',
          'drink' => 'required',
          'married' => 'required',
          'body_type' => 'required',
          'height' => 'required',
          'hair_color' => 'required',
          'eye_color' => 'required'*/
       ]);


        $UserProfiledata =  User::where('_id',getUserID())->get()->toArray();

        $userName  =  $UserProfiledata[0]['username'];

        $update_profile_data = $this->userEngine->updateUserProfileDetail($request->all());

        $processReaction = $this->userEngine->prepareUserProfile($userName);
        $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
        $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);
        /*echo "test";
        echo "<pre>"; print_r($UserSubscription_plan); exit;*/
        $blockUser_array = array();
        foreach ($blockUserCollection as $key => $value) {
            $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
        }
        $PlanByType = $this->userEngine->fetchPlanByType();

        
        if (empty($UserSubscription_plan)) {
            $fetchPlan['data'] = "";
            $explode_expiry_at = "";
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
        $processReaction['data']['active_tab'] = 7;

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
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }

        $search_data_forum = "";
        $search_5_data_forum = "";

        if($search != ""){
            $search_data_forum = $this->manageForumTopicsEngine->searchRequestFromHomeForForum($search);
            $search_5_data_forum = array_slice($search_data_forum, 0,5);; 
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
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['search_data_forum'] = $search_data_forum;
        $processReaction['data']['search_5_data_forum'] = $search_5_data_forum;
        $processReaction['data']['is_profile_page'] = true;
        $processReaction['data']['advance_search'] = 0;
        $processReaction['data']['active_tab'] = 8;
        return $this->loadProfileView('user.profile', $processReaction['data']);
    }


    public function viewAllResultSearch(Request $request)
    {
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
        }

          //echo "<pre>";print_r($search_data);exit();

        if(!empty($search_data)){
            $countUser = 0;
            if(isset($search_data['search_data']['data'])){
                $countUser = 0;
            }else{
                $countUser = count($search_data);
            }
        }

    
        $processReaction['data']['active_tab'] = 1;
        $processReaction['data']['advance_search'] = 0;
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['totalCount'] = $countUser;
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

        return $this->loadProfileView('filter.view-all-results', $processReaction['data']);
        
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
    public function getMyFavouriteView(CommonUnsecuredPostRequest $request)
    {   
        /*search*/
            $search = $request['search'] ??"";
            $search_data = "";
            $search_5_data = "";
            if ($search != "" ) {
                $search_data = $this->userEngine->searchRequestFromHome($search);
                $search_5_data =array_slice($search_data, 0,5);
            }
        /*end search*/
            
        //get page requested
        $page = request()->input('page');
        //get favourite people data by parameter like '1'
        $processReaction = $this->userEngine->prepareUserFavouriteData(1);


        if (isset($processReaction['data']['usersData'])) {

            $usersData = $processReaction['data']['usersData'];

            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();

            function checkFavoriteUser($userid,$current_user_favourite){

                    if (!empty($current_user_favourite['data'])) {
                        $favourite_id_array =array();
                        foreach ($current_user_favourite['data']['usersData'] as $key => $value) {
                            $favourite_id_array[] = $value['user_id'];
                        }

                        if (in_array($userid,$favourite_id_array)) {
                           return $favourite_sts = 1; 
                        } else {
                           return $favourite_sts = 0;
                        }
                    }
                    
            }
            function checkLikeUser($userid,$prepare_like_data){

                    if (!empty($prepare_like_data['data'])) {
                        $like_id_array =array();
                        foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                            $like_id_array[] = $value['userId'];
                        }

                            //echo "<pre>";print_r($like_id_array);exit();

                        if (in_array($userid,$like_id_array)) {
                          return $like_sts = 1; 
                        } else {
                          return $like_sts = 0;
                        }
                    }
            }

            foreach ($usersData as $key => $user) {


                $favourite_sts =  checkFavoriteUser($user['user_id'],$current_user_favourite);
                $like_sts =  checkLikeUser($user['user_id'],$prepare_like_data);

                $processReaction['data']['usersData'][$key]['favourite_sts'] = $favourite_sts;
                $processReaction['data']['usersData'][$key]['like_sts'] = $like_sts;

               
            }
        
        }    

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }
        /*search*/
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['advance_search'] = 0;
        /*end search*/
        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        $processReaction['data']['active_tab'] = 5;

       
        //echo "<pre>"; print_r($processReaction['data']); exit;
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
    public function getMutualLikeView(CommonUnsecuredPostRequest $request)
    {
        /*search*/
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }/*end search*/

        //get page requested
        $page = request()->input('page');
        //get mutual like data
        $processReaction = $this->userEngine->prepareMutualLikeData();

        if (isset($processReaction['data']['usersData'])) {
            $usersData = $processReaction['data']['usersData'];

            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();

            function checkFavoriteUser($userid,$current_user_favourite){

                    if (!empty($current_user_favourite['data'])) {
                        $favourite_id_array =array();
                        foreach ($current_user_favourite['data']['usersData'] as $key => $value) {

                            $favourite_id_array[] = $value['user_id'];
                        }

                        if (in_array($userid,$favourite_id_array)) {
                           return $favourite_sts = 1; 
                        } else {
                           return $favourite_sts = 0;
                        }
                    }
                    
            }
            function checkLikeUser($userid,$prepare_like_data){

                    if (!empty($prepare_like_data['data'])) {
                        $like__id_array =array();
                        foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                            $like_id_array[] = $value['userId'];
                        }
                        if (in_array($userid,$like_id_array)) {
                          return $like_sts = 1; 
                        } else {
                          return $like_sts = 0;
                        }
                    }
            }


            foreach ($usersData as $key => $user) {
                $favourite_sts =  checkFavoriteUser($user['userId'],$current_user_favourite);
                $like_sts =  checkLikeUser($user['userId'],$prepare_like_data);
                $processReaction['data']['usersData'][$key]['favourite_sts'] = $favourite_sts;
                $processReaction['data']['usersData'][$key]['like_sts'] = $like_sts;

            }

            
        }

        

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;
        /*search*/
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['advance_search'] = 0;
        /*end search*/
        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        $processReaction['data']['active_tab'] = 4;
        //load default view
      
        return $this->loadProfileView('user.mutual-like', $processReaction['data']);
    }

    public function getLikeView(CommonUnsecuredPostRequest $request)
    {
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }

        //get page requested
        $page = request()->input('page');
        //get mutual like data
        $processReaction = $this->userEngine->prepareLikeDataView();
        if (isset($processReaction['data']['usersData'])) {
            $usersData = $processReaction['data']['usersData'];

            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();
           
            function checkFavoriteUser($userid,$current_user_favourite){

                    if (!empty($current_user_favourite['data'])) {
                        $favourite_id_array =array();
                        foreach ($current_user_favourite['data']['usersData'] as $key => $value) {

                            $favourite_id_array[] = $value['user_id'];
                        }

                           // echo "<pre>favourite_id_array";print_r($favourite_id_array);

                        if (in_array($userid,$favourite_id_array)) {
                           return $favourite_sts = 1; 
                        } else {
                           return $favourite_sts = 0;
                        }
                    }
                    
            }
            function checkLikeUser($userid,$prepare_like_data){

                    if (!empty($prepare_like_data['data'])) {
                        $like__id_array =array();
                        foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                            $like_id_array[] = $value['userId'];
                        }

                         //echo "<pre>like_id_array";print_r($like_id_array);

                        if (in_array($userid,$like_id_array)) {
                          return $like_sts = 1; 
                        } else {
                          return $like_sts = 0;
                        }
                    }
            }


            foreach ($usersData as $key => $user) {

                $favourite_sts =  checkFavoriteUser($user['userId'],$current_user_favourite);
                $like_sts =  checkLikeUser($user['userId'],$prepare_like_data);
                $processReaction['data']['usersData'][$key]['favourite_sts'] = $favourite_sts;
                $processReaction['data']['usersData'][$key]['like_sts'] = $like_sts;

            }

            
        }

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['advance_search'] = 0;
       //echo "<pre>"; print_r($processReaction); exit;
        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        $processReaction['data']['active_tab'] = 2;
        //load default view

        return $this->loadProfileView('user.like', $processReaction['data']);
    }

    public function getLikeYouView()
    {

        //get page requested
        $page = request()->input('page');
        //get mutual like data
        $processReaction = $this->userEngine->prepareLikeYouDataView();
        if (isset($processReaction['data']['usersData'])) {
            $usersData = $processReaction['data']['usersData'];

            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();

            function checkFavoriteUser($userid,$current_user_favourite){

                    if (!empty($current_user_favourite['data'])) {
                        $favourite_id_array =array();
                        foreach ($current_user_favourite['data']['usersData'] as $key => $value) {
                         
                            $favourite_id_array[] = $value['user_id'];
                        }

                           // echo "<pre>favourite_id_array";print_r($favourite_id_array);
                         
                          
                        if (in_array($userid,$favourite_id_array)) {
                           return $favourite_sts = 1; 
                        } else {
                           return $favourite_sts = 0;
                        }
                    }
                    
            }
            function checkLikeUser($userid,$prepare_like_data){

                    if (!empty($prepare_like_data['data'])) {
                        $like__id_array =array();
                        foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                            $like_id_array[] = $value['userId'];
                        }

                         //echo "<pre>like_id_array";print_r($like_id_array);

                        if (in_array($userid,$like_id_array)) {
                          return $like_sts = 1; 
                        } else {
                          return $like_sts = 0;
                        }
                    }
            }
            foreach ($usersData as $key => $user) {
          
                $favourite_sts =  checkFavoriteUser($user['userId'],$current_user_favourite);
                $like_sts =  checkLikeUser($user['userId'],$prepare_like_data);
                $processReaction['data']['usersData'][$key]['favourite_sts'] = $favourite_sts;
                $processReaction['data']['usersData'][$key]['like_sts'] = $like_sts;

            }

            
        }
       /* echo "<pre>"; print_r($processReaction['data']); exit;*/
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

        $processReaction['data']['active_tab'] = 3;
        //load default view
        return $this->loadProfileView('user.likeyourprofile', $processReaction['data']);
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
    public function messages(CommonUnsecuredPostRequest $request)
    {
       /* $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }

        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;*/
        //get page requested
        /*search*/
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }/*end search*/
        $page = request()->input('page');
        //get mutual like data
        $processReaction = $this->userEngine->prepareMutualLikeData();

        if (isset($processReaction['data']['usersData'])) {
            $usersData = $processReaction['data']['usersData'];

            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();

            function checkFavoriteUser($userid,$current_user_favourite){

                    if (!empty($current_user_favourite['data'])) {
                        $favourite_id_array =array();
                        foreach ($current_user_favourite['data']['usersData'] as $key => $value) {

                            $favourite_id_array[] = $value['user_id'];
                        }

                        if (in_array($userid,$favourite_id_array)) {
                           return $favourite_sts = 1; 
                        } else {
                           return $favourite_sts = 0;
                        }
                    }
                    
            }
            function checkLikeUser($userid,$prepare_like_data){

                    if (!empty($prepare_like_data['data'])) {
                        $like__id_array =array();
                        foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                            $like_id_array[] = $value['userId'];
                        }
                        if (in_array($userid,$like_id_array)) {
                          return $like_sts = 1; 
                        } else {
                          return $like_sts = 0;
                        }
                    }
            }


            foreach ($usersData as $key => $user) {
                $favourite_sts =  checkFavoriteUser($user['userId'],$current_user_favourite);
                $like_sts =  checkLikeUser($user['userId'],$prepare_like_data);
                $processReaction['data']['usersData'][$key]['favourite_sts'] = $favourite_sts;
                $processReaction['data']['usersData'][$key]['like_sts'] = $like_sts;

            }

            
        }

        

        $user_subscription_detail = UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

        if (!empty($user_subscription_detail)) {
            $subscription_detail = "yes";
        } else {
            $subscription_detail = "no";
        }
        /*search*/
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        $processReaction['data']['advance_search'] = 0;
        /*end search*/
        $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;

        //check if page is not null and not equal to first page
        if (!is_null($page) and ($page != 1)) {

            $processReaction['data'] = view('user.partial-templates.my-liked-users', $processReaction['data'])
                ->render();

            return $processReaction;
        }

        $processReaction['data']['active_tab'] = 6;

        return $this->loadProfileView('user.messages', $processReaction['data']);
    }


    public function frind_profile_data(Request $request){
            $username = $request->input('username');
            $processReaction = $this->userEngine->prepareUserProfile($username);
            //echo "<pre>"; print_r($processReaction); exit;
            $current_user_favourite = $this->userEngine->prepareUserFavouriteData(1);
            $prepare_like_data = $this->userEngine->prepareLikeData();
            function checkFavoriteUser($userid,$current_user_favourite){
                if (!empty($current_user_favourite['data'])) {
                    $favourite_id_array =array();

                    foreach ($current_user_favourite['data']['usersData'] as $key => $value) {

                        $favourite_id_array[] = $value['user_id'];
                    } 
                    $new_arr = array_map(function($fav){
                        return (string) $fav;
                    }, $favourite_id_array);

                    // then use in array
                    if(in_array($userid, $new_arr)) {
                         return 1; 
                    } else {
                        return 0;
                    }
                   /* if (in_array($userid,$favourite_id_array)) {
                        return $favourite_sts = 1; 
                    } else {
                       return $favourite_sts = 0;
                    }*/
                }
            }
            /*like*/
            function checkLikeUser($userid,$prepare_like_data){
              
                if (!empty($prepare_like_data['data'])) {
                    $like_id_array =array();
                    foreach ($prepare_like_data['data']['usersData'] as $key => $value) {
                        $like_id_array[] = $value['userId'];
                    }

                    $new_arr = array_map(function($like){
                        return (string) $like;
                    }, $like_id_array);

                    // then use in array
                    if(in_array($userid, $new_arr)) {
                         return 1; 
                    } else {
                        return 0;
                    }
                    
                    /*if (in_array($userid,$like_id_array)) {
                      return $like_sts = 1; 
                    } else {
                      return $like_sts = 0;
                    }*/
                }
            }
            
            $usersData = $processReaction['data']['userData'];
            $favourite_sts =  checkFavoriteUser($usersData['userId'],$current_user_favourite);
            $like_sts =  checkLikeUser($usersData['userId'],$prepare_like_data);
            $processReaction['data']['userData']['favourite_sts'] = $favourite_sts;
            $processReaction['data']['userData']['like_sts'] = $like_sts;
          
            /*echo "<pre>";
            print_r($processReaction['data']);
            exit;*/
            if (isset($processReaction['data'])) {
                $userName = $processReaction['data']['userData']['userName'];
                $favourite_sts = $processReaction['data']['userData']['favourite_sts'];
                $like_sts = $processReaction['data']['userData']['like_sts'];
                $userUId = $processReaction['data']['userData']['userUId'];
                $userId = $processReaction['data']['userData']['userId'];
                
                $profilePicture = imageOrNoImageAvailable($processReaction['data']['userData']['profilePicture']);
                $userAge = $processReaction['data']['userData']['userAge'];
               if (isset($processReaction['data']['userOnlineStatus'])) {
                    $userOnlineStatus = $processReaction['data']['userOnlineStatus'];
                }else{
                    $userOnlineStatus = "";
                }

                if (isset($processReaction['data']['userProfileData']['country_name'])) {

                $country_name = $processReaction['data']['userProfileData']['country_name'];
                }else{
                    $country_name = "";
                }

                if (isset($processReaction['data']['userProfileData']['aboutMe'])) {
                $aboutMe = $processReaction['data']['userProfileData']['aboutMe'];
            
                } else {
                   $aboutMe ="";
                }

                if (isset($processReaction['data']['userSpecifications']['our_sexual_orientation'])) {
                    $our_sexual_orientation = $processReaction['data']['userSpecifications']['our_sexual_orientation'];            
                } else {
                    $our_sexual_orientation = "";
                }

                if (isset($processReaction['data']['userSpecifications']['looking_for'])) {
            
                $looking_for = $processReaction['data']['userSpecifications']['looking_for'];
                } else {
                    $looking_for = "";
                }
                
                if (isset($processReaction['data']['userSpecifications']['kinks'])) {            
                    $kinks = $processReaction['data']['userSpecifications']['kinks'];
                }else{
                    $kinks = "";
                }

                if (isset($processReaction['data']['userSpecifications']['body_type'])) {
                $body_type = $processReaction['data']['userSpecifications']['body_type'];            
                } else {
                    $body_type = "";
                }
                
                if (isset($processReaction['data']['userSpecifications']['hair_color'])) {
                $hair_color = $processReaction['data']['userSpecifications']['hair_color'];            
                } else {
                    $hair_color = "";
                }

                if (isset($processReaction['data']['userSpecifications']['ethnicity'])) {
                $ethnicity = $processReaction['data']['userSpecifications']['ethnicity'];            
                } else {
                    $ethnicity = "";
                }
                
                if (isset($processReaction['data']['userSpecifications']['height'])) {            
                $height = $processReaction['data']['userSpecifications']['height'];
                } else {
                    $height = "";
                }

                if ($processReaction['data']['userSpecifications']['eye_color']) {
                $eye_color = $processReaction['data']['userSpecifications']['eye_color'];            
                } else {
                   $eye_color = "";
                }
                
                if (isset($processReaction['data']['userSpecifications']['smoke'])) {
                $smoke = $processReaction['data']['userSpecifications']['smoke'];            
                } else {
                    $smoke = "";
                }
                
                if (isset($processReaction['data']['userSpecifications']['drink'])) {
                $drink = $processReaction['data']['userSpecifications']['drink'];            
                } else {
                   $drink = "";
                }
                
                
                $photosData = $processReaction['data']['photosData'];

                


                $response_layout = "<div class='my-profile-section'>
                        <div class='left-right-side-my-profile'>
                            <div class='left-side-my-profile'>
                                <img class='lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img' id='lwProfilePictureStaticImage' src=".$profilePicture.">
                            </div>
                            <div class='right-side-my-profile'>
                                <div class='profile-title-edit d-flex-class'>
                                    <div class='profile-title'>
                                        <h3>".$userName."</h3>";

                                        if($userOnlineStatus == 1){
                $response_layout .= "<span><img src=".url('dist/images/dots-thik.svg').">".__tr('Online')."</span>";

                                        }
                                        if($userOnlineStatus == 2){

                $response_layout .= "<span><img src=".url('dist/images/dots-thik.svg').">".__tr('Idle')."</span>";
                                        }
                                        if($userOnlineStatus == 3){

                $response_layout .= "<span><img src=".url('dist/images/dots-thik.svg').">".__tr('Offline')."</span>";
                                        }
                                        
                $response_layout .="</div>
                                </div>
                                <div class='address-social-icon d-flex-class'>
                                    <div class='address'>
                                        <p>".$userAge."<br>".$country_name."</p>
                                    </div>                                    
                                </div>";
                /*favorite and like icon*/
                $response_layout .= "<div class='profile-icon d-flex' id='user-".$userId ."'><div class='profile-icon-left-side d-flex'>";
                $response_layout .= "<div class='main-heart'><a href data-action='".route('user.write.like_dislike', ['toUserUid' => $userUId, 'like' => 1])."' data-method='post' data-callback='onLikeCallback' title='Like' class='icon lw-ajax-link-action lw-like-action-btn' id='lwLikeBtn'>";
                if ($like_sts == 1) { 
                    $response_layout .="<span class='heart-icon' style='display:block;'>
                                            <svg xmlns='http://www.w3.org/2000/svg
                                             width='22.002' height='19.503' viewBox='0 0 22.002 19.503'>
                                                <g id='Component_2_19' data-name='Component 2 – 19' transform='
                                                translate(1.001 1.003)'>
                                                    <path id='Path_280' data-name='Path 280' d='M18.059-15.055a5.342,5.342,0,0,0-7.289.531L10-13.73l-.77-.793a5.341,5.341,0,0,0-7.289-.531,5.609,5.609,0,0,0-.387,8.121L9.113.871a1.225,1.225,0,0,0,1.77,0l7.559-7.8A5.606,5.606,0,0,0,18.059-15.055Z' transform=
                                                    translate(0 16.251)' fill='#f06a6a' stroke='#f06a6a' stroke-width='2'/>
                                                </g>
                                            </svg>
                                        </span>";
                }else {
                    $response_layout .="<span class='heart-icon' style='display:block;'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='22.002' height='19.503' viewBox='0 0 22.002 19.503'>
                                                <g id='Component_2_19' data-name='Component 2 – 19' transform='translate(1.001 1.003)'>
                                                    <path id='Path_280' data-name='Path 280' d='M18.059-15.055a5.342,5.342,0,0,0-7.289.531L10-13.73l-.77-.793a5.341,5.341,0,0,0-7.289-.531,5.609,5.609,0,0,0-.387,8.121L9.113.871a1.225,1.225,0,0,0,1.77,0l7.559-7.8A5.606,5.606,0,0,0,18.059-15.055Z' transform='translate(0 16.251)' fill='rgba(240,106,106,0)' stroke='#f06a6a' stroke-width='2'/>
                                                </g>
                                            </svg>
                                        </span>";
                }
                $response_layout .="</a></div>";
                /*favortite*/
                $response_layout .="<div class='main-star' > <a href data-action='".route('user.write.favourite', ['toUserUid' => $userUId, 'favourite' => 1])."' data-method='post' data-callback='onFavouriteCallback' title='Favorite' class='icon lw-ajax-link-action lw-favourite-action-btn' id='lwFavouriteBtn'>";
                if ($favourite_sts == 1) {
                    $response_layout .="<span class='hover-show' style='display:block'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='22.936' height='22.029' viewBox='0 0 22.936 22.029'>
                                                <g id='Component_2_20' data-name='Component 2 – 20' transform='translate(1.019 1)'>
                                                    <path id='Path_282' data-name='Path 282' d='M10.129-16.8,7.578-11.633,1.871-10.8A1.251,1.251,0,0,0,1.18-8.668L5.309-4.645,4.332,1.039A1.249,1.249,0,0,0,6.145,2.355L11.25-.328l5.105,2.684a1.25,1.25,0,0,0,1.812-1.316l-.977-5.684L21.32-8.668a1.251,1.251,0,0,0-.691-2.133l-5.707-.832L12.371-16.8A1.251,1.251,0,0,0,10.129-16.8Z' transform='translate(-0.801 17.5)' fill='#ebe054' stroke='rgba(235,224,84,0)' stroke-width='2'/>
                                                </g>
                                            </svg>
                                        </span>";
                } else {
                    $response_layout .="<span class='star-icon' style='display:block'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='22.936' height=
                                            22.029' viewBox='0 0 22.936 22.029'>
                                                <g id='Component_2_20' data-name='Component 2 – 20' transform='translate(1.019 1)'>
                                                    <path id='Path_282' data-name='Path 282' d='M10.129-16.8,7.578-11.633,1.871-10.8A1.251,1.251,0,0,0,1.18-8.668L5.309-4.645,4.332,1.039A1.249,1.249,0,0,0,6.145,2.355L11.25-.328l5.105,2.684a1.25,1.25,0,0,0,1.812-1.316l-.977-5.684L21.32-8.668a1.251,1.251,0,0,0-.691-2.133l-5.707-.832L12.371-16.8A1.251,1.251,0,0,0,10.129-16.8Z' transform=
                                                    translate(-0.801 17.5)' fill='rgba(235,224,84,0)' stroke='#ebab54' stroke-width='2'/>
                                                </g>
                                            </svg>
                                        </span>";
                }
                $response_layout .="</a> </div>";
                $response_layout .= "</div></div>";
            $response_layout .="<div class='open-for-relation'>
                                            <div class='open-title-edit d-flex-class'>";
                if(!empty($our_sexual_orientation)){
                    $response_layout .="<h5>Sexual Orientation is ".$our_sexual_orientation."</h5>
                                                <a href='#' class='edit'><!-- Edit --></a>";
                } else {
                    $response_layout .="<h5>Open for relationships</h5>";
                }
                    $response_layout .="</div>
                                            <div class='open-desc'>
                                                <ul>
                                                    <li>
                                                        <span>Looking for</span>
                                                        <p>".ucwords(str_replace(array(",", "-"), array(", ", " "),$looking_for))."</p>
                                                    </li>
                                                    <li>
                                                        <span>Interests/Kinks</span>
                                                        <p>".ucwords(str_replace(array(",", "-"), array(", ", " "),$kinks))."</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class='physical-appearance'>
                                            <div class='open-desc d-flex-class'>
                                                <span>Physical Appearance</span>
                                                <span>Ethnicity</span>
                                            </div>
                                            <div class='open-desc'>
                                                <ul>
                                                    <li>
                                                        <img src=".url('dist/images/app-1.png').">
                                                        <p>".$body_type."</p>
                                                    </li>
                                                    <li>
                                                        <img src=".url('dist/images/app-2.png').">
                                                        <p>".$hair_color."</p>
                                                    </li>
                                                    <li>
                                                        <img src=".url('dist/images/app-3.png').">
                                                        <p>".$ethnicity."</p>
                                                    </li>
                                                    <li>
                                                        <img src=".url('dist/images/app-4.png').">
                                                        <p>".$height."</p>
                                                    </li>
                                                    <li>
                                                        <img src=".url('dist/images/app-5.png').">
                                                        <p>".$eye_color."</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class='physical-appearance life-style'>
                                            <div class='open-desc d-flex-class'>
                                                <span>Lifestyle</span>
                                            </div>
                                            <div class='open-desc'>
                                                <ul>
                                                    <li>
                                                        <img src=".url('dist/images/app-6.png').">
                                                        <p>".$smoke."</p>
                                                    </li>
                                                    <li>
                                                        <img src=".url('dist/images/app-7.png').">
                                                        <p>".$drink."</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>";
                    
            $response_layout .="</div>
                        </div>";

                       
                        if (isset($user['user_detail_data']['data']['userProfileData']['aboutMe'])) { 
                            
            $response_layout .="<div class='my-profile-about'>
                                <p class='about-title'>". __tr('About Me')."</p>
                                <div class='about-content'>".__ifIsset($aboutMe)."</div>
                            </div>";
                        
                         
                        }

                        
                                if(!__isEmpty($photosData)){
        $response_layout .="<div class='my-profile-images'>
                                <p class='about-title'>".__tr('More photos')."</p>
                                <div class='row'>";
                                    foreach($photosData as $key => $photo){
                                        $response_layout .="<div class='col-md-3'><img class='lw-user-photo lw-photoswipe-gallery-img lw-lazy-img' data-img-index=".$key." src=". imageOrNoImageAvailable($photo['image_url'])."></div>";
                                    }
                               $response_layout .="</div>
                            </div>";
                            }else{
                                $response_layout .="<p>".__tr('Ooops... No images found...')."</p>";
                            }
            $response_layout .="<div><br><br></div>";

                    return $response_layout .="</div>";
                
            }

    }
    public function advance_search(Request $request){

            $inputs = $request->all();

            $userUid= getUserID();
            $user = $this->userRepository->fetch($userUid);
            $userName = $user->username;
            $processReaction = $this->userEngine->prepareAdvanceSearchDetail($inputs);

                //echo "<pre>";print_r($processReaction);exit();
            

            if(!empty($processReaction)){
                $countUser = 0;
                if(isset($processReaction['search_data']['data'])){
                    $countUser = 0;
                }else{
                    $countUser = count($processReaction);
                }
            }

        
            $processReaction['data']['search_data'] = $processReaction;
            $processReaction['data']['totalCount'] = $countUser;            
            $processReaction['data']['active_tab'] = 1;
            $processReaction['data']['advance_search'] = 1;
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

            $processReaction['data']['userProfileData']['userSpecificationData'] = "";
            $processReaction['data']['userProfileData']['subscription_detail'] = $subscription_detail;
            
            

                //echo "<pre>";print_r($processReaction['data']);exit();
            

               
            return $this->loadProfileView('filter.view-all-results', $processReaction['data']);
    }
}
