<?php
/**
* UserEngine.php - Main component file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use Auth;
use Cookie;
use Hash;
use YesSecurity;
use Session;
use PushBroadcast;
use Carbon\Carbon;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Base\BaseMailer;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\User\Repositories\CreditWalletRepository;
use App\Yantrana\Components\User\Repositories\UserEncounterRepository;
use App\Yantrana\Components\UserSetting\Repositories\UserSettingRepository;
use App\Yantrana\Components\Item\Repositories\ManageItemRepository;
use App\Yantrana\Components\AbuseReport\Repositories\ManageAbuseReportRepository;
use App\Yantrana\Support\Country\Repositories\CountryRepository;
use App\Yantrana\Components\Plans\Repositories\ManagePlansRepository;
use App\Yantrana\Components\Messenger\Repositories\MessengerRepository;
use App\Yantrana\Support\CommonTrait;
use \Illuminate\Support\Facades\URL;
use App\Yantrana\Components\User\Models\UserSubscription;
use App\Yantrana\Components\User\Models\{
    User as UserModel,
};
use App\Yantrana\Components\User\Models\Payment;
use \Illuminate\Support\Facades\DB;
use YesTokenAuth;
use App\Yantrana\Support\Utils;

class UserEngine extends BaseEngine
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * @var BaseMailer - Base Mailer
     */
    protected $baseMailer;

    /**
     * @var  UserSettingRepository $userSettingRepository - UserSetting Repository
     */
    protected $userSettingRepository;

    /**
     * @var ManageItemRepository - ManageItem Repository
     */
    protected $manageItemRepository;

    /**
     * @var  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     */
    protected $creditWalletRepository;

    /**
     * @var ManageAbuseReportRepository - ManageAbuseReport Repository
     */
    protected $manageAbuseReportRepository;

    /**
     * @var ManagePlansRepository - ManagePlansRepository Repository
     */
    protected $managePlansRepository;

    /**
     * @var MessengerRepository - MessengerRepository Repository
     */
    protected $messengerRepository;

    /**
     * @var  UserEncounterRepository $userEncounterRepository - UserEncounter Repository
     */
    protected $userEncounterRepository;

    /**
     * @var  CountryRepository $countryRepository - Country Repository
     */
    protected $countryRepository;

    /**
     * @var  MediaEngine $mediaEngine - Media Engine
     */
    protected $mediaEngine;

    /**
     * Constructor.
     * @param  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     * @param UserRepository  $userRepository  - User Repository
     * @param BaseMailer  $baseMailer  - Base Mailer
     * @param  UserSettingRepository $userSettingRepository - UserSetting Repository
     * @param ManageItemRepository $manageItemRepository - ManageItem Repository
     * @param  CountryRepository $countryRepository - Country Repository
     *
     *-----------------------------------------------------------------------*/
    public function __construct(
        BaseMailer  $baseMailer,
        UserRepository $userRepository,
        UserSettingRepository $userSettingRepository,
        ManageItemRepository $manageItemRepository,
        CreditWalletRepository $creditWalletRepository,
        ManageAbuseReportRepository $manageAbuseReportRepository,
        ManagePlansRepository $managePlansRepository,
        MessengerRepository $messengerRepository,
        UserEncounterRepository $userEncounterRepository,
        CountryRepository $countryRepository,
        MediaEngine $mediaEngine
    ) {
        $this->baseMailer                    = $baseMailer;
        $this->userRepository                = $userRepository;
        $this->userSettingRepository        = $userSettingRepository;
        $this->manageItemRepository         = $manageItemRepository;
        $this->creditWalletRepository         = $creditWalletRepository;
        $this->manageAbuseReportRepository     = $manageAbuseReportRepository;
        $this->managePlansRepository     = $managePlansRepository;
        $this->messengerRepository     = $messengerRepository;
        $this->userEncounterRepository         = $userEncounterRepository;
        $this->countryRepository            = $countryRepository;
        $this->mediaEngine                  = $mediaEngine;
    }

    /**
     * Process user login request using user repository & return
     * engine reaction.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processLogin($input)
    {
        //check is email or username
        $user = $this->userRepository->fetchByEmailOrUsername($input['email_or_username']);



        if (!empty($user)) {
            $checkUserSubscriptionStatus = UserSubscription::where('users__id', $user['_id'])->where('status', 1)->get()->toArray(); 
                            
            if (isset($checkUserSubscriptionStatus[0]['status'])) {
                if ($checkUserSubscriptionStatus[0]['status'] == 0) {
                    return $this->engineReaction(2, ['show_message' => true], __tr('You do not have active subscription at the moment. Please select one to process and try login.'));
                }
            }
            
        }

        // Check if empty then return error message
        if (__isEmpty($user)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('You are not a member of the system, Please go and register first , then you can proceed for login.'));
        }

        //collect login credentials
        $loginCredentials = [
            'email'         => $user->email,
            'password'      => $input['password'],
        ];

        //check user status not equal to 1
        if ($user->status != 1) {
            return $this->engineReaction(2, ['show_message' => true], __tr('Your profile is currently in the review/approval process.', ['__status__' => configItem('status_codes', $user->status)]));
        }

        //get remember me data
        $remember_me = (isset($input['remember_me']) and $input['remember_me'] == 'on') ? true : false;

        // Process for login attempt
        if (Auth::attempt($loginCredentials, $remember_me)) {
            // Clear login attempts of ip address
            $this->userRepository->clearLoginAttempts();
            //loggedIn user name
            $loggedInUserName = $user->first_name . ' ' . $user->last_name;
            //get people likes me data
            $userLikedMeData = $this->userRepository->fetchUserLikeMeData();
            //check user like data exists
            if (!__isEmpty($userLikedMeData)) {
                foreach ($userLikedMeData as $userLike) {
                    //notification log message
                    notificationLog($loggedInUserName . ' is online now. ', route('user.profile_view', ['username' => $user->username]), null, $userLike->userId);

                    //push data to pusher
                    PushBroadcast::notifyViaPusher('event.user.notification', [
                        'type'                    => 'user-login',
                        'userUid'                 => $userLike->userUId,
                        'subject'                 => __tr('User Logged In successfully'),
                        'message'                 => $loggedInUserName . __tr(' is online now. '),
                        'messageType'             => __tr('success'),
                        'showNotification'         => getUserSettings('show_user_login_notification', $userLike->userId),
                        'getNotificationList'     => getNotificationList($userLike->userId)
                    ]);
                }
            }

            //if mobile request
            if (isMobileAppRequest()) {

                //issue new token
                $authToken = YesTokenAuth::issueToken([
                    'aud'  => $user->_id,
                    'uaid' => $user->user_authority_id
                ]);

                return $this->engineReaction(1, [
                    'auth_info'     => getUserAuthInfo(1),
                    'access_token'  => $authToken
                ], 'Welcome, you are logged in successfully.');
            }

            return $this->engineReaction(1, [
                'auth_info'     => getUserAuthInfo(1),
                'intendedUrl' => Session::get('intendedUrl'),
                'show_message' => true
            ], __tr('Welcome, you are logged in successfully.'));
        }

        // Store every login attempt.
        $this->userRepository->updateLoginAttempts();

        return $this->engineReaction(2, ['show_message' => true], __tr("Authentication failed, please check your credentials & try again."));
    }

    /**
     * Process logout request
     *
     * @return array
     *---------------------------------------------------------------- */

    public function processLogout()
    {
        if (Session::has('intendedUrl')) {
            Session::forget('intendedUrl');
        }

        if (isset($_SESSION['CURRENT_LOCALE'])) {
            $_SESSION['CURRENT_LOCALE'] = null;
        }

        $userId = Auth::user()->_id;

        //fetch user authority
        $userAuthority = $this->userRepository->fetchUserAuthority($userId);

        //update data
        $updateData = [
            'updated_at' => Carbon::now()->subMinutes(2)->toDateTimeString()
        ];

        // Check for if new email activation store
        if ((!__isEmpty($userAuthority)) and $this->userRepository->updateUserAuthority($userAuthority, $updateData)) {
            Auth::logout();
        } else {
            Auth::logout();
        }

        return $this->engineReaction(2, null, __tr('User logout failed.'));
    }

    /**
     * Process App logout request
     *
     * @return array
     *---------------------------------------------------------------- */

    public function processAppLogout()
    {
        Auth::logout();

        return $this->engineReaction(1, ['auth_info' => getUserAuthInfo()], 'logout Successfully');
    }

    /**
     * User Sign prepare
     *-----------------------------------------------------------------------*/
    public function prepareSignupData()
    {
        $allGenders = configItem('user_settings.gender');
        $genders = [];
        foreach ($allGenders as $key => $value) {
            $genders[] = [
                'id'     => $key,
                'value' => $value
            ];
        }
        return $this->engineReaction(1, [
            'genders' => $genders
        ]);
    }

    /**
     * User Sign Process.
     *
     * @param array $inputData
     *
     *-----------------------------------------------------------------------*/
    public function userSignUpProcess($inputData)
    {
        // Username will be same as email
        $transactionResponse = $this->userRepository->processTransaction(function () use ($inputData) {
            $activationRequiredForNewUser = getStoreSettings('activation_required_for_new_user');
            $inputData['status'] = 1; // Active
            // check if activation is required for new user
            if ($activationRequiredForNewUser) {
                $inputData['status'] = 4; // Never Activated
            }
            // Store user
            $newUser = $this->userRepository->storeUser($inputData);
            // Check if user not stored successfully
            if (!$newUser) {
                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('User not added.'));
            }

            

            $profile_key = array('setting_someone_match','someone_favorite','someone_likes_favorite','someone_comments_topic','subscription_renew');
            foreach ($profile_key as $key => $value) {
                $created_at = date("Y-m-d h:i:s");
                $updated_at = date("Y-m-d h:i:s");
                $insert_defalt_user_settings = DB::table('user_settings')->insert(['created_at' => $created_at,'updated_at' => $updated_at,'key_name' => $value,'value' => 1,'data_type'=>1,'users__id' => $newUser->_id]);
            }





            $userAuthorityData = [
                'user_id' => $newUser->_id,
                'user_roles__id' => 2
            ];
            // Add user authority
            if ($this->userRepository->storeUserAuthority($userAuthorityData)) {

                //check enable bonus credits for new user
                if (getStoreSettings('enable_bonus_credits')) {
                    $creditWalletStoreData = [
                        'status'     => 1,
                        'users__id' => $newUser->_id,
                        'credits'     => getStoreSettings('number_of_credits'),
                        'credit_type' => 1 //Bonuses
                    ];
                    //store user credit transaction data
                    if (!$this->userRepository->storeCreditWalletTransaction($creditWalletStoreData)) {
                        return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('User credits not stored.'));
                    }
                }

                $profileData = [
                    'users__id'     => $newUser->_id,
                    'gender'         => isset($inputData['gender']) ? $inputData['gender'] : '0', // 3 for secret
                    'dob'             => isset($inputData['dob']) ? $inputData['dob'] : '01/01/1970',
                    'status'        => 1
                ];

                //store profile
                if ($this->userRepository->storeUserProfile($profileData)) {

                    //check activation required for new users
                    if ($activationRequiredForNewUser) {
                        if (isMobileAppRequest()) {
                            $emailData = [
                                'fullName' => $newUser->first_name,
                                'email' => $newUser->email,
                                'expirationTime' => configItem('otp_expiry'),
                                'otp' => $newUser->remember_token
                            ];
                            // check if email send to member
                            if ($this->baseMailer->notifyToUser('Your account registered successfully.', 'account.activation-for-app', $emailData, $newUser->email)) {
                                return $this->userRepository->transactionResponse(1, [
                                    'show_message' => true,
                                    'activation_required' => true
                                ], __tr('Your account created successfully, to activate your account please check your email.'));
                            }
                        } else {

                            /** 
                            // Remove comment if user can activate account through email link activation 
                            $emailData = [
                                'fullName' => $newUser->first_name,
                                'email' => $newUser->email,
                                'expirationTime' => configItem('account.expiry'),
                                'activation_url' => URL::temporarySignedRoute('user.account.activation', Carbon::now()->addHours(configItem('account.expiry')), ['userUid' => $newUser->_uid])
                            ];
                            // check if email send to member
                            if ($this->baseMailer->notifyToUser('Your account registered successfully.', 'account.activation', $emailData, $newUser->email)) {
                                return $this->userRepository->transactionResponse(1, ['show_message' => true], __tr('Your account created successfully, to activate your account please check your email.'));
                            }
                            **/

                            session()->put('userSignUpData', [
                                "id"   => $newUser->_id
                            ]);
                            return $this->userRepository->transactionResponse(1, ['show_message' => false]);
                            //return $this->userRepository->transactionResponse(1, ['show_message' => true], __tr('Your account created successfully. Please wait for admin to activate the account'));
                        }
                    } else {
                        return $this->userRepository->transactionResponse(1, ['show_message' => true], __tr('Your account created successfully.'));
                    }
                }
            }
            // Send failed server error message
            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Something went wrong on server, please contact to administrator.'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process user update password request.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUpdatePassword($inputData)
    {
        $user = Auth::user();
        // Check if logged in user password matched with entered password
        if (!Hash::check($inputData['current_password'], $user->password)) {
            return $this->engineReaction(3, ['show_message' => true], __tr('Current password is incorrect.'));
        }

        // Check if user password updated
        if ($this->userRepository->updatePassword($user, $inputData['new_password'])) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Password updated successfully'));
        }

        return $this->engineReaction(14, ['show_message' => true], __tr('Password not updated.'));
    }

    /**
     * Send new email activation reminder.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processChangeEmail($inputData)
    {
        $user = Auth::user();
        // Check if user entered correct password or not
        if (!Hash::check($inputData['current_password'], $user->password)) {
            return $this->engineReaction(3, ['show_message' => true], __tr('Please check your password.'));
        }
        //get data
        $activationRequired = getStoreSettings('activation_required_for_change_email');

        //check activation required or not
        if ($activationRequired) {
            $emailData = [
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'newEmail' => $inputData['new_email'],
                'expirationTime' => configItem('account.change_email_expiry'),
                'activation_url' => URL::temporarySignedRoute('user.new_email.activation', Carbon::now()->addHours(configItem('account.change_email_expiry')), ['userUid' => $user->_uid, 'newEmail' => $inputData['new_email']])

            ];
            // check if email send to member
            if ($this->baseMailer->notifyToUser('New Email Activation.', 'account.new-email-activation', $emailData, $inputData['new_email'])) {
                return $this->engineReaction(1, ['show_message' => true, 'activationRequired' => true], __tr('New email activation link has been sent to your new email address, please check your email.'));
            }
        } else {
            $updateData = [
                'email' => $inputData['new_email']
            ];
            // Check for if new email activation store
            if ($this->userRepository->updateUser($user, $updateData)) {
                return $this->engineReaction(1, [
                    'show_message' => true,
                    'activationRequired' => false,
                    'newEmail' => $inputData['new_email']
                ], __tr('Update email successfully.'));
            }
        }
        //error response
        return $this->engineReaction(2, ['show_message' => true], __tr('Email not updated.'));
    }

    /**
     * Activate new email.
     *
     * @param number $userID
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processNewEmailActivation($userUid, $newEmail)
    {
        $user = $this->userRepository->fetch($userUid);
        // Check if user record exist
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User data not exists.'));
        }
        $updateData = [
            'email' => $newEmail
        ];

        // Check for if new email activation store
        if ($this->userRepository->updateUser($user, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Update email successfully.'));
        }
        //error response
        return $this->engineReaction(2, ['show_message' => true], __tr('Email not updated.'));
    }

    /**
     * Process forgot password request based on passed email address &
     * send password reminder on enter email address.
     *
     * @param string $email
     *
     * @return array
     *---------------------------------------------------------------- */
    public function sendPasswordReminder($email)
    {
        $user = $this->userRepository->fetchActiveUserByEmail($email);

        // Check if user record exist
        if (__isEmpty($user)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('Invalid Request.'));
        }

        // Delete old password reminder for this user
        $this->userRepository->deleteOldPasswordReminder($email);

        $token = YesSecurity::generateUid();
        $createdAt = getCurrentDateTime();

        $storeData = [
            'email'            =>    $email,
            'token'            =>    $token,
            'created_at'    =>    $createdAt
        ];

        // Check for if password reminder added
        if (!$this->userRepository->storePasswordReminder($storeData)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('Invalid Request.'));
        }

        //message data
        $emailData = [
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'expirationTime' => config('__tech.account.password_reminder_expiry'),
            'email' => $user->email,
            'email' => $user->email,
            'email' => $user->email,
            'tokenUrl' => route('user.reset_password', ['reminderToken' => $token]),
        ];

        // if reminder mail has been sent
        if ($this->baseMailer->notifyToUser('Forgot Password.', 'account.password-reminder', $emailData, $user->email)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('We have e-mailed your password reset link.')); // success reaction
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server')); // error reaction
    }

    /**
     * Process reset password request.
     *
     * @param array  $input
     * @param string $reminderToken
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processResetPassword($input, $reminderToken)
    {
        $email = $input['email'];

        //check if mobile app request then change request Url
        $token = $reminderToken;

        //get password reminder count
        $count = $this->userRepository->fetchPasswordReminderCount($token, $email);

        // Check if reminder count not exist on 0
        if (!$count > 0) {
            return  $this->engineReaction(18, ['show_message' => true], __tr('Invalid Request.'));
        }

        //fetch active user by email
        $user = $this->userRepository->fetchActiveUserByEmail($email);

        // Check if user record exist
        if (__isEmpty($user)) {
            return  $this->engineReaction(18, ['show_message' => true], __tr('Invalid Request.'));
        }

        // Check if user password updated
        if ($this->userRepository->resetPassword($user, $input['password'])) {
            return  $this->engineReaction(1, ['show_message' => true], __tr('Password reset successfully.'));
        }

        //failed response
        return  $this->engineReaction(2, ['show_message' => true], __tr('Password not updated.'));
    }

    /**
     * Process Account Activation.
     *
     * @param string $userUid
     *
     *-----------------------------------------------------------------------*/
    public function processAccountActivation($userUid)
    {
        $neverActivatedUser = $this->userRepository->fetchNeverActivatedUser($userUid);

        // Check if never activated user exist or not
        if (__isEmpty($neverActivatedUser)) {
            return $this->engineReaction(18, null, __tr('Account Activation link invalid.'));
        }

        $updateData = [
            'status' => 1 // Active
        ];
        // Check if user activated successfully
        if ($this->userRepository->updateUser($neverActivatedUser, $updateData)) {
            return $this->engineReaction(1, null, __tr('Your account has been activated successfully. Login with your email ID and password.'));
        }

        return  $this->engineReaction(2, null, __tr('Account Activation link invalid.'));
    }


    public function searchRequestFromHome($searchData,$checkUserStatusForAdmin = false){

        $paginateCount = configItem('user_settings.search_pagination');
        $responce_user_data = UserModel::leftJoin('user_authorities', 'users._id', '=', 'user_authorities.users__id')
            ->select(
                __nestedKeyValues([
                    'users.*',
                    'user_authorities' => [
                        '_id as userAuthorityId',
                        'updated_at as userAuthorityUpdatedAt'
                    ]
                ])
            )
            ->Where('users.username', 'like', '%' . $searchData . '%')
            ->where(function ($query) use ($checkUserStatusForAdmin) {
                if ($checkUserStatusForAdmin and !isAdmin()) {
                    $query->where('users.status', '=', 1);
                } else if (!$checkUserStatusForAdmin) {
                    $query->where('users.status', '=', 1);
                }
            })
            ->get()->toArray();

        if (__isEmpty($responce_user_data)) {
            $responce_user_data = array(
                'search_data' => $this->engineReaction(18, [], __tr('User does not exists.')),
                'serch_string' => $searchData);
            return $responce_user_data;
        }else{
            $search_responce_user['profilePicture'] = array();   
            // echo "<pre>";
            // print_r($responce_user_data);exit();
            foreach ($responce_user_data as $key => $responce_users) {

                $uid = $responce_users['_id'];
                $userId = $responce_users['_uid'];

                $userProfile = $this->userSettingRepository->fetchUserProfile($uid);

                $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $userId]);
                $profilePictureUrl = noThumbImageURL();
                if (!__isEmpty($userProfile)) {
                    if (!__isEmpty($userProfile->profile_picture)) {
                        $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $userProfile->profile_picture);
                    }
                }

                $userAge = isset($userProfile['dob']) ? Carbon::parse($userProfile['dob'])->age : null;
                $gender = isset($userProfile['gender']) ? configItem('user_settings.gender', $userProfile['gender']) : null;

                // print_r($userAge);
                // echo "<pre>";
                // print_r($gender);
                // echo "<pre>";

                // print_r(implode(", ", array_filter([$userAge, $gender])));exit();

                $responce_user_data[$key]['profilePicture'] = $profilePictureUrl;
                $responce_user_data[$key]['serch_string'] = $searchData;      
                $responce_user_data[$key]['userOnlineStatus'] = $this->getUserOnlineStatus($responce_users['userAuthorityUpdatedAt']);
                $responce_user_data[$key]['fullName'] = $responce_users['first_name']. ' ' . $responce_users['last_name'];
                $responce_user_data[$key]['isPremiumUser'] = isPremiumUser($uid);
                $responce_user_data[$key]['detailString'] = implode(", ", array_filter([$userAge, $gender]));
                $responce_user_data[$key]['gender'] = $gender;
                $responce_user_data[$key]['dob'] = $userProfile['dob'];
                $responce_user_data[$key]['userAge'] = $userAge;
                $responce_user_data[$key]['countryName'] = $userProfile['countryName'];


            }

            // echo "<pre>";
            // print_r($responce_user_data);exit();

            // $currentPage = $responce_user_data->currentPage() + 1;
            // $fullUrl = route('user.read.view-search-results').'?search=he';

            //      echo "<pre>";
            // print_r($responce_user_data);exit(); 

            // return $this->engineReaction(1, [
            //     'responce_user_data'            => $responce_user_data,
            //     'nextPageUrl'           => $fullUrl . '&page=' . $currentPage,
            //     'hasMorePages'          => $responce_user_data->hasMorePages(),
            //     'totalCount'          => $responce_user_data->total(),
            // ]); 

            return $responce_user_data;
        }

    }

    /**
     * Prepare User Profile Data.
     *
     * @param string $userName
     *
     *-----------------------------------------------------------------------*/
    public function prepareUserProfile($userName)
    {
            
        // fetch User by username
        $user = $this->userRepository->fetchByUsername($userName, true);          
        // check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, [], __tr('User does not exists.'));
        }
        $userId = $user->_id;
        $user_subscription_detail = UserSubscription::where('users__id',$userId)->where('status',1)->get()->toArray();
        $userUid = $user->_uid;

        $userPhotosCollection = $this->userSettingRepository->fetchUserPhotos($userId);
          
        $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => $userUid]);
        // check if user photos exists
        if (!__isEmpty($userPhotosCollection)) {
            foreach ($userPhotosCollection as $userPhoto) {
                $photosData[] = [
                    'image_url' => getMediaUrl($userPhotosFolderPath, $userPhoto->file)
                ];
            }
        }

        $isOwnProfile = ($userId == getUserID()) ? true : false;
        // Prepare user data
        $userData = [
            'userId' => $userId,
            'userUId' => $userUid,
            'email' => $user->email,
            'fullName' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile_number' => $user->mobile_number,
            'userName'    => $user->username,
            'subscription_date' => $user->created_at->toDateTimeString()
        ];

        $userProfileData = $userSpecifications = $userSpecificationData = $userSpecificationTexts = $photosData = [];

        // fetch User details
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        $userProfileForSetting = $this->userSettingRepository->fetchUserSettingsProfile($userId);

            
        $Dbresponce = array();
        if (!empty($userProfileForSetting)) {
            $checkInArray = array('setting_someone_match','someone_favorite','someone_likes_favorite','someone_comments_topic','subscription_renew');
            foreach ($userProfileForSetting as $key => $Settingdetail) {
                if (in_array($Settingdetail['key_name'],$checkInArray)) {
                    $Dbresponce[$Settingdetail['key_name']] = $Settingdetail['value'];
                }
            }
        }

        $userSettingConfig = configItem('user_settings');
        $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $userUid]);
        $profilePictureUrl = noThumbImageURL();
        $coverPictureFolderPath = getPathByKey('cover_photo', ['{_uid}' => $userUid]);
        $coverPictureUrl = noThumbCoverImageURL();
        // Check if user profile exists
        if (!__isEmpty($userProfile)) {
            if (!__isEmpty($userProfile->profile_picture)) {
                $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $userProfile->profile_picture);
            }
            if (!__isEmpty($userProfile->cover_picture)) {
                $coverPictureUrl = getMediaUrl($coverPictureFolderPath, $userProfile->cover_picture);
            }
        }
        // Set cover and profile picture url
        $userData['profilePicture'] = $profilePictureUrl;
        $userData['coverPicture'] = $coverPictureUrl;
        $userData['userAge'] = isset($userProfile->dob) ? Carbon::parse($userProfile->dob)->age : null;

        // check if user profile exists
        if (!\__isEmpty($userProfile)) {
            // Get country name
            $countryName = '';
            if (!__isEmpty($userProfile->countries__id)) {
                $country = $this->countryRepository->fetchById($userProfile->countries__id, ['name']);
                $countryName = $country->name;
            }

            //fetch user liked data by to user id
            $peopleILikeUserIds = $this->userRepository->fetchMyLikeDataByUserId($user->_id)->pluck('to_users__id')->toArray();

            $showMobileNumber = true;
            //check login user exist then don't apply this condition.
            if ($user->_id != getUserID()) {
                //check admin can set true mobile number not display of user
                if (getStoreSettings('display_mobile_number') == 1) {
                    $showMobileNumber = false;
                }
                //check admin can set user choice user can show or not mobile number
                if (getStoreSettings('display_mobile_number') == 2 and getUserSettings('display_user_mobile_number', $user->_id) == 1) {
                    $showMobileNumber = false;
                }
                //check admin can set user choice and user can select people I liked user
                if (getStoreSettings('display_mobile_number') == 2 and getUserSettings('display_user_mobile_number', $user->_id) == 2 and !in_array(getUserID(), $peopleILikeUserIds)) {
                    $showMobileNumber = false;
                }
            }

            if (!empty($user_subscription_detail)) {
                $subscription_detail = "yes";
            } else {
                $subscription_detail = "no";
            }
            


            $userProfileData = [
                'aboutMe'               => $userProfile->about_me,
                'city'                  => $userProfile->city,
                'mobile_number'         => $user->mobile_number,
                'showMobileNumber'        => $showMobileNumber,
                'gender'                => $userProfile->gender,
                'gender_text'           => array_get($userSettingConfig, 'gender.' . $userProfile->gender),
                'country'               => $userProfile->countries__id,
                'country_name'          => $countryName,
                'dob'                   => $userProfile->dob,
                'birthday'              => (!\__isEmpty($userProfile->dob))
                    ? formatDate($userProfile->dob)
                    : '',
                'work_status'           => $userProfile->work_status,
                'formatted_work_status' => array_get($userSettingConfig, 'work_status.' . $userProfile->work_status),
                'education'                 => $userProfile->education,
                'formatted_education'       => array_get($userSettingConfig, 'educations.' . $userProfile->education),
                'preferred_language'    => $userProfile->preferred_language,
                'formatted_preferred_language' => array_get($userSettingConfig, 'preferred_language.' . $userProfile->preferred_language),
                'relationship_status'   => $userProfile->relationship_status,
                'formatted_relationship_status' => array_get($userSettingConfig, 'relationship_status.' . $userProfile->relationship_status),
                'latitude'              => $userProfile->location_latitude,
                'longitude'             => $userProfile->location_longitude,
                'isVerified'            => $userProfile->is_verified,
                'subscription_detail'            => $subscription_detail,
                'userPhotos' => $photosData,
                'user_setting_section_detail'            => $Dbresponce
            ];
        }

        $specificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);
        // Check if user specifications exists
        if (!\__isEmpty($specificationCollection)) {
            $userSpecifications = $specificationCollection->pluck('specification_value', 'specification_key')->toArray();
        }

        $specificationConfig = $this->getUserSpecificationConfig();
        foreach ($specificationConfig['groups'] as $specKey => $specification) {
            $items = [];
            foreach ($specification['items'] as $itemKey => $item) {
                $userSpecValue =  '';
                if (!$isOwnProfile and array_key_exists($itemKey, $userSpecifications)) {
                    $userSpecKey =  $userSpecifications[$itemKey];
                    $value = (isset($item['options']) and isset($item['options'][$userSpecKey]))
                            ? $item['options'][$userSpecKey]
                            : $userSpecifications[$itemKey];
                    $items[] = [
                        'name'  => $itemKey,
                        'label'  => $item['name'],
                        'input_type' => $item['input_type'],
                        'value' => $value,
                        'options' => isset($item['options']) ? $item['options'] : '',
                        'selected_options' => (!__isEmpty($userSpecKey)) ? $userSpecKey : ''
                    ];

                    if ($item['input_type'] == 'dynamic') {
                        $selectedText = null;
                    } else if ($item['input_type'] == 'select') {
                        $options = isset($item['options']) ? $item['options'] : '';
                        $selectedOptions = (!__isEmpty($userSpecKey)) ? $userSpecKey : '';
                        $selectedText = isset($options[$selectedOptions]) ? $options[$selectedOptions] : null;
                    } else {
                        $selectedText = $value;
                    }

                    $selectedText = $value;

                } elseif ($isOwnProfile) {
                    $itemValue = '';
                    $userSpecValue =  isset($userSpecifications[$itemKey])
                        ? $userSpecifications[$itemKey]
                        : '';
                    if (!__isEmpty($userSpecValue)) {
                        $itemValue = isset($item['options'])
                            ? (isset($item['options'][$userSpecValue])
                                ? $item['options'][$userSpecValue] : '')
                            : $userSpecValue;
                    }
                    $items[] = [
                        'name'  => $itemKey,
                        'label'  => $item['name'],
                        'input_type' => $item['input_type'],
                        'value' => $itemValue,
                        'options' => isset($item['options']) ? $item['options'] : '',
                        'selected_options' => $userSpecValue
                    ];

                    if ($item['input_type'] == 'dynamic') {
                        $selectedText = null;
                    } else if ($item['input_type'] == 'select') {
                        $options = isset($item['options']) ? $item['options'] : [];
                        $selectedOptions = $userSpecValue;
                        $selectedText = isset($options[$selectedOptions]) ? $options[$selectedOptions] : null;
                    } else {
                        $selectedText = $itemValue;
                    }
                }                

                $userSpecificationTexts[$itemKey] = $selectedText;
            }
            // Check if Item exists
            if (!__isEmpty($items)) {
                $userSpecificationData[$specKey] = [
                    'title' => $specification['title'],
                    'icon' => $specification['icon'],
                    'items' => $items
                ];
            }
        }

        // Get user photos collection
        $userPhotosCollection = $this->userSettingRepository->fetchUserPhotos($userId);
        $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => $userUid]);
        // check if user photos exists
        if (!__isEmpty($userPhotosCollection)) {
            foreach ($userPhotosCollection as $userPhoto) {
                $photosData[] = [
                    'image_url' => getMediaUrl($userPhotosFolderPath, $userPhoto->file)
                ];
            }
        }

        //fetch like dislike data by to user id
        $likeDislikeData = $this->userRepository->fetchLikeDislike($user->_id);

        $userLikeData = [];
        //check is not empty
        if (!__isEmpty($likeDislikeData)) {
            $userLikeData = [
                '_id' =>  $likeDislikeData->_id,
                'like' => $likeDislikeData->like
            ];
        }

        $userFavouriteData = [];

        //check loggedIn User id doesn't match current user id then
        // store visitor profile data
        if ($userId != getUserID()) {
            $profileVisitorData = $this->userRepository->fetProfileVisitorByUserId($userId);
            //check is empty then store profile visitor data
            if (__isEmpty($profileVisitorData)) {
                $storeData = [
                    'status' => 1,
                    'to_users__id' => $userId,
                    'by_users__id' => getUserID()
                ];

                //store profile visitors data
                if ($this->userRepository->storeProfileVisitors($storeData)) {
                    //user full name
                    $userFullName = $user->first_name . ' ' . $user->last_name;

                    //activity log message
                    activityLog($userFullName . ' ' . 'profile visited.');

                    //loggedIn user name
                    $loggedInUserName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    //check user browser
                    $allowVisitorProfile = getFeatureSettings('browse_incognito_mode');
                    //check in setting allow visitor notification log and pusher request
                    if (!$allowVisitorProfile) {
                        //notification log message
                        notificationLog('Profile visited by' . ' ' . $loggedInUserName, route('user.profile_view', ['username' => Auth::user()->username]), null, $userId);
                        //push data to pusher
                        PushBroadcast::notifyViaPusher('event.user.notification', [
                            'type'                    => 'profile-visitor',
                            'userUid'                 => $userUid,
                            'subject'                 => __tr('Profile visited successfully'),
                            'message'                 => __tr('Profile visited by') . ' ' . $loggedInUserName,
                            'messageType'             => __tr('success'),
                            'showNotification'         => getUserSettings('show_visitor_notification', $user->_id),
                            'getNotificationList'     => getNotificationList($user->_id)
                        ]);
                    }
                } else {
                    return $this->engineReaction(18, [], __tr('Profile visitors not created.'));
                }
            }
        }

        //fetch total visitors data
        $visitorData = $this->userRepository->fetchProfileVisitor($userId);

        //fetch gift collection
        $giftCollection = $this->manageItemRepository->fetchListData(1);

        $giftListData = [];
        if (!__isEmpty($giftCollection)) {
            foreach ($giftCollection as $key => $giftData) {
                //only active gifts
                if ($giftData->status == 1) {
                    $giftImageUrl = '';
                    $giftImageFolderPath = getPathByKey('gift_image', ['{_uid}' => $giftData->_uid]);
                    $giftImageUrl = getMediaUrl($giftImageFolderPath, $giftData->file_name);
                    //get normal price or normal price is zero then show free gift
                    $normalPrice = (isset($giftData['normal_price']) and intval($giftData['normal_price']) <= 0) ? 'Free' : intval($giftData['normal_price']) . ' ' . __tr('credits');

                    //get premium price or premium price is zero then show free gift
                    $premiumPrice = (isset($giftData['premium_price']) and $giftData['premium_price'] <= 0) ? 'Free' : $giftData['premium_price'] . ' ' . __tr('credits');
                    $giftData['premium_price'] . ' ' . __tr('credits');

                    $price = 'Free';
                    //check user is premium or normal or Set price
                    if (isPremiumUser()) {
                        $price = $premiumPrice;
                    } else {
                        $price = $normalPrice;
                    }
                    $giftListData[] = [
                        '_id'                 => $giftData['_id'],
                        '_uid'                 => $giftData['_uid'],
                        'normal_price'         => $normalPrice,
                        'premium_price'     => $giftData['premium_price'],
                        'formattedPrice'     => $price,
                        'gift_image_url'    => $giftImageUrl
                    ];
                }
            }
        }

        //fetch user gift record
        $userGiftCollection = $this->userRepository->fetchUserGift($userId);

        $userGiftData = [];
        //check if not empty
        if (!__isEmpty($userGiftCollection)) {
            foreach ($userGiftCollection as $key => $userGift) {
                $userGiftImgUrl = '';
                $userGiftFolderPath = getPathByKey('gift_image', ['{_uid}' => $userGift->itemUId]);
                $userGiftImgUrl = getMediaUrl($userGiftFolderPath, $userGift->file_name);
                //check gift status is private (1) and check gift send to current user or gift send by current user
                if ($userGift->status == 1 and ($userGift->to_users__id == getUserID() || $userGift->from_users__id == getUserID())) {
                    $userGiftData[] = [
                        '_id'                 => $userGift->_id,
                        '_uid'                 => $userGift->_uid,
                        'itemId'             => $userGift->itemId,
                        'status'             => $userGift->status,
                        'fromUserName'        => $userGift->fromUserName,
                        'senderUserName'    => $userGift->senderUserName,
                        'userGiftImgUrl'     => $userGiftImgUrl
                    ];
                    //check gift status is public (0)
                } elseif ($userGift->status != 1) {
                    $userGiftData[] = [
                        '_id'                 => $userGift->_id,
                        '_uid'                 => $userGift->_uid,
                        'itemId'             => $userGift->itemId,
                        'status'             => $userGift->status,
                        'fromUserName'        => $userGift->fromUserName,
                        'senderUserName'    => $userGift->senderUserName,
                        'userGiftImgUrl'     => $userGiftImgUrl
                    ];
                }
            }
        }

        //fetch block me users
        $blockMeUser =  $this->userRepository->fetchBlockMeUser($user->_id);
        $isBlockUser = false;
        //check if not empty then set variable is true
        if (!__isEmpty($blockMeUser)) {
            $isBlockUser = true;
        }

        //fetch block by me user
        $blockUserData = $this->userRepository->fetchBlockUser($user->_id);
        $blockByMe = false;
        //if it is empty
        if (!__isEmpty($blockUserData)) {
            $blockByMe = true;
        }

        // echo "<pre>";
        // print_r($userSpecificationData);
        // echo "<pre>";
        
        // print_r($userSpecificationTexts);exit;

        return $this->engineReaction(1, [
            'isOwnProfile'          => $isOwnProfile,
            'userData'              => $userData,
            'countries'             => $this->countryRepository->fetchAll()->toArray(),
            'genders'               => $userSettingConfig['gender'],
            'preferredLanguages'    => $userSettingConfig['preferred_language'],
            'relationshipStatuses'  => $userSettingConfig['relationship_status'],
            'workStatuses'          => $userSettingConfig['work_status'],
            'educations'            => $userSettingConfig['educations'],
            'userProfileData'       => $userProfileData,
            'photosData'            => $photosData,
            'userSpecificationData' => $userSpecificationData,
            'userSpecificationTexts' => $userSpecificationTexts,
            'userLikeData'            => $userLikeData,
            'userFavouriteData'            => $userFavouriteData,
            'totalUserLike'            => fetchTotalUserLikedCount($userId),
            'totalVisitors'            => $visitorData->count(),
            'isBlockUser'            => $isBlockUser,
            'blockByMeUser'            => $blockByMe,
            'giftListData'            => $giftListData,
            'userGiftData'            => $userGiftData,
            'userOnlineStatus'        => $this->getUserOnlineStatus($user->userAuthorityUpdatedAt),
            'isPremiumUser'            => isPremiumUser($userId)
        ]);
    }

    public function updateUserProfileDetail($userReqData){ 


        $userSettingConfig = configItem('user_settings');
        
        
        $cityId = isset($userReqData['city_id']) ? $userReqData['city_id'] : 0;

        $user = $this->userRepository->fetchByID(getUserID());

        if (!__isEmpty($cityId)) {

            $cityData = $this->userSettingRepository->fetchCity($cityId);

            //check is empty then show error message
            if (!__isEmpty($cityData)) {
                $cityName = $cityData->name;
                $latitude = $cityData->latitude;
                $longitude = $cityData->longitude;
                // Fetch Country code
                $countryDetails = $this->countryRepository->fetchByCountryCode($cityData->country_code);

                //check is empty then show error message
                if (!__isEmpty($countryDetails)) {
                    
                    $countryId = $countryDetails->_id;
                    $countryName = $countryDetails->name;
                    $isUserLocationUpdated = false;

                    $userProfileDetails = [
                        'gender' => $userReqData['gender'],
                        'about_me' => $userReqData['about_me'] ??"",
                        'dob' => $userReqData['dob'],
                        'countries__id' => $countryId,
                        'city' => $cityName,
                        'location_latitude' => $cityData->latitude,
                        'location_longitude' => $cityData->longitude
                    ];
                    // get user profile
                    $userProfile = $this->userSettingRepository->fetchUserProfile(getUserID());

                    
                    if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                        activityLog($user->first_name . ' ' . $user->last_name . ' added own location.');
                        $isUpdated = true;
                    }
                }
            }            
        }else{
            $cityName = "";
            $latitude = "";
            $longitude = "";
        }

        

        $updateProfileDetail = array(
            'about_me' => $userReqData['about_me'] ??"",
            'gender' => $userReqData['gender'],
            'dob' => $userReqData['dob'],
            'city' => $cityName,
            'location_latitude' => $latitude,
            'location_longitude' => $longitude,
            'relationship_status'   => $userReqData['relationships'],
            'formatted_relationship_status' => array_get($userSettingConfig, 'relationship_status.' . $userReqData['relationships']),
             );

        

        $userSpecificationcollection = array(
            'gender' => $userReqData['gender'],
            'city_id' => $userReqData['city_id'],
            'city_name' => $cityName,
            'smoke' => $userReqData['smoke'],
            'drink' => $userReqData['drink'],
            'married' => $userReqData['married'],
            'looking_for' => $userReqData['looking_for'],
            'kinks' => implode(',',$userReqData['kinks']),
            'body_type' =>  $userReqData['body_type'],
            'height' => $userReqData['height'],
            'hair_color' => $userReqData['hair_color'],
            'eye_color' => $userReqData['eye_color'],
            'ethnicity' => $userReqData['ethnicity'],
            'relocate' => $userReqData['relocate'],
            'children' => $userReqData['children'],
            'no_of_children' => $userReqData['no_of_children'],
            'relationships' => $userReqData['relationships'],
            );
       
        $user_data = array('first_name' => $userReqData['fname'],'last_name' => $userReqData['lname'],'username' => $userReqData['username'], 'email' => $userReqData['email']);
     /*   echo "<pre>"; print_r($userSpecificationcollection);
        print_r($updateProfileDetail); exit;*/
            
            $userdata_res = $this->userSettingRepository->updateUserDataFront($user_data);
        $userSpecificationcollection_res = $this->userSettingRepository->updateUserSpecificationFront($userSpecificationcollection);
      
        $updateProfileDetail_res = $this->userSettingRepository->updateUserProfileFront($updateProfileDetail);
    }

    /**
     * User Like Dislike Process.
     *
     * @param array $inputData
     *
     *-----------------------------------------------------------------------*/
    public function processUserLikeDislike($toUserUid, $like)
    {
        // fetch User by toUserUid
        $user = $this->userRepository->fetch($toUserUid);
        //echo "<pre>";print_r($user);exit();
        // check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('User does not exists.'));
        }

        //delete old encounter User
        $this->userEncounterRepository->deleteOldEncounterUser();

        //user full name
        $userFullName = $user->first_name . ' ' . $user->last_name;
        //loggedIn user name
        $loggedInUserFullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loggedInUserName = Auth::user()->username;
        $showLikeNotification = getFeatureSettings('show_like', null, $user->_id);

        //fetch like dislike data by to user id
        $likeDislikeData = $this->userRepository->fetchLikeDislike($user->_id);

        //check if not empty
        if (!__isEmpty($likeDislikeData)) {
            //if user already liked then show error messages
            if ($like == $likeDislikeData['like'] and $like == 1) {
                if ($this->userRepository->deleteLikeDislike($likeDislikeData)) {

                    // TerminateChat Chat on dislike
                    $this->terminateChat($user->_id);

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 1,
                        'status' => 'deleted',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User Liked Removed Successfully'));
                }
            } elseif ($like == $likeDislikeData['like'] and $like == 0) {
                if ($this->userRepository->deleteLikeDislike($likeDislikeData)) {
                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 2,
                        'status' => 'deleted',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User Disliked Removed Successfully'));
                }
            }

            //update data
            $updateData = ['like' => $like];
            //update like dislike
            if ($this->userRepository->updateLikeDislike($likeDislikeData, $updateData)) {
                //is like 1
                if ($like == 1) {

                    // Initiate Chat on mutual like
                    $this->initiateChat($user->_id);

                    //activity log message
                    activityLog($userFullName . ' ' . 'profile liked.');
                    //check show like feature return true
                    if ($showLikeNotification) {
                        //notification log message
                        notificationLog('Profile liked by' . ' ' . $loggedInUserFullName, route('user.profile_view', ['username' => $loggedInUserName]), null, $user->_id);
                        //push data to pusher
                        PushBroadcast::notifyViaPusher('event.user.notification', [
                            'type'                    => 'user-likes',
                            'userUid'                 => $user->_uid,
                            'subject'                 => __tr('User liked successfully'),
                            'message'                 => __tr('Profile liked by') . ' ' . $loggedInUserFullName,
                            'messageType'             => 'success',
                            'showNotification'         => getUserSettings('show_like_notification', $user->_id),
                            'getNotificationList'     => getNotificationList($user->_id)
                        ]);
                    }
                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 1,
                        'status' => 'updated',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User liked successfully.'));
                } else {
                    //activity log message
                    activityLog($userFullName . ' ' . 'profile Disliked.');

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 2,
                        'status' => 'updated',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User Disliked successfully.'));
                }
            }
        } else {
            //store data
            $storeData = [
                'status' => 1,
                'to_users__id' => $user->_id,
                'by_users__id' => getUserID(),
                'like'           => $like
            ];
            //store like dislike
            if ($this->userRepository->storeLikeDislike($storeData)) {
                //is like 1
                if ($like == 1) {

                    // Initiate Chat on mutual like
                    $this->initiateChat($user->_id);

                    //activity log message
                    activityLog($userFullName . ' ' . 'profile liked.');
                    //check show like feature return true
                    if ($showLikeNotification) {
                        //notification log message
                        notificationLog('Profile liked by' . ' ' . $loggedInUserFullName, route('user.profile_view', ['username' => $loggedInUserName]), null, $user->_id);

                        //push data to pusher
                        PushBroadcast::notifyViaPusher('event.user.notification', [
                            'type'                    => 'user-likes',
                            'userUid'                 => $user->_uid,
                            'subject'                 => __tr('User liked successfully'),
                            'message'                 => __tr('Profile liked by') . ' ' . $loggedInUserFullName,
                            'messageType'             => 'success',
                            'showNotification'         => getUserSettings('show_like_notification', $user->_id),
                            'getNotificationList'     => getNotificationList($user->_id)
                        ]);
                    }

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 1,
                        'status' => 'created',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User liked successfully.'));
                } else {
                    //activity log message
                    activityLog($userFullName . ' ' . 'profile Disliked.');

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'likeStatus' => 2,
                        'status' => 'created',
                        'totalLikes' => fetchTotalUserLikedCount($user->_id)
                    ], __tr('User Disliked successfully.'));
                }
            }
        }
        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong.'));
    }

    /**
     * User Favourite Disfavourite Process.
     *
     * @param array $inputData
     *
     *-----------------------------------------------------------------------*/
    public function processUserFavourite($toUserUid, $favourite)
    {
        // fetch User by toUserUid
        $user = $this->userRepository->fetch($toUserUid);

        // check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('User does not exists.'));
        }

        //delete old encounter User
        $this->userEncounterRepository->deleteOldEncounterUser();

        //user full name
        $userFullName = $user->first_name . ' ' . $user->last_name;
        //loggedIn user name
        $loggedInUserFullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loggedInUserName = Auth::user()->username;
        //$showFavouriteNotification = getFeatureSettings('show_favourite', null, $user->_id);
        $showFavouriteNotification = false;

        //fetch favourite disfavourite data by to user id
        $favouriteDisfavouriteData = $this->userRepository->fetchFavourite($user->_id);

        //check if not empty
        if (!__isEmpty($favouriteDisfavouriteData)) {
            //if user already favourited then show error messages
            if ($favourite == $favouriteDisfavouriteData['favourite'] and $favourite == 1) {
                if ($this->userRepository->deleteFavourite($favouriteDisfavouriteData)) {

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 1,
                        'status' => 'deleted',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User removed from favorite.'));
                }
            } elseif ($favourite == $favouriteDisfavouriteData['favourite'] and $favourite == 0) {
                if ($this->userRepository->deleteFavourite($favouriteDisfavouriteData)) {
                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 2,
                        'status' => 'deleted',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User Disfavourited Removed Successfully'));
                }
            }

            //update data
            $updateData = ['favourite' => $favourite];
            //update favourite disfavourite
            if ($this->userRepository->updateFavourite($favouriteDisfavouriteData, $updateData)) {
                //is favourite 1
                if ($favourite == 1) {

                    //activity log message
                    activityLog($userFullName . ' ' . 'profile favourited.');
                    //check show favourite feature return true
                    if ($showFavouriteNotification) {
                        //notification log message
                        notificationLog('Profile favourited by' . ' ' . $loggedInUserFullName, route('user.profile_view', ['username' => $loggedInUserName]), null, $user->_id);
                        //push data to pusher
                        PushBroadcast::notifyViaPusher('event.user.notification', [
                            'type'                    => 'user-favourites',
                            'userUid'                 => $user->_uid,
                            'subject'                 => __tr('User added as favorite.'),
                            'message'                 => __tr('Profile favourited by') . ' ' . $loggedInUserFullName,
                            'messageType'             => 'success',
                            'showNotification'         => getUserSettings('show_favourite_notification', $user->_id),
                            'getNotificationList'     => getNotificationList($user->_id)
                        ]);
                    }
                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 1,
                        'status' => 'updated',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User added as favorite.'));
                } else {
                    //activity log message
                    activityLog($userFullName . ' ' . 'profile Disfavourited.');

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 2,
                        'status' => 'updated',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User Disfavourited successfully.'));
                }
            }
        } else {
            //store data
            $storeData = [
                'status' => 1,
                'to_users__id' => $user->_id,
                'by_users__id' => getUserID(),
                'favourite'           => $favourite
            ];
            //store favourite disfavourite
            if ($this->userRepository->storeFavourite($storeData)) {
                //is favourite 1
                if ($favourite == 1) {

                    // Initiate Chat on mutual favourite
                    $this->initiateChat($user->_id);

                    //activity log message
                    activityLog($userFullName . ' ' . 'profile favourited.');
                    //check show favourite feature return true
                    if ($showFavouriteNotification) {
                        //notification log message
                        notificationLog('Profile favourited by' . ' ' . $loggedInUserFullName, route('user.profile_view', ['username' => $loggedInUserName]), null, $user->_id);

                        //push data to pusher
                        PushBroadcast::notifyViaPusher('event.user.notification', [
                            'type'                    => 'user-favourites',
                            'userUid'                 => $user->_uid,
                            'subject'                 => __tr('User added as favorite.'),
                            'message'                 => __tr('Profile favourited by') . ' ' . $loggedInUserFullName,
                            'messageType'             => 'success',
                            'showNotification'         => getUserSettings('show_favourite_notification', $user->_id),
                            'getNotificationList'     => getNotificationList($user->_id)
                        ]);
                    }

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 1,
                        'status' => 'created',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User added as favorite.'));
                } else {
                    //activity log message
                    activityLog($userFullName . ' ' . 'profile Disfavourited.');

                    return $this->engineReaction(1, [
                        'user_id' => $user->_id,
                        'show_message' => true,
                        'favouriteStatus' => 2,
                        'status' => 'created',
                        'totalFavourites' => fetchTotalUserFavouritedCount($user->_id)
                    ], __tr('User Disfavourited successfully.'));
                }
            }
        }
        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong.'));
    }

    /**
     * Prepare User Liked Data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareUserLikeDislikedData($likeType)
    {
        //fetch user liked data by to user id
        $likedCollection = $this->userRepository->fetchUserLikeData($likeType, true);

        return $this->engineReaction(1, [
            'usersData' => $this->prepareUserArray($likedCollection),
            'nextPageUrl' => $likedCollection->nextPageUrl()
        ]);
    }

    /**
     * Prepare User Favourite Data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareUserFavouriteData($favouriteType)
    {
        //fetch user liked data by to user id
        $favouriteCollection = $this->userRepository->fetchUserFavouriteData($favouriteType, true);

        return $this->engineReaction(1, [
            'usersData' => $this->prepareUserArray($favouriteCollection),
            'nextPageUrl' => $favouriteCollection->nextPageUrl()
        ]);
    }

    /**
     * Prepare User Liked Me Data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareUserLikeMeData()
    {
        //get people likes me data
        $userLikedMeData = $this->userRepository->fetchUserLikeMeData(true);

        return $this->engineReaction(1, [
            'usersData'          => $this->prepareUserArray($userLikedMeData),
            'nextPageUrl'        => $userLikedMeData->nextPageUrl(),
            'showWhoLikeMeUser'  => getFeatureSettings('show_like')
        ]);
    }

    /**
     * Prepare Mutual like data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareMutualLikeData()
    {
        //fetch user liked data by to user id
        $likedCollection = $this->userRepository->fetchUserLikeData(1, false);
        //pluck people like ids
        $peopleLikeUserIds = $likedCollection->pluck('to_users__id')->toArray();
        //get people likes me data
        $userLikedMeData = $this->userRepository->fetchUserLikeMeData(false)->whereIn('by_users__id', $peopleLikeUserIds);
        //pluck people like me ids
        $mutualLikeIds = $userLikedMeData->pluck('_id')->toArray();
        //get mutual like data
        $mutualLikeCollection = $this->userRepository->fetchMutualLikeUserData($mutualLikeIds);

        return $this->engineReaction(1, [
            'usersData' => $this->prepareUserArray($mutualLikeCollection),
            'nextPageUrl' => $mutualLikeCollection->nextPageUrl()
        ]);
    }


    public function prepareLikeData()
    {
        //fetch user liked data by to user id
        $likedCollection_like = $this->userRepository->UserLikeData(1, false);

           
            //echo "<pre>";print_r($likedCollection_like);
        $userData =array();
        foreach ($likedCollection_like as $key => $Collection_data) {
               

            if (!__isEmpty($Collection_data['profile_picture'])) {
                $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $Collection_data['_uid']]);
                $userImageUrl = getMediaUrl($profileImageFolderPath, $Collection_data['profile_picture']);
            } else {
                $userImageUrl = noThumbImageURL();
            }
            $userAge = isset($Collection_data['dob']) ? Carbon::parse($Collection_data['dob'])->age : null;
            $gender = isset($Collection_data['gender']) ? configItem('user_settings.gender', $Collection_data['gender']) : null;

            $userData[] = [
                        '_id'             => $Collection_data['_id'],
                        '_uid'             => $Collection_data['_uid'],
                        'user_id'             => $Collection_data['_id'],                        
                        'user_uid'             => $Collection_data['_uid'],
                        'userId'             => $Collection_data['userId'],
                        'status'         => $Collection_data['status'],
                        'like'            => $Collection_data['like'],
                        'created_at'     => formatDiffForHumans($Collection_data['created_at']),
                        'updated_at'    => formatDiffForHumans($Collection_data['updated_at']),
                        'userFullName'    => $Collection_data['userFullName'],
                        'username'      => $Collection_data['username'],
                        'userImageUrl'  => $userImageUrl,
                        'profilePicture' => $Collection_data['profile_picture'],
                        'userOnlineStatus' => $this->getUserOnlineStatus($Collection_data['userAuthorityUpdatedAt']),
                        'gender'         => $gender,
                        'dob'             => $Collection_data['dob'],
                        'userAge'        => $userAge,
                        'countryName'     => $Collection_data['countryName'],
                        'isPremiumUser'        => isPremiumUser($Collection_data['userId']),
                        'detailString'    => implode(", ", array_filter([$userAge, $gender]))
                    ];

            
        }

            //echo "<pre>";print_r($userData);exit();
        
       return $likedCollection_data = array('reaction_code' => 1,
                            'data' => array('usersData' => $userData,'nextPageUrl' => ''),
                            'message' => '',
                            'http_code' => '');
        
           //echo "<pre>";print_r($likedCollection_data);exit();

      // return 

    }

    /**
     * Prepare profile visitors Data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareProfileVisitorsData()
    {
        //profile boost all user list
        $isPremiumUser = $this->userRepository->fetchAllPremiumUsers();
        //premium user ids
        $premiumUserIds = $isPremiumUser->pluck('users__id')->toArray();
        //get profile visitor data
        $profileVisitors = $this->userRepository->fetchProfileVisitorData($premiumUserIds);

        $userData = [];
        //check if not empty collection
        if (!__isEmpty($profileVisitors)) {
            foreach ($profileVisitors as $key => $user) {
                //check user browser
                $allowVisitorProfile = getFeatureSettings('browse_incognito_mode', null, $user->userId);

                //check is premium user value is false and in array check premium user exists
                //then data not shown in visitors page
                if (!$allowVisitorProfile and !in_array($user->userId, $premiumUserIds)) {
                    $userImageUrl = '';
                    //check is not empty
                    if (!__isEmpty($user->profile_picture)) {
                        $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $user->userUId]);
                        $userImageUrl = getMediaUrl($profileImageFolderPath, $user->profile_picture);
                    } else {
                        $userImageUrl = noThumbImageURL();
                    }

                    $userAge = isset($user->dob) ? Carbon::parse($user->dob)->age : null;
                    $gender = isset($user->gender) ? configItem('user_settings.gender', $user->gender) : null;

                    $userData[] = [
                        '_id'             => $user->_id,
                        '_uid'             => $user->_uid,
                        'status'         => $user->status,
                        'like'            => $user->like,
                        'created_at'     => formatDiffForHumans($user->created_at),
                        'updated_at'    => formatDiffForHumans($user->updated_at),
                        'userFullName'    => $user->userFullName,
                        'username'      => $user->username,
                        'userImageUrl'  => $userImageUrl,
                        'profilePicture' => $user->profile_picture,
                        'userOnlineStatus' => $this->getUserOnlineStatus($user->userAuthorityUpdatedAt),
                        'gender'         => $gender,
                        'dob'             => $user->dob,
                        'userAge'        => $userAge,
                        'countryName'     => $user->countryName,
                        'isPremiumUser'        => isPremiumUser($user->userId),
                        'detailString'    => implode(", ", array_filter([$userAge, $gender]))
                    ];
                }
            }
        }

        return $this->engineReaction(1, [
            'usersData' => $userData,
            'nextPageUrl' => $profileVisitors->nextPageUrl()
        ]);
    }

    /**
     * Prepare User Subscription Array Data.
     *
     *-----------------------------------------------------------------------*/

    public function fetchUserSubscription()
    {
        
        $currentDateTime = Carbon::now();
        return UserSubscription::select('*')
            ->where('users__id', getUserID())            
            ->where('status', 1)            
            ->latest()
            ->first();
    }


    /**
     * Prepare User Blocked By Login Users Array Data.
     *
     *-----------------------------------------------------------------------*/

    public function fetchBlockedUser()
    {

        $currentDateTime = Carbon::now();
        return UserSubscription::select('*')
            ->where('users__id', getUserID())            
            ->where('status', 1)            
            ->latest()
            ->first();
    }



    public function prepareUserArray($userCollection)
    {

        $userData = [];
        //check if not empty collection
        if (!__isEmpty($userCollection)) {
            foreach ($userCollection as $key => $user) {
                $userImageUrl = '';
                //check is not empty
                if (!__isEmpty($user->profile_picture)) {
                    $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $user->userUId]);
                    $userImageUrl = getMediaUrl($profileImageFolderPath, $user->profile_picture);
                } else {
                    $userImageUrl = noThumbImageURL();
                }

                $userAge = isset($user->dob) ? Carbon::parse($user->dob)->age : null;
                $gender = isset($user->gender) ? configItem('user_settings.gender', $user->gender) : null;

                $userData[] = [
                    '_id'             => $user->_id,
                    '_uid'             => $user->_uid,
                    'user_id'             => $user->userId,
                    'user_uid'             => $user->userUId,
                    'status'         => $user->status,
                    'like'            => $user->like,
                    'created_at'     => formatDiffForHumans($user->created_at),
                    'updated_at'    => formatDiffForHumans($user->updated_at),
                    'userFullName'    => $user->userFullName,
                    'username'      => $user->username,
                    'userImageUrl'  => $userImageUrl,
                    'profilePicture' => $user->profile_picture,
                    'userOnlineStatus' => $this->getUserOnlineStatus($user->userAuthorityUpdatedAt),
                    'gender'         => $gender,
                    'dob'             => $user->dob,
                    'userAge'        => $userAge,
                    'countryName'     => $user->countryName,
                    'isPremiumUser'    => isPremiumUser($user->userId),
                    'detailString'    => implode(", ", array_filter([$userAge, $gender]))
                ];
            }
        }

        return $userData;
    }

    /**
     * Process User Send Gift.
     *
     *-----------------------------------------------------------------------*/
    public function processUserSendGift($inputData, $sendUserUId)
    {
        //buy premium plan request
        $userSendGiftRequest = $this->userRepository->processTransaction(function () use ($inputData, $sendUserUId) {
            //fetch user
            $user = $this->userRepository->fetch($sendUserUId);

            //if user not exists
            if (__isEmpty($user)) {
                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('User does not exists.'));
            }

            //fetch gift data
            $giftData = $this->manageItemRepository->fetch($inputData['selected_gift']);

            //if gift not exists
            if (__isEmpty($giftData)) {
                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Gift data does not exists.'));
            }

            //fetch user credits data
            $totalUserCredits = totalUserCredits();

            //if gift price greater then total user credits then show error message
            if ($giftData->normal_price > $totalUserCredits) {
                return $this->userRepository->transactionResponse(2, [
                    'show_message' => true,
                    'errorType' => 'insufficient_balance'
                ], __tr('Your credit balance is too low, please purchase credits.'));
            }
            //check user is premium or normal or Set price
            if (isPremiumUser()) {
                $credits = $giftData->premium_price;
            } else {
                $credits = $giftData->normal_price;
            }
            //credit wallet store data
            $creditWalletStoreData = [
                'status' => 1,
                'users__id' => getUserID(),
                'credits' => '-' . '' . $credits
            ];

            //store user gift data
            if ($creditWalledId = $this->userRepository->storeCreditWalletTransaction($creditWalletStoreData)) {

                //store gift data
                $giftStoreData = [
                    'status' => (isset($inputData['isPrivateGift'])
                        and $inputData['isPrivateGift'] == 'on') ? 1 : 0,
                    'from_users__id' => getUserID(),
                    'to_users__id'     => $user->_id,
                    'items__id'         => $giftData->_id,
                    'price'             => $giftData->normal_price,
                    'credit_wallet_transactions__id' => $creditWalledId
                ];

                //store gift data
                if ($this->userRepository->storeUserGift($giftStoreData)) {
                    $userFullName = $user->first_name . ' ' . $user->last_name;
                    activityLog($userFullName . ' ' . 'send gift.');
                    //loggedIn user name
                    $loggedInUserName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    //notification log message
                    notificationLog('Gift send by' . ' ' . $loggedInUserName, route('user.profile_view', ['username' => Auth::user()->username]), null, $user->_id);

                    //push data to pusher
                    PushBroadcast::notifyViaPusher('event.user.notification', [
                        'type'                    => 'user-gift',
                        'userUid'                 => $user->_uid,
                        'subject'                 => __tr('Gift send successfully'),
                        'message'                 => __tr('Gift send by') . ' ' . $loggedInUserName,
                        'messageType'             => 'success',
                        'showNotification'         => getUserSettings('show_gift_notification', $user->_id),
                        'getNotificationList'     => getNotificationList($user->_id)
                    ]);

                    return $this->userRepository->transactionResponse(1, [
                        'show_message' => true,
                        'giftUid' => $giftData->_uid
                    ], __tr('Gift send successfully.'));
                }
            }
            //error message
            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Gift not send.'));
        });

        //response
        return $this->engineReaction($userSendGiftRequest);
    }

    /**
     * Process Report User.
     *
     *-----------------------------------------------------------------------*/
    public function processReportUser($inputData, $sendUserUId)
    {
        //fetch user
        $user = $this->userRepository->fetch($sendUserUId);

        //if user not exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('User does not exists.'));
        }

        //fetch reported user data
        $reportUserData = $this->manageAbuseReportRepository->fetchAbuseReport($user->_id);

        //if exist then throw error message
        if (!__isEmpty($reportUserData)) {
            return $this->engineReaction(2, ['show_message' => true], __tr('User already abuse reported.'));
        }

        //store report data
        $storeReportData = [
            'status' => 1,
            'for_users__id' => $user->_id,
            'by_users__id'  => getUserID(),
            'reason'        =>    $inputData['report_reason']
        ];
        // store report data
        if ($this->manageAbuseReportRepository->storeReportUser($storeReportData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('User abuse reported successfully.'));
        }

        //error message
        return $this->engineReaction(1, ['show_message' => true], __tr('User failed to abuse report.'));
    }

    /**
     * Process Block User.
     *
     *-----------------------------------------------------------------------*/
    public function processBlockUser($inputData)
    {
        //fetch user
        $user = $this->userRepository->fetch($inputData['block_user_id']);

        //if user not exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User does not exists.'));
        }

        //fetch block user data
        $blockUserData = $this->userRepository->fetchBlockUser($user->_id);

        //check is not empty
        if (!__isEmpty($blockUserData)) {
            //error response
            return $this->engineReaction(2, null, __tr('You are already block this user.'));
        }

        //store data
        $storeData = [
            'status' => 1,
            'to_users__id' => $user->_id,
            'by_users__id' => getUserID()
        ];

        //store block user data
        if ($this->userRepository->storeBlockUser($storeData)) {
            //user full name
            $userFullName = $user->first_name . ' ' . $user->last_name;
            //loggedIn user name
            $loggedInUserName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            //activity log message
            activityLog($loggedInUserName . ' ' . 'blocked by.' . ' ' . $userFullName);

            //success response
            return $this->engineReaction(1, null, __tr('Blocked user successfully.'));
        }
        //error response
        return $this->engineReaction(2, null, __tr('User not block.'));
    }

    /**
     * Prepare Block User Data.
     *
     *-----------------------------------------------------------------------*/
    public function prepareBlockUserData()
    {
        $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

        $blockUserList = [];
        //check if not empty
        if (!__isEmpty($blockUserCollection)) {
            foreach ($blockUserCollection as $key => $blockUser) {
                $userImageUrl = '';
                //check is not empty
                if (!__isEmpty($blockUser->profile_picture)) {
                    $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $blockUser->userUId]);
                    $userImageUrl = getMediaUrl($profileImageFolderPath, $blockUser->profile_picture);
                } else {
                    $userImageUrl = noThumbImageURL();
                }

                $userAge = isset($blockUser->dob) ? Carbon::parse($blockUser->dob)->age : null;
                $gender = isset($blockUser->gender) ? configItem('user_settings.gender', $blockUser->gender) : null;

                $blockUserList[] = [
                    '_id'             => $blockUser->_id,
                    '_uid'             => $blockUser->_uid,
                    'userId'         => $blockUser->userId,
                    'userUId'         => $blockUser->userUId,
                    'userFullName'     => $blockUser->userFullName,
                    'status'         => $blockUser->status,
                    'created_at'     => formatDiffForHumans($blockUser->created_at),
                    'userOnlineStatus' => $this->getUserOnlineStatus($blockUser->userAuthorityUpdatedAt),
                    'username'        =>    $blockUser->username,
                    'userImageUrl'  => $userImageUrl,
                    'profilePicture' => $blockUser->profile_picture,
                    'gender'         => $gender,
                    'dob'             => $blockUser->dob,
                    'userAge'        => $userAge,
                    'countryName'     => $blockUser->countryName,
                    'isPremiumUser'    => isPremiumUser($blockUser->userId),
                    'detailString'    => implode(", ", array_filter([$userAge, $gender]))
                ];
            }
        }

        //success reaction
        return $this->engineReaction(1, [
            'usersData' => $blockUserList,
            'nextPageUrl'     => $blockUserCollection->nextPageUrl()
        ]);
    }

    /**
     *Process unblock user.
     *
     *-----------------------------------------------------------------------*/
    public function processUnblockUser($userUid)
    {
        //fetch user
        $user = $this->userRepository->fetch($userUid);

        //if user not exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User does not exists.'));
        }

        //fetch block user data
        $blockUserData = $this->userRepository->fetchBlockUser($user->_id);

        //if it is empty
        if (__isEmpty($blockUserData)) {
            return $this->engineReaction(2, null, __tr('Block user does not exists.'));
        }

        //delete block user
        if ($this->userRepository->deleteBlockUser($blockUserData)) {
            //user full name
            $userFullName = $user->first_name . ' ' . $user->last_name;
            //loggedIn user name
            $loggedInUserName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            //activity log message
            activityLog($loggedInUserName . ' ' . 'Unblock by.' . ' ' . $userFullName);
            //success response
            return $this->engineReaction(1, [
                'blockUserUid' => $blockUserData->_uid,
                'blockUserLength' => $this->userRepository->fetchAllBlockUser()->count()
            ], __tr('User has been unblock successfully.'));
        }

        //error response
        return $this->engineReaction(2, null, __tr('Failed to unblock user.'));
    }


    /**
     *  Process Boost Profile
     *
     *-----------------------------------------------------------------------*/
    public function processBoostProfile()
    {
        $transactionResponse = $this->userRepository->processTransaction(function () {
            $user = Auth::user();

            //fetch user
            $activeBoost = $this->userRepository->fetchActiveProfileBoost($user->_id);

            $remainingTime = 0;

            if (!__isEmpty($activeBoost)) {
                $remainingTime = Carbon::now()->diffInSeconds($activeBoost->expiry_at, false);
            }

            $totalUserCredits = totalUserCredits();
            $boostPeriod = getStoreSettings('booster_period');
            $boostPrice = getStoreSettings('booster_price');

            if (isPremiumUser()) {
                $boostPrice = getStoreSettings('booster_price_for_premium_user');
            }

            if ($totalUserCredits < $boostPrice) {
                return $this->userRepository->transactionResponse(2, [
                    'show_message' => true,
                    'creditsRemaining' => totalUserCredits()
                ], __tr('Enough credits are not available. Please buy some credits'));
            }

            //credit wallet store data
            $creditWalletStoreData = [
                'status' => 1,
                'users__id' => $user->_id,
                'credits' => '-' . '' . $boostPrice
            ];

            //store user gift data
            if ($creditWalledId = $this->userRepository->storeCreditWalletTransaction($creditWalletStoreData)) {
                $boosterData = [
                    'for_users__id' => $user->_id,
                    'expiry_at'     => Carbon::now()->addSeconds(($remainingTime + ($boostPeriod * 60))),
                    'status' => 1,
                    'credit_wallet_transactions__id' => $creditWalledId
                ];

                if ($booster = $this->userRepository->storeBooster($boosterData)) {

                    //activity log message
                    activityLog(strtr("Booster activated by user __firstName__ __lastName__", [
                        '__firstName__' => $user->first_name,
                        '__lastName__' => $user->last_name
                    ]));

                    //fetch user
                    $activeBoost = $this->userRepository->fetchActiveProfileBoost($user->_id);

                    //success response
                    return $this->userRepository->transactionResponse(1, [
                        'show_message' => true,
                        'boosterExpiry' => Carbon::now()->diffInSeconds($activeBoost->expiry_at, false),
                        'creditsRemaining' => totalUserCredits()
                    ], __tr('Booster activated successfully'));
                }
            }

            //error response
            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to boost profile.'));
        });
        return $this->engineReaction($transactionResponse);
    }

    /**
     *  Check profile status
     *
     *-----------------------------------------------------------------------*/
    public function checkProfileStatus()
    {
        //get profile
        $userProfile = $this->userSettingRepository->fetchUserProfile(getUserID());

        if (__isEmpty($userProfile)) {
            $userProfile = $this->userRepository->storeUserProfile([
                'users__id' => getUserID(),
                'status'    => 1,
            ]);
        }

        $steps = configItem('profile_update_wizard');

        if ($userProfile->status == 2) {
            $profileStatus = [
                'step_one'         => true,
                'step_two'         => true
            ];
        } else {

            //check if co-ordinates are set
            if ((__isEmpty($userProfile['location_longitude'])
                    or $userProfile['location_longitude'] == 0)
                and (__isEmpty($userProfile['location_latitude'])
                    or $userProfile['location_latitude'] == 0)
            ) {
                $profileStatus['step_two'] = false;
            } else {
                $profileStatus['step_two'] = true;
            }

            //for step one
            $profileStatus['step_one'] = $this->isStepCompleted($userProfile->toArray(), $steps['step_one']);
        }

        //preview options
        $profileInfo = [
            'profile_picture_url' => null,
            'cover_picture_url' => null,
            'gender' => $userProfile['gender'],
            'birthday' => $userProfile['dob'],
            'location_longitude' => isset($userProfile['location_longitude']) ? floatval($userProfile['location_longitude']) : null,
            'location_latitude'  => isset($userProfile['location_latitude']) ? floatval($userProfile['location_latitude']) : null
        ];

        $userUID = authUID();

        //profile pic
        if (isset($userProfile['profile_picture']) and !__isEmpty($userProfile['profile_picture'])) {
            //path
            $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $userUID]);
            // url
            $profileInfo['profile_picture_url'] = getMediaUrl($profilePictureFolderPath, $userProfile['profile_picture']);
        }

        //cover photo
        if (isset($userProfile['cover_picture']) and !__isEmpty($userProfile['cover_picture'])) {
            //path
            $coverPictureFolderPath = getPathByKey('cover_photo', ['{_uid}' => $userUID]);
            // url
            $profileInfo['cover_picture_url'] = getMediaUrl($coverPictureFolderPath, $userProfile['cover_picture']);
        }

        return $this->engineReaction(1, [
            'profileStatus' => $profileStatus,
            'profileInfo'     => $profileInfo,
            'genders'         => configItem('user_settings.gender'),
            'profileMediaRestriction' => getMediaRestriction('profile'),
            'coverImageMediaRestriction' => getMediaRestriction('cover_image')
        ]);
    }

    /**
     *  Check profile status
     *
     *-----------------------------------------------------------------------*/
    public function finishWizard()
    {
        //get profile
        $userProfile = $this->userSettingRepository->fetchUserProfile(getUserID());

        if ($this->userRepository->updateProfile($userProfile, ['status' => 2]) || $userProfile->status == 2) {
            return $this->engineReaction(1, [
                'redirectURL' => route('user.profile_view', ['username' => getUserAuthInfo('profile.username')])
            ], __tr("Wizard completed successfully"));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr("Failed to complete profile"));
    }

    /**
     *  check if step completed
     *
     *-----------------------------------------------------------------------*/
    private function isStepCompleted($profile, $stepFields)
    {
        if (!__isEmpty($profile)) {
            foreach ($profile as $key => $value) {
                if (in_array($key, $stepFields)) {
                    if (__isEmpty($profile[$key])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Process user contact request.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processContact($inputData)
    {
        //contact email data
        $emailData = [
            'userName'        => $inputData['fullName'],
            'senderEmail'     => $inputData['email'],
            'toEmail'         => getStoreSettings('contact_email'),
            'subject'       => $inputData['subject'],
            'messageText'   => $inputData['message']
        ];

        // check if email send to member
        if ($this->baseMailer->notifyAdmin($inputData['subject'], 'contact', $emailData, 2)) {
            //success response
            return $this->engineReaction(1, ['show_message' => true], __tr('Mail has been sent successfully, we contact soon.'));
        }
        //error response
        return $this->engineReaction(2, ['show_message' => true], __tr('Failed to send mail.'));
    }

    /**
     * Process user contact request.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function getBoosterInfo()
    {
        return $this->engineReaction(1, [
            'booster_period'     => __tr(getStoreSettings('booster_period')),
            'booster_price'        => __tr((isPremiumUser()) ? getStoreSettings('booster_price_for_premium_user') : getStoreSettings('booster_price'))
        ]);
    }

    /**
     * Process delete account.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDeleteAccount($inputData)
    {
        $user = $this->userRepository->fetchByID(getUserID());

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        if (!Hash::check($inputData['password'], $user->password)) {
            return $this->engineReaction(3, ['show_message' => true], __tr('Current password is incorrect.'));
        }

        // Delete all media of user
        $deletedMedia = $this->mediaEngine->deleteUserAccount();

        // Delete account successfully
        if ($this->userRepository->deleteUser($user)) {
            // Process Logout user
            $this->processLogout();
            return $this->engineReaction(1, ['show_message' => true], __tr('Your account has been deleted successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Account not deleted.'));
    }

    /**
     * Process delete account.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function resendActivationMail($inputData)
    {
        $user = $this->userRepository->fetchByEmail($inputData['email']);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('You are not a member of this system.'));
        }

        // Check if user exists
        if ($user->status == 1) {
            return $this->engineReaction(18, ['show_message' => true], __tr('Account already activated.'));
        }

        $transactionResponse = $this->userRepository->processTransaction(function () use ($inputData, $user) {

            // Delete account successfully
            if ($updatedUser = $this->userRepository->updateUser($user, [
                'remember_token' => Utils::generateStrongPassword(4, false, 'ud')
            ])) {
                $emailData = [
                    'fullName' => $user->first_name,
                    'email' => $user->email,
                    'expirationTime' => configItem('otp_expiry'),
                    'otp' => $updatedUser->remember_token
                ];

                // check if email send to member
                if ($this->baseMailer->notifyToUser('Activation mail sent.', 'account.activation-for-app', $emailData, $user->email)) {
                    return $this->userRepository->transactionResponse(1, [
                        'show_message' => true,
                        'activation_mail_sent' => true
                    ], __tr('Activation mail sent successfully, to activate your account please check your email.'));
                }

                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to send activation mail'));
            }
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process verify otp
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function verifyOtpProcess($inputData, $type)
    {
        // exit;
        $user = $this->userRepository->fetchByEmail($inputData['email']);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('You are not a member of this system.'));
        }

        $transactionResponse = $this->userRepository->processTransaction(function () use ($inputData, $user, $type) {
            if ($type == 1) {
                $neverActivatedUser = $this->userRepository->fetchNeverActivatedUserForApp($inputData['email']);

                // Check if never activated user exist or not
                if (__isEmpty($neverActivatedUser)) {
                    return $this->userRepository->transactionResponse(18, null, __tr('Invalid OTP'));
                }


                if ($user->remember_token == $inputData['otp']) {
                    $updatedUser = $this->userRepository->updateUser($user, ['remember_token' => null, 'status' => 1]);

                    return $this->userRepository->transactionResponse(1, [
                        'show_message' => true
                    ], __tr('Otp verified successfully.'));
                }
            } elseif ($type == 2) {
                $passwordReset = $this->userRepository->fetchPasswordReset($inputData['otp']);

                if (__isEmpty($passwordReset)) {
                    return $this->userRepository->transactionResponse(18, null, __tr('Invalid OTP'));
                }

                return $this->userRepository->transactionResponse(1, [
                    'show_message' => true,
                    'account_verified' => true
                ], __tr('OTP verified successfully.'));
            }

            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Invalid OTP'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process delete account.
     *
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function requestNewPassword($inputData)
    {
        $user = $this->userRepository->fetchByEmail($inputData['email']);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('You are not a member of this system.'));
        }

        // Check if user exists
        if ($user->status != 1) {
            return $this->engineReaction(18, [
                'show_message' => true
            ], __tr('Your account might be Inactive, Blocked or Not Activated.'));
        }

        $transactionResponse = $this->userRepository->processTransaction(function () use ($inputData, $user) {

            // Delete old password reminder for this user
            $this->userRepository->appDeleteOldPasswordReminder($inputData['email']);

            $currentDateTime = Carbon::now();
            $token = Utils::generateStrongPassword(4, false, 'ud');
            $createdAt = $currentDateTime->addSeconds(configItem('otp_expiry'));

            $storeData = [
                'email'            =>    $inputData['email'],
                'token'            =>    $token,
                'created_at'    =>    $createdAt
            ];

            // Check for if password reminder added
            if (!$this->userRepository->storePasswordReminder($storeData)) {
                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Invalid Request.'));
            }

            $otpExpiry = configItem('otp_expiry');
            $differenceSeconds = Carbon::now()->diffInSeconds($createdAt, false);
            $newExpiryTime = 0;
            if ($differenceSeconds > 0 and $differenceSeconds < $otpExpiry) {
                $newExpiryTime = $differenceSeconds;
            }

            $emailData = [
                'fullName'             => $user->first_name . ' ' . $user->last_name,
                'email'             => $user->email,
                'expirationTime'     => config('__tech.account.app_password_reminder_expiry'),
                'otp'                 => $token
            ];

            // check if email send to member
            if ($this->baseMailer->notifyToUser('OTP sent.', 'account.forgot-password-for-app', $emailData, $user->email)) {
                return $this->userRepository->transactionResponse(1, [
                    'show_message' => true,
                    'mail_sent' => true,
                    'newExpiryTime' => $newExpiryTime,
                ], __tr('OTP sent successfully, to reset password use OTP sent to your email.'));
            }

            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to send OTP'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process Forgot Password resend otp request
     * @param $userEmail array - userEmail data
     * @return json object
     */
    public function processForgotPasswordResendOtp($userEmail)
    {
        $transactionResponse = $this->userRepository->processTransaction(function () use ($userEmail) {
            $user = $this->userRepository->fetchActiveUserByEmail($userEmail);

            // Check if empty then return error message
            if (__isEmpty($user)) {
                return $this->userRepository->transactionResponse(2, null, 'You are not a member of the system, Please go and register first , then you can proceed for login.');
            }

            // Delete old password reminder for this user
            $this->userRepository->appDeleteOldPasswordReminder($user->email);

            //check if mobile app request then change request Url
            $currentDateTime = Carbon::now();
            $token = Utils::generateStrongPassword(4, false, 'ud');
            $createdAt = $currentDateTime->addSeconds(configItem('otp_expiry'));

            $storeData = [
                'email'            =>    $user->email,
                'token'            =>    $token,
                'created_at'    =>    $createdAt
            ];

            // Check for if password reminder added
            if (!$this->userRepository->storePasswordReminder($storeData)) {
                return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Invalid Request.'));
            }

            $emailData = [
                'fullName'             => $user->first_name,
                'email'             => $user->email,
                'expirationTime'     => config('__tech.account.app_password_reminder_expiry'),
                'otp'                 => $token
            ];

            $otpExpiry = configItem('otp_expiry');
            $differenceSeconds = Carbon::now()->diffInSeconds($createdAt, false);
            $newExpiryTime = 0;
            if ($differenceSeconds > 0 and $differenceSeconds < $otpExpiry) {
                $newExpiryTime = $differenceSeconds;
            }

            // check if email send to member
            if ($this->baseMailer->notifyToUser('OTP sent.', 'account.forgot-password-for-app', $emailData, $user->email)) {
                return $this->userRepository->transactionResponse(1, [
                    'show_message' => true,
                    'mail_sent' => true,
                    'newExpiryTime' => $newExpiryTime,
                ], __tr('OTP sent successfully, to reset password use OTP sent to your email.'));
            }

            return $this->userRepository->transactionResponse(2, null, 'Invalid Request'); // error reaction
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process reset password request.
     *
     * @param array  $input
     * @param string $reminderToken
     *
     * @return array
     *---------------------------------------------------------------- */
    public function resetPasswordForApp($input)
    {
        $email = $input['email'];

        //fetch active user by email
        $user = $this->userRepository->fetchActiveUserByEmail($email);

        // Check if user record exist
        if (__isEmpty($user)) {
            return  $this->engineReaction(18, ['show_message' => true], __tr('Invalid Request.'));
        }

        // Check if user password updated
        if ($this->userRepository->resetPassword($user, $input['password'])) {
            return  $this->engineReaction(1, [
                'show_message' => true,
                'password_changed' => true
            ], __tr('Password reset successfully.'));
        }

        //failed response
        return  $this->engineReaction(2, ['show_message' => true], __tr('Password not updated.'));
    }

    /**
     * prepare profile details
     * @return array
     *---------------------------------------------------------------- */
    public function prepareProfileDetails($username)
    {
        // fetch User by username
        $user = $this->userRepository->fetchByUsername($username, true);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $userId = $user->_id;
        $userUid = $user->_uid;
        $isOwnProfile = ($userId == getUserID()) ? true : false;
        // Prepare user data
        $userData = [
            'userId'             => $userId,
            'userUId'             => $userUid,
            'fullName'             => $user->first_name . ' ' . $user->last_name,
            'first_name'         => $user->first_name,
            'last_name'         => $user->last_name,
            'mobile_number'     => $user->mobile_number,
            'userName'            => $user->username,
            'userOnlineStatus'    => $this->getUserOnlineStatus($user->userAuthorityUpdatedAt),
        ];

        $userProfileData = $userSpecifications = $userSpecificationData = $photosData = [];

        // fetch User details
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);
        $userSettingConfig = configItem('user_settings');
        $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $userUid]);
        $profilePictureUrl = noThumbImageURL();
        $coverPictureFolderPath = getPathByKey('cover_photo', ['{_uid}' => $userUid]);
        $coverPictureUrl = noThumbCoverImageURL();

        // Check if user profile exists
        if (!__isEmpty($userProfile)) {
            if (!__isEmpty($userProfile->profile_picture)) {
                $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $userProfile->profile_picture);
            }
            if (!__isEmpty($userProfile->cover_picture)) {
                $coverPictureUrl = getMediaUrl($coverPictureFolderPath, $userProfile->cover_picture);
            }
        }

        // Set cover and profile picture url
        $userData['profilePicture'] = $profilePictureUrl;
        $userData['coverPicture'] = $coverPictureUrl;
        $userData['userAge'] = isset($userProfile->dob) ? Carbon::parse($userProfile->dob)->age : null;

        // check if user profile exists
        if (!__isEmpty($userProfile)) {
            // Get country name
            $countryName = '';
            if (!__isEmpty($userProfile->countries__id)) {
                $country = $this->countryRepository->fetchById($userProfile->countries__id, ['name']);
                $countryName = $country->name;
            }
            $userProfileData = [
                'aboutMe'               => $userProfile->about_me,
                'city'                  => $userProfile->city,
                'mobile_number'         => $user->mobile_number,
                'gender'                => $userProfile->gender,
                'gender_text'           => array_get($userSettingConfig, 'gender.' . $userProfile->gender),
                'country'               => $userProfile->countries__id,
                'country_name'          => $countryName,
                'dob'                   => $userProfile->dob,
                'birthday'              => (!\__isEmpty($userProfile->dob))
                    ? formatDate($userProfile->dob)
                    : '',
                'work_status'           => $userProfile->work_status,
                'formatted_work_status' => array_get($userSettingConfig, 'work_status.' . $userProfile->work_status),
                'education'                 => $userProfile->education,
                'formatted_education'       => array_get($userSettingConfig, 'educations.' . $userProfile->education),
                'preferred_language'    => $userProfile->preferred_language,
                'formatted_preferred_language' => array_get($userSettingConfig, 'preferred_language.' . $userProfile->preferred_language),
                'relationship_status'   => $userProfile->relationship_status,
                'formatted_relationship_status' => array_get($userSettingConfig, 'relationship_status.' . $userProfile->relationship_status),
                'latitude'              => isset($userProfile->location_latitude) ? floatval($userProfile->location_latitude) : null,
                'longitude'             => isset($userProfile->location_longitude) ? floatval($userProfile->location_longitude) : null,
                'isVerified'            => $userProfile->is_verified,
            ];
        }

        // Get user photos collection
        $userPhotosCollection = $this->userSettingRepository->fetchUserPhotos($userId);
        $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => authUID()]);
        // check if user photos exists
        if (!__isEmpty($userPhotosCollection)) {
            foreach ($userPhotosCollection as $userPhoto) {
                $photosData[] = [
                    'image_url' => getMediaUrl($userPhotosFolderPath, $userPhoto->file)
                ];
            }
        }

        //fetch total visitors data
        $visitorData = $this->userRepository->fetchProfileVisitor($userId);

        //fetch user gift record
        $userGiftCollection = $this->userRepository->fetchUserGift($userId);

        $userGiftData = [];
        //check if not empty
        if (!__isEmpty($userGiftCollection)) {
            foreach ($userGiftCollection as $key => $userGift) {
                $userGiftImgUrl = '';
                $userGiftFolderPath = getPathByKey('gift_image', ['{_uid}' => $userGift->itemUId]);
                $userGiftImgUrl = getMediaUrl($userGiftFolderPath, $userGift->file_name);
                //check gift status is private (1) and check gift send to current user or gift send by current user
                if ($userGift->status == 1 and ($userGift->to_users__id == getUserID() || $userGift->from_users__id == getUserID())) {
                    if (__isEmpty($userGift->file_name)) {
                        $userGiftImgUrl = noThumbImageURL();
                    }

                    $userGiftData[] = [
                        '_id'                 => $userGift->_id,
                        '_uid'                 => $userGift->_uid,
                        'itemId'             => $userGift->itemId,
                        'status'             => $userGift->status,
                        'fromUserName'        => $userGift->fromUserName,
                        'senderUserName'    => $userGift->senderUserName,
                        'userGiftImgUrl'     => $userGiftImgUrl
                    ];
                    //check gift status is public (0)
                } elseif ($userGift->status != 1) {
                    if (__isEmpty($userGift->file_name)) {
                        $userGiftImgUrl = noThumbImageURL();
                    }

                    $userGiftData[] = [
                        '_id'                 => $userGift->_id,
                        '_uid'                 => $userGift->_uid,
                        'itemId'             => $userGift->itemId,
                        'status'             => $userGift->status,
                        'fromUserName'        => $userGift->fromUserName,
                        'senderUserName'    => $userGift->senderUserName,
                        'userGiftImgUrl'     => $userGiftImgUrl
                    ];
                }
            }
        }

        //fetch gift collection
        $giftCollection = $this->manageItemRepository->fetchListData(1);

        $giftListData = [];
        if (!__isEmpty($giftCollection)) {
            foreach ($giftCollection as $key => $giftData) {
                //only active gifts
                if ($giftData->status == 1) {
                    $giftImageUrl = '';
                    $giftImageFolderPath = getPathByKey('gift_image', ['{_uid}' => $giftData->_uid]);
                    $giftImageUrl = getMediaUrl($giftImageFolderPath, $giftData->file_name);
                    //get normal price or normal price is zero then show free gift
                    $normalPrice = (isset($giftData['normal_price']) and intval($giftData['normal_price']) <= 0) ? 'Free' : intval($giftData['normal_price']) . ' ' . __tr('credits');

                    //get premium price or premium price is zero then show free gift
                    $premiumPrice = (isset($giftData['premium_price']) and $giftData['premium_price'] <= 0) ? 'Free' : $giftData['premium_price'] . ' ' . __tr('credits');
                    $giftData['premium_price'] . ' ' . __tr('credits');

                    $price = 'Free';
                    //check user is premium or normal or Set price
                    if (isPremiumUser()) {
                        $price = $premiumPrice;
                    } else {
                        $price = $normalPrice;
                    }
                    $giftListData[] = [
                        '_id'                 => $giftData['_id'],
                        '_uid'                 => $giftData['_uid'],
                        'normal_price'         => $normalPrice,
                        'premium_price'     => $giftData['premium_price'],
                        'formattedPrice'     => $price,
                        'gift_image_url'    => $giftImageUrl
                    ];
                }
            }
        }

        $specificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);
        // Check if user specifications exists
        if (!\__isEmpty($specificationCollection)) {
            $userSpecifications = $specificationCollection->pluck('specification_value', 'specification_key')->toArray();
        }
        $specificationConfig = $this->getUserSpecificationConfig();
        foreach ($specificationConfig['groups'] as $specKey => $specification) {
            $items = [];
            foreach ($specification['items'] as $itemKey => $item) {
                $itemValue = '';
                $userSpecValue =  isset($userSpecifications[$itemKey])
                    ? $userSpecifications[$itemKey]
                    : '';
                if (!__isEmpty($userSpecValue)) {
                    $itemValue = isset($item['options'])
                        ? (isset($item['options'][$userSpecValue])
                            ? $item['options'][$userSpecValue] : '')
                        : $userSpecValue;
                }
                $items[] = [
                    'label'  => $item['name'],
                    'value' => $itemValue,
                ];
            }

            // Check if Item exists
            if (!__isEmpty($items)) {
                $userSpecificationData[$specKey] = [
                    'title' => $specification['title'],
                    'items' => $items
                ];
            }
        }

        //fetch block me users
        $blockMeUser =  $this->userRepository->fetchBlockMeUser($user->_id);
        $isBlockUser = false;
        //check if not empty then set variable is true
        if (!__isEmpty($blockMeUser)) {
            $isBlockUser = true;
        }

        //fetch block by me user
        $blockUserData = $this->userRepository->fetchBlockUser($user->_id);
        $blockByMe = false;
        //if it is empty
        if (!__isEmpty($blockUserData)) {
            $blockByMe = true;
        }

        //fetch like dislike data by to user id
        $likeDislikeData = $this->userRepository->fetchLikeDislike($user->_id);

        $userLikeData = [];
        //check is not empty
        if (!__isEmpty($likeDislikeData)) {
            $userLikeData = [
                '_id' =>  $likeDislikeData->_id,
                'like' => $likeDislikeData->like
            ];
        }

        return $this->engineReaction(1, [
            'userData'              => $userData,
            'userProfileData'       => $userProfileData,
            'photosData'            => $photosData,
            'totalUserLike'            => fetchTotalUserLikedCount($userId),
            'totalUserCredits'        => totalUserCredits(),
            'totalVisitors'            => $visitorData->count(),
            'userGiftData'            => $userGiftData,
            'isPremiumUser'            => isPremiumUser($userId),
            'isOwnProfile'            => ($userId == getUserID()) ? true : false,
            'specifications'        => (array) $userSpecificationData,
            'isBlockUser'            => $isBlockUser,
            'blockByMeUser'            => $blockByMe,
            'giftListData'            => $giftListData,
            'userLikeData'            => $userLikeData
        ]);
    }

    /**
     * Process reset password request.
     *
     * @param array  $input
     * @param string $reminderToken
     *
     * @return array
     *---------------------------------------------------------------- */
    public function changeEmailProcess($input)
    {
        $email = $input['current_email'];

        //fetch active user by email
        $user = $this->userRepository->fetchActiveUserByEmail($email);

        // Check if user record exist
        if (__isEmpty($user)) {
            return  $this->engineReaction(18, ['show_message' => true], __tr('Invalid Request.'));
        }

        // Check if user entered correct password or not
        if (!Hash::check($input['current_password'], $user->password)) {
            return $this->engineReaction(3, [], __tr('Authentication Failed. Please Check Your Password.'));
        }

        // Check if user password updated
        if ($this->userRepository->updateUser($user, ['email' => $input['new_email']])) {
            return  $this->engineReaction(1, [
                'show_message' => true,
            ], __tr('Email updated successfully.'));
        }

        //failed response
        return  $this->engineReaction(2, ['show_message' => true], __tr('Email not updated.'));
    }

    /**
     * prepare profile details
     * @return array
     *---------------------------------------------------------------- */
    public function prepareProfileUpdate()
    {
        $user = $this->userRepository->fetchByID(getUserID());

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $userId = $user->_id;
        $userUid = $user->_uid;

        $basicInformation = $userSpecifications = $userSpecificationData = $locationInformation = [];

        // fetch User details
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        $profilePictureUrl = noThumbImageURL();
        $coverPictureUrl = noThumbCoverImageURL();
        $userSettingConfig = configItem('user_settings');
        $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $userUid]);
        $coverPictureFolderPath = getPathByKey('cover_photo', ['{_uid}' => $userUid]);

        // Check if user profile exists
        if (!__isEmpty($userProfile)) {
            if (!__isEmpty($userProfile->profile_picture)) {
                $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $userProfile->profile_picture);
            }
            if (!__isEmpty($userProfile->cover_picture)) {
                $coverPictureUrl = getMediaUrl($coverPictureFolderPath, $userProfile->cover_picture);
            }
        }

        $dob = isset($userProfile['dob']) ? formatDate($userProfile['dob'], "Y-m-d") : null;

        // Prepare user data
        $basicInformation = [
            'first_name'             => $user->first_name,
            'last_name'             => $user->last_name,
            'mobile_number'             => $user->mobile_number,
            'work_status'           => (string) isset($userProfile['work_status']) ? $userProfile['work_status'] : null,
            'gender'                   => (string) isset($userProfile['gender']) ? $userProfile['gender'] : null,
            'relationship_status'   => (string) isset($userProfile['relationship_status']) ? $userProfile['relationship_status'] : null,
            'preferred_language'    => (string) isset($userProfile['preferred_language']) ? $userProfile['preferred_language'] : null,
            'education'                => (string) isset($userProfile['education']) ? $userProfile['education'] : null,
            'birthday'              => $dob,
            'about_me'              => isset($userProfile['about_me']) ? $userProfile['about_me'] : null,
            'country'               => isset($userProfile['countries__id']) ? $userProfile['countries__id'] : null,
            'profile_picture'        => $profilePictureUrl,
            'cover_picture'            => $coverPictureUrl,
            'profileMediaRestriction' => getMediaRestriction('profile'),
            'coverImageMediaRestriction' => getMediaRestriction('cover_image')
        ];

        // Prepare user data
        $locationInformation = [
            'country'               => isset($userProfile['countries__id']) ? $userProfile['countries__id'] : null,
            'location_latitude'        => isset($userProfile['location_latitude']) ? floatval($userProfile['location_latitude']) : null,
            'location_longitude'    => isset($userProfile['location_longitude']) ? floatval($userProfile['location_longitude']) : null
        ];

        $specificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);

        // Check if user specifications exists
        if (!__isEmpty($specificationCollection)) {
            $userSpecifications = $specificationCollection->pluck('specification_value', 'specification_key')->toArray();
        }

        $specificationConfig = $this->getUserSpecificationConfig();

        foreach ($specificationConfig['groups'] as $specKey => $specification) {
            $items = [];

            foreach ($specification['items'] as $itemKey => $item) {
                $itemValue = '';
                $userSpecValue =  isset($userSpecifications[$itemKey])
                    ? $userSpecifications[$itemKey]
                    : '';
                if (!__isEmpty($userSpecValue)) {
                    $itemValue = isset($item['options'])
                        ? (isset($item['options'][$userSpecValue])
                            ? $item['options'][$userSpecValue] : '')
                        : $userSpecValue;
                }

                $items[] = [
                    'name'  => $itemKey,
                    'label'  => $item['name'],
                    'value' => $itemValue,
                    'options' => isset($item['options']) ? $item['options'] : '',
                    'selected_options' => $userSpecValue
                ];
            }

            // Check if Item exists
            if (!__isEmpty($items)) {
                $userSpecificationData[$specKey] = [
                    'title' => $specification['title'],
                    'items' => $items
                ];
            }
        }

        $allGenders = configItem('user_settings.gender');

        $genders = [];

        foreach ($allGenders as $key => $value) {
            $genders[] = [
                'id'     => $key,
                'value' => $value
            ];
        }

        return $this->engineReaction(1, [
            'basicInformation'         => $basicInformation,
            'locationInformation'    => $locationInformation,
            'specifications'        => (array) $userSpecificationData,
            'countries'             => $this->countryRepository->fetchAll()->toArray(),
            'user_settings'            => configItem('user_settings')
        ]);
    }

    /**
     * prepare featured users
     * @return array
     *---------------------------------------------------------------- */
    public function prepareFeaturedUsers()
    {
        return $this->engineReaction(1, [
            'getFeatureUserList'         => getFeatureUserList()
        ]);
    }

    /**
     * Prepare profile data
     *
     *---------------------------------------------------------------- */
    public function prepareBuildProfile()
    {   
        if (Session::has('userSignUpData')) {
            $userSignUpData = Session::get('userSignUpData');
            $userID = $userSignUpData['id'];
        } else {
            $userID = null;
        }
        $user = $this->userRepository->fetchByID($userID);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $userId = $user->_id;
        $userUid = $user->_uid;

        $basicInformation = $userSpecifications = $userSpecificationData = $locationInformation = [];

        // fetch User details
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        $specificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);

        // Check if user specifications exists
        if (!__isEmpty($specificationCollection)) {
            $userSpecifications = $specificationCollection->pluck('specification_value', 'specification_key')->toArray();
        }

        $specifications = [];
        $specificationConfig = $this->getUserSpecificationConfig();
        foreach ($specificationConfig['groups'] as $specKey => $specification) {
            $items = [];

            foreach ($specification['items'] as $itemKey => $item) {
                $specifications[$itemKey] = $item;
            }
        }

        $genders = configItem('user_settings.gender');

        $specificationData = [];
        $specificationConfig = $this->getUserSpecificationConfig();
        foreach ($specificationConfig['groups'] as $specKey => $specification) {
            $items = [];
            foreach ($specification['items'] as $itemKey => $item) {

                if (isset($item['frontend']) and ($item['frontend'] == false)) {
                    continue;
                }

                $multiple = (isset($item['multiple']) and ($item['multiple'] == true))
                            ? true
                            : false;

                $itemValue = '';
                $userSpecValue =  isset($userSpecifications[$itemKey])
                    ? $userSpecifications[$itemKey]
                    : '';
                if (!__isEmpty($userSpecValue)) {
                    $itemValue = isset($item['options'])
                        ? (isset($item['options'][$userSpecValue])
                            ? $item['options'][$userSpecValue] : '')
                        : $userSpecValue;
                }

                if ($item['input_type'] == 'dynamic') {
                    if ($itemKey == 'gender') {
                        $userSpecValue = $userProfile->gender;
                    } else if ($itemKey == 'dob') {
                        $userSpecValue = $userProfile->dob;
                    }
                }

                $items[] = [
                    'name'  => $itemKey,
                    'label'  => $item['name'],
                    'input_type' => $item['input_type'],
                    'value' => $itemValue,
                    'options' => isset($item['options']) ? $item['options'] : '',
                    'selected_options' => $userSpecValue,
                    'multiple' => (isset($item['multiple']) and ($item['multiple'] == true))
                        ? true
                        : false,
                ];
            }
            // Check if Item exists
            if (!__isEmpty($items)) {
                $specificationData[$specKey] = [
                    'title' => $specification['title'],
                    'icon' => $specification['icon'],
                    'items' => $items
                ];
            }
        }

        return $this->engineReaction(1, [
            'specifications'         => $specifications,
            'specificationData'         => $specificationData,
            'genders'         => $genders,
        ]);
    }

    /**
     * Process profile data
     *
     * @param array $inputData
     *
     *-----------------------------------------------------------------------*/
    public function processProfileBuild($inputData)
    {
        if (Session::has('userSignUpData')) {
            $userSignUpData = Session::get('userSignUpData');
            $userID = $userSignUpData['id'];
        } else {
            $userID = null;
        }

        $user = $this->userRepository->fetchByID($userID);

        // Check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $cityId = isset($inputData['city_id']) ? $inputData['city_id'] : 0;
        $userId = $user->_id;
        $isUpdated = false;

        
        ## Location info ##
        if (!__isEmpty($cityId)) {

            $cityData = $this->userSettingRepository->fetchCity($cityId);

            //check is empty then show error message
            if (!__isEmpty($cityData)) {
                $cityName = $cityData->name;
                // Fetch Country code
                $countryDetails = $this->countryRepository->fetchByCountryCode($cityData->country_code);

                //check is empty then show error message
                if (!__isEmpty($countryDetails)) {
                    
                    $countryId = $countryDetails->_id;
                    $countryName = $countryDetails->name;
                    $isUserLocationUpdated = false;

                    $userProfileDetails = [
                        'countries__id' => $countryId,
                        'city' => $cityName,
                        'location_latitude' => $cityData->latitude,
                        'location_longitude' => $cityData->longitude
                    ];
                    // get user profile
                    $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

                    
                    if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                        activityLog($user->first_name . ' ' . $user->last_name . ' added own location.');
                        $isUpdated = true;
                    }
                }
            }            
        }

        ## Profile Information ##
        // Prepare User profile details
        $dob = array_get($inputData, 'birthday');
        $dob = ($dob) ? $dob : '01/01/1970';
        $userProfileDetails = [
            'gender'                => array_get($inputData, 'gender'),
            'dob'                   => $dob,
        ];

        // get user profile
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        // update user profile
        if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
            
            // Adding activity log for update user
            activityLog($user->first_name . ' ' . $user->last_name . ' user profile info created.');

            $isUpdated = true;
        }


        ## Profile Settings ##
        
        $userSpecifications = $storeOrUpdateData = [];
        // Get collection of user specifications
        $userSpecificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);
        // check if user specification exists
        if (!__isEmpty($userSpecificationCollection)) {
            $userSpecifications = $userSpecificationCollection->pluck('_id', 'specification_key')->toArray();
        }

        $specificationConfig = $this->getUserSpecificationConfig();

        $index = 0;
        foreach ($inputData as $inputKey => $inputValue) {
            
            if (!__isEmpty($inputValue)) {

                $multiple = false;
                foreach ($specificationConfig['groups'] as $specKey => $specification) {
                    foreach ($specification['items'] as $itemKey => $item) {
                        if ($itemKey == $inputKey && (isset($item['multiple']) && $item['multiple'] == true)) {
                            $multiple == true;
                        }
                    }
                }

                if ($multiple == true) {
                    $inputValue = serialize($inputValue);
                }
                if($inputKey == 'kinks'){
                    $inputValue = implode(',',$inputValue);
                }
                $storeOrUpdateData[$index] = [
                    'type'                  => 1,
                    'status'                => 1,
                    'specification_key'     => $inputKey,
                    'specification_value'   => $inputValue,
                    'users__id'             => $userId
                ];
                if (array_key_exists($inputKey, $userSpecifications)) {
                    $storeOrUpdateData[$index]['_id'] = $userSpecifications[$inputKey];
                }
                $index++;
            }
        }
         /*   echo "<pre>";print_r($storeOrUpdateData);
             exit();*/
        // Check if user profile updated or store
        if ($this->userSettingRepository->storeOrUpdateUserSpecification($storeOrUpdateData, true)) {
            activityLog($user->first_name . ' ' . $user->last_name . ' created own user settings.');

            $isUpdated = true;
        }

        if ($isUpdated) {

            //Session::forget('userSignUpData');

            return $this->engineReaction(1, ['show_message' => false], __tr('Your account created successfully. Please wait for admin to activate the account'));
        }

        return $this->engineReaction(14, ['show_message' => true], __tr('Nothing created.'));
    }


    /**
     * Get plans list
     *
     *-----------------------------------------------------------------------*/
    public function fetchPlanByType()
    {
        if (Session::has('userSignUpData')) {
            $userSignUpData = Session::get('userSignUpData');
            $userID = $userSignUpData['id'];
        } else {
            $userID = null;
        }

        $user = $this->userRepository->fetchByID($userID);

        // Check if user exists
        if (__isEmpty($user)) {
            //return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $plans = $this->managePlansRepository->fetchList();

        $list = [];

        $free = null;

        foreach ($plans as $plan) {
            if (!isset($list[$plan['plan_type']])) {
                $list[$plan['plan_type']] = [];
            }
            if ($plan['price'] == 0) {
                $free = $plan;
            } else {
                $list[$plan['plan_type']][] = $plan; 
            }            
        }

        if ($free) {
            foreach ($list as $type => $plans) {
                array_unshift($plans, $free);
                $list[$type] = $plans;
            }
        }

        return $this->engineReaction(1, [
            'plans' => $list
        ]);
    }

    /**
     * Get plan
     *
     *-----------------------------------------------------------------------*/
    public function fetchPlan($planId)
    {
        if (Session::has('userSignUpData')) {
            $userSignUpData = Session::get('userSignUpData');
            $userID = $userSignUpData['id'];
        } else {
            $userID = null;
        }

        $user = $this->userRepository->fetchByID($userID);

        // Check if user exists
        if (__isEmpty($user)) {
            //return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $planCollection = $this->managePlansRepository->fetch($planId);

        //if is empty
        if (__isEmpty($planCollection)) {
            return $this->engineReaction(19, ['show_message' => false]);
        }

        $planData = [
            '_id'             => $planCollection['_id'],
            '_uid'             => $planCollection['_uid'],
            'title'         => $planCollection['title'],
            'price'         => $planCollection['price'],
            'plan_type'         => $planCollection['plan_type'],
            'description'     => $planCollection['content'],
            'created_at'     => formatDate($planCollection['created_at']),
            'updated_at'     => formatDate($planCollection['updated_at']),
            'status'         => $planCollection['status'],
        ];

        return $this->engineReaction(1, [
            'planData' => $planData
        ]);
    }




    /**
     * process plan
     *
     *-----------------------------------------------------------------------*/
    public function processPlan($planId)
    {
        if (Session::has('userSignUpData')) {
            $userSignUpData = Session::get('userSignUpData');
            $userID = $userSignUpData['id'];
        } else {
            $userID = null;
        }

        $user = $this->userRepository->fetchByID($userID);

        // Check if user exists
        if (__isEmpty($user)) {
            //return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $planCollection = $this->managePlansRepository->fetch($planId);

        $subscribe_plan_started = date("Y-m-d h:i:s");
        $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +3 months"));
        if ($planCollection['plan_type'] == 1) {
            $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +1 months"));
        }


        $PlansInsertDetail = array('_uid' => $planCollection['_uid'], 'created_at' => $planCollection['created_at']->toDateString(), 'updated_at' => $planCollection['updated_at']->toDateString(), 'status' => $planCollection['status'], 'users__id' => $userID,'expiry_at' => "", 'credit_wallet_transactions__id' => "", 'plan_id' => $planId);

       

        //$data  = UserSubscription::where('status', 1)->get();

        $subscription = new UserSubscription;
        $subscription->_uid = $planCollection['_uid'];
        $subscription->created_at = $subscribe_plan_started;
        $subscription->updated_at = $planCollection['updated_at']->toDateString();
        $subscription->status = $planCollection['status'];
        $subscription->users__id = $userID;
        $subscription->expiry_at = $subscribe_plan_expiry;
        $subscription->plan_id = $planId;
        $subscription->save();
            //echo "<pre>";print_r($subscription);exit();

        //if is empty
        if (__isEmpty($planCollection)) {
            return $this->engineReaction(19, ['show_message' => false]);
        }

        Session::forget('userSignUpData');
        
        return $this->engineReaction(1, ['show_message' => false], __tr('Your account created successfully. Please wait for admin to activate the account'));
    }

    /*cancel Subscription Plan Date*/
    public function updateSubscriptionPlanStatus($u_id){

            $UserSubscription = UserSubscription::where('users__id', $u_id)->where('status','1')->update(['status' => 0]);

            session()->put('nosubsciption_plan', 0);
            
            if ($UserSubscription == 1) {
                return $this->engineReaction(1, ['show_message' => true], __tr('Your subscription plan successfully canceled.'));
            }

    }

    /*cancel Subscription Plan Date*/
    public function updatePaymentPlanStatus($u_id){

            $UserSubscription = Payment::where('user_id', $u_id)->where('plan_active_status','1')->update(['plan_active_status' => 0]);
            
            if ($UserSubscription == 1) {
                return $this->engineReaction(1, ['show_message' => true], __tr('Your Payment plan successfully canceled.'));
            }

    }

     public function chnageProfileStatus($request_data,$u_id){

            $user_settings = DB::table('user_settings')->where('users__id',$u_id)->where('key_name',$request_data['profile_key'])->get()->toArray();
            
            $created_at = date("Y-m-d h:i:s");
            $updated_at = date("Y-m-d h:i:s");
            if (!empty($user_settings)) {
                $update_user_settings=  DB::table('user_settings')->where('key_name', $request_data['profile_key'])->where('users__id',$u_id)->update(['value' => $request_data['someone_match'],'updated_at' => $updated_at]);
                return $this->engineReaction(1, ['show_message' => true], __tr('Your setting saved successfully.'));
            }else{
                $insert_user_settings = DB::table('user_settings')->insert(['created_at' => $created_at,'updated_at' => $updated_at,'key_name' => $request_data['profile_key'],'value' => $request_data['someone_match'],'data_type'=>1,'users__id' => $u_id]);
                return $this->engineReaction(1, ['show_message' => true], __tr('Your setting updated successfully.'));
            } 

     }



    /*update Subscription Plan Date*/
    public function updateSubscriptionPlan($u_id,$user_current_plan,$update_array_plan_date,$renew_subscription){

               // echo "<pre>";print_r($renew_subscription);exit();
            $renew_subscription_update = 1;
            if ($renew_subscription == 1) {
                $renew_subscription_update = 0;
            }
            $UserSubscription = UserSubscription::where('users__id', $u_id)->update(['renewal_sts' => $renew_subscription_update]);

            if ($UserSubscription == 1) {
                return $this->engineReaction(1, ['show_message' => true], __tr('Your subscription plan successfully renew.'));
            }

    }

    /**
     * Initiate Chat on mutual like
     *
     *-----------------------------------------------------------------------*/
    private function initiateChat($userId) {

        $likeByUser = $this->userRepository->fetchMyLikeDataByUserId($userId);
        $likeByUser = $likeByUser->pluck('by_users__id')->toArray();

        if (in_array($userId, $likeByUser)) {
            $messageRequest = $this->messengerRepository->fetchMessageRequest($userId);

            // Check if message request not exists
            // Then store initial message request
            if (\__isEmpty($messageRequest)) {
                $initialMessageGeneratedUid = YesSecurity::generateUid();
                $isMessageRequestReceived = true;
                $initialMessageRequest = [
                    [
                        'status' => 1, // Sent
                        'message' => 'Message Request',
                        'type' => 10,
                        'from_users__id' => getUserID(),
                        'to_users__id' => $userId,
                        'users__id' => getUserID(),
                        'integrity_id' => $initialMessageGeneratedUid
                    ],
                    [
                        'status' => 1, // Sent
                        'message' => 'Message Request',
                        'type' => 10,
                        'from_users__id' => getUserID(),
                        'to_users__id' => $userId,
                        'users__id' => $userId,
                        'integrity_id' => $initialMessageGeneratedUid
                    ]
                ];
                // Initial message request store in DB
                $this->messengerRepository->storeMessage($initialMessageRequest);
            }
        }
    }

    /**
     * Initiate Chat on dislike
     *
     *-----------------------------------------------------------------------*/
    private function terminateChat($userId) {

        $messageCollection = $this->messengerRepository->fetchMessagesByUser($userId);

        // Loop over messages
        foreach ($messageCollection as $messageChatData) {
            if ($messageChatData->type == 2) {
                $messengerFolderPath = getPathByKey('messenger_file', ['{_uid}' => $messageChatData->_uid]);
                $this->mediaEngine->delete($messengerFolderPath, $messageChatData->message);
            }
            $this->messengerRepository->deleteMessage($messageChatData);
        }

        $userId = getUserID();
        $messageCollection = $this->messengerRepository->fetchMessagesByUser($userId);

        // Loop over messages
        foreach ($messageCollection as $messageChatData) {
            if ($messageChatData->type == 2) {
                $messengerFolderPath = getPathByKey('messenger_file', ['{_uid}' => $messageChatData->_uid]);
                $this->mediaEngine->delete($messengerFolderPath, $messageChatData->message);
            }
            $this->messengerRepository->deleteMessage($messageChatData);
        }

    }
}
