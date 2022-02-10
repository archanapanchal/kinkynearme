<?php
/**
* UserSettingEngine.php - Main component file
*
* This file is part of the UserSetting component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\UserSetting;

use Illuminate\Support\Str;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Support\Country\Repositories\CountryRepository;
use App\Yantrana\Components\UserSetting\Repositories\UserSettingRepository;
use App\Yantrana\Components\UserSetting\Interfaces\UserSettingEngineInterface;
use App\Yantrana\Components\User\Models\{
    User,
    UserProfile
};
use App\Yantrana\Components\UserSetting\Models\{
    UserPhotosModel,UserVideosModel
};
use App\Yantrana\Components\User\UserEngine;
use App\Yantrana\Components\Plans\Models\PlanModel;
use File;
class UserSettingEngine extends BaseEngine implements UserSettingEngineInterface
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var  UserSettingRepository $userSettingRepository - UserSetting Repository
     */
    protected $userEngine;
    protected $userSettingRepository;

    /**
     * @var  CountryRepository $countryRepository - Country Repository
     */
    protected $countryRepository;

    /**
     * @var  MediaEngine $mediaEngine - Media Engine
     */
    protected $mediaEngine;

    /**
     * Constructor
     *
     * @param  UserSettingRepository $userSettingRepository - UserSetting Repository
     * @param  CountryRepository $countryRepository - Country Repository
     * @param  MediaEngine $mediaEngine - Media Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(
        UserSettingRepository $userSettingRepository,
        CountryRepository $countryRepository,
        MediaEngine $mediaEngine,
        UserEngine $userEngine
    ) {
        $this->userSettingRepository    = $userSettingRepository;
        $this->countryRepository        = $countryRepository;
        $this->mediaEngine              = $mediaEngine;
        $this->userEngine = $userEngine;
    }

    /**
     * Prepare User Settings.
     *
     * @param string $pageType
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareUserSettings($pageType)
    {
        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(array_get($this->getUserSettingConfig(), "items.$pageType"));
        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('Invalid page type.'));
        }

        $userSettings = $dbUserSettings = [];
        // Check if default settings exists
        if (!__isEmpty($defaultSettings)) {
            // Get selected default settings
            $userSettingCollection = $this->userSettingRepository->fetchUserSettingByName(array_keys($defaultSettings));

            // check if configuration collection exists
            if (!__isEmpty($userSettingCollection)) {
                foreach ($userSettingCollection as $configuration) {
                    $dbUserSettings[$configuration->key_name] = $this->castValue($configuration->data_type, $configuration->value);
                }
            }

            // Loop over the default settings
            foreach ($defaultSettings as $defaultSetting) {
                $userSettings[$defaultSetting['key']] = $this->prepareDataForConfiguration($dbUserSettings, $defaultSetting);
            }
        }

        if ($pageType == 'notification') {
            $userSettings['user_choice_display_mobile_number'] = configItem('user_choice_display_mobile_number');
        }

        return $this->engineReaction(1, [
            'userSettingData' => $userSettings
        ]);
    }

    /**
     * Process User Setting Store.
     *
     * @param string $pageType
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUserSettingStore($pageType, $inputData)
    {
        $dataForStoreOrUpdate = $userSettingKeysForDelete = [];
        $isDataAddedOrUpdated = false;

        // Get settings from config
        $defaultSettings = $this->getDefaultSettings($this->getUserSettingConfig()['items'][$pageType]);

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('Invalid page type.'));
        }

        //check page type is notifications
        if ($pageType == 'notification') {
            if (!__isEmpty($inputData)) {
                foreach ($inputData as $key => $value) {
                    if ($key != 'display_user_mobile_number') {
                        $inputData[$key] = (isset($value) and $value == 'true') ? true : false;
                    }
                }
            }
        }

        // Check if input data exists
        if (!__isEmpty($inputData)) {
            foreach ($inputData as $inputKey => $inputValue) {
                // Get selected default settings
                $userSettingCollection = $this->userSettingRepository->fetchUserSettingByName(array_keys($defaultSettings));
                //pluck user setting value and key name
                $userSettingKeyName = $userSettingCollection->pluck('value', 'key_name')->toArray();

                // Check if default text and form text not same                
                if (array_key_exists($inputKey, $defaultSettings) and $inputValue != $defaultSettings[$inputKey]['default']) {
                    $castValues = $this->castValue(
                        ($defaultSettings[$inputKey]['data_type'] == 4)
                            ? 5 : $defaultSettings[$inputKey]['data_type'], // for Encode purpose only
                        $inputValue
                    );
                    //if data exists in configuration then use existing data
                    if (array_key_exists($inputKey, $userSettingKeyName)) {
                        foreach ($userSettingCollection as $key => $settings) {
                            if ($inputKey == $settings['key_name']) {
                                $dataForStoreOrUpdate[] = [
                                    '_id'            => $settings['_id'],
                                    'key_name'      => $inputKey,
                                    'value'         => $castValues,
                                    'data_type'     => $defaultSettings[$inputKey]['data_type'],
                                    'users__id'     => getUserID()
                                ];
                            }
                        }
                    } else {
                        $dataForStoreOrUpdate[] = [
                            'key_name'      => $inputKey,
                            'value'         => $castValues,
                            'data_type'     => $defaultSettings[$inputKey]['data_type'],
                            'users__id'     => getUserID()
                        ];
                    }
                }

                // Check if default value and input value same and it is exists
                if ((array_key_exists($inputKey, $defaultSettings))
                    and ($inputValue == $defaultSettings[$inputKey]['default'])
                    and (!isset($defaultSettings[$inputKey]['hide_value']))
                ) {
                    if (array_key_exists($inputKey, $userSettingKeyName)) {
                        foreach ($userSettingCollection as $key => $settings) {
                            if ($inputKey == $settings['key_name']) {
                                $userSettingKeysForDelete[] = $settings['_id'];
                            }
                        }
                    }
                }
            }

            // Send data for store or update
            if (
                !__isEmpty($dataForStoreOrUpdate)
                and $this->userSettingRepository->storeOrUpdate($dataForStoreOrUpdate)
            ) {
                activityLog('User settings stored / updated.');
                $isDataAddedOrUpdated = true;
            }

            // Check if deleted keys deleted successfully
            if (
                !__isEmpty($userSettingKeysForDelete)
                and $this->userSettingRepository->deleteUserSetting($userSettingKeysForDelete)
            ) {
                $isDataAddedOrUpdated = true;
            }

            // Check if data added / updated or deleted
            if ($isDataAddedOrUpdated) {
                return $this->engineReaction(1, ['show_message' => true], __tr('User setting updated successfully.'));
            }
            return $this->engineReaction(14, ['show_message' => true], __tr('Nothing updated.'));
        }
        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /*
      * Process Store User General Settings
      *
      * @param array $inputData
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public function processStoreUserBasicSettings($inputData)
    {
        $transactionResponse = $this->userSettingRepository->processTransaction(function () use ($inputData) {
            $isBasicSettingsUpdated = false;
            // Prepare User Details
            $userDetails = [
                'first_name' => $inputData['first_name'],
                'last_name'  => $inputData['last_name'],
                'mobile_number'   => $inputData['mobile_number']
            ];

            $userId = getUserID();
            $user = $this->userSettingRepository->fetchUserDetails($userId);
            // Check if user details exists
            if (\__isEmpty($userDetails)) {
                return $this->engineReaction(18, null, __tr('User does not exists.'));
            }
            // check if user details updated
            if ($this->userSettingRepository->updateUser($user, $userDetails)) {
                activityLog($user->first_name . ' ' . $user->last_name . ' update own user info.');
                $isBasicSettingsUpdated = true;
            }

            // Prepare User profile details
            $userProfileDetails = [
                'gender'                => array_get($inputData, 'gender'),
                'dob'                   => array_get($inputData, 'birthday'),
                'work_status'           => array_get($inputData, 'work_status'),
                'education'             => array_get($inputData, 'education'),
                'about_me'              => array_get($inputData, 'about_me'),
                'preferred_language'    => array_get($inputData, 'preferred_language'),
                'relationship_status'   => array_get($inputData, 'relationship_status')
            ];

            // get user profile
            $userProfile = $this->userSettingRepository->fetchUserProfile($userId);
            // check if user profile exists
            if (\__isEmpty($userProfile)) {
                $userProfileDetails['user_id'] = $userId;
                if ($this->userSettingRepository->storeUserProfile($userProfileDetails)) {
                    activityLog($user->first_name . ' ' . $user->last_name . ' store own user profile.');
                    $isBasicSettingsUpdated = true;
                }
            } else {
                if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                    activityLog($user->first_name . ' ' . $user->last_name . ' update own user profile.');
                    $isBasicSettingsUpdated = true;
                }
            }

            if ($isBasicSettingsUpdated) {
                return $this->userSettingRepository->transactionResponse(1, [], __tr('Your basic information updated successfully.'));
            }
            // // Send failed server error message
            return $this->userSettingRepository->transactionResponse(2, [], __tr('Something went wrong on server.'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /*
      * process Store Profile Wizard
      *
      * @param array $inputData
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public function processStoreProfileWizard($inputData)
    {
        $transactionResponse = $this->userSettingRepository->processTransaction(function () use ($inputData) {
            $isBasicSettingsUpdated = false;

            $userId = getUserID();
            $user = $this->userSettingRepository->fetchUserDetails($userId);
            // Check if user details exists
            if (\__isEmpty($user)) {
                return $this->engineReaction(18, null, __tr('User does not exists.'));
            }

            // Prepare User profile details
            $userProfileDetails = [
                'gender'                => array_get($inputData, 'gender'),
                'dob'                   => array_get($inputData, 'birthday'),
            ];

            // get user profile
            $userProfile = $this->userSettingRepository->fetchUserProfile($userId);
            // check if user profile exists
            if (\__isEmpty($userProfile)) {
                $userProfileDetails['user_id'] = $userId;
                if ($this->userSettingRepository->storeUserProfile($userProfileDetails)) {
                    activityLog($user->first_name . ' ' . $user->last_name . ' store own user profile.');
                    $isBasicSettingsUpdated = true;
                }
            } else {
                if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                    activityLog($user->first_name . ' ' . $user->last_name . ' update own user profile.');

                    $isBasicSettingsUpdated = true;
                }
            }

            if ($isBasicSettingsUpdated) {
                return $this->userSettingRepository->transactionResponse(1, [], __tr('Your basic information updated successfully.'));
            }
            // // Send failed server error message
            return $this->userSettingRepository->transactionResponse(2, [], __tr('Nothing to update'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process Store Location Data
     *
     * @param array $inputData
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processStoreLocationData($inputData)
    {
        // Get country from input data
        $placeData = $inputData['placeData'];
        // check if place data exists
        if (__isEmpty($placeData)) {
            return $this->engineReaction(2, null, __tr('Invalid data proceed.'));
        }

        $countryCode = $cityName = $countryName = '';
        // Loop over the place data
        foreach ($placeData as $place) {
            if (in_array('country', $place['types']) or in_array('continent', $place['types'])) {
                $countryCode = $place['short_name'];
                $countryName = $place['long_name'];
            }
            if (in_array('locality', $place['types'])) {
                $cityName = $place['long_name'];
            }
        }
        // Fetch Country code
        $countryDetails = $this->countryRepository->fetchByCountryCode($countryCode);

        //check is empty then show error message
        if (__isEmpty($countryDetails)) {
            return $this->engineReaction(18, null, __tr('Select your country level precise location.'));
        }

        $countryId = $countryDetails->_id;
        $countryName = $countryDetails->name;
        $isUserLocationUpdated = false;
        $userId = getUserID();
        $user = $this->userSettingRepository->fetchUserDetails($userId);
        // Check if user details exists
        if (\__isEmpty($user)) {
            return $this->engineReaction(18, null, __tr('User does not exists.'));
        }
        $userProfileDetails = [
            'countries__id' => $countryId,
            'city' => $cityName,
            'location_latitude' => $inputData['latitude'],
            'location_longitude' => $inputData['longitude']
        ];
        // get user profile
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        // check if user profile exists
        if (\__isEmpty($userProfile)) {
            $userProfileDetails['user_id'] = $userId;
            if ($this->userSettingRepository->storeUserProfile($userProfileDetails)) {
                activityLog($user->first_name . ' ' . $user->last_name . ' store own location.');
                $isUserLocationUpdated = true;
            }
        } else {
            if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                activityLog($user->first_name . ' ' . $user->last_name . ' update own location.');
                $isUserLocationUpdated = true;
            }
        }

        // check if user profile stored or update
        if ($isUserLocationUpdated) {
            return $this->engineReaction(1, [
                'country_name' => $countryName,
                'city' => $cityName
            ], __tr('Location stored successfully.'));
        }

        return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
    }


    public function processDeleteProfileImage($inputData, $requestType)
    {
        $UserProfiledata =  User::where('_id',getUserID())->get()->toArray();
        $profileimage_detail = UserProfile::where('users__id', $UserProfiledata[0]['_id'])->first()->toArray();
           // echo "<pre>";print_r();exit();
        if (!empty($profileimage_detail)) {
             $delete_profile =  UserProfile::where('users__id', $profileimage_detail['users__id'])->update(array('profile_picture' => ''));
        }

        return $this->engineReaction(1, [], __tr('Profile picture deleted successfully.'));
            
    }

    public function processDeletePortfolioImage($image_name, $requestType){
        $userData =  User::where('_id',getUserID())->get()->toArray();
        $photo_detail = UserPhotosModel::where('users__id', getUserID())->where('file',$image_name)->get();
       //    echo "<pre>"; print_r($photo_detail); exit;
        if(count($photo_detail) != 0 )
        {
            $photo_collection_id = $photo_detail[0]->_id;

            $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => getUserID()]);
            // Get user photos collection
            $userPhotosCollection = UserPhotosModel::where('_id',$photo_collection_id)->first()->toArray();
            //$images_url = getMediaUrl($userPhotosFolderPath);
           
            $images_url = public_path('media-storage/users/'.getUserID().'/photos/'.$userPhotosCollection['file']);
            
            if(File::exists($images_url)){
                echo "s";
                    unlink($images_url);
                }
           
            $delete_photo = UserPhotosModel::where('_id',$photo_collection_id)->delete();
        }
        return $this->engineReaction(1, [], __tr('Portfolio picture deleted successfully.'));
    }



    /**
     * Process upload profile image.
     *
     * @param array $inputData
     * @param string $requestType
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUploadProfileImage($inputData, $requestType)
    {

        if (isset($inputData['id'])) {
            $userId = $inputData['id'];
            $user = $this->userSettingRepository->fetchUserDetails($userId);

            // check if user details exists
            if (__isEmpty($user)) {
                return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
            }

            $uid = $user->_uid;
            $fullName = $user->first_name . ' ' . $user->last_name;
            $inputData['uid'] = $uid;
        } else {
            $userId = getUserID();
            $uid = authUID();
            $userInfo = getUserAuthInfo();
            $fullName = array_get($userInfo, 'profile.full_name');
        }

        $uploadedFile = $this->mediaEngine->processUploadProfile($inputData, $requestType);
        $isProfilePictureUpdated = false;
        // check if file uploaded successfully
        if ($uploadedFile['reaction_code'] == 1) {
            $uploadedFileData = $uploadedFile['data'];
            $fileName = $uploadedFileData['fileName'];

            // get user profile data
            $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

            $userProfileData = [
                'profile_picture' => $fileName
            ];

            // check if user profile exists
            if (__isEmpty($userProfile)) {
                $userProfileData['user_id'] = $userId;
                // Check if user profile stored
                if ($this->userSettingRepository->storeUserProfile($userProfileData)) {
                    activityLog($fullName . ' store profile picture.');
                    $isProfilePictureUpdated = true;
                }
            } else {
                // check if existing profile picture exists
                if (!__isEmpty($userProfile->profile_picture)) {
                    $profileFolderPath = getPathByKey('profile_photo', ['{_uid}' => $uid]);
                    $this->mediaEngine->delete($profileFolderPath, $userProfile->profile_picture);
                }

                // Check if user profile updated
                if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileData)) {
                    activityLog($fullName . ' update profile picture.');
                    $isProfilePictureUpdated = true;
                }
            }
        }
        // check if profile picture updated successfully.
        if ($isProfilePictureUpdated) {
            return $this->engineReaction(1, [
                'image_url' => $uploadedFileData['path']
            ], __tr('Profile picture updated successfully.'));
        }

        return $uploadedFile;
    }

    /**
     * Process upload cover image.
     *
     * @param array $inputData
     * @param string $requestType
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUploadCoverImage($inputData, $requestType)
    {
        if (isset($inputData['id'])) {
            $userId = $inputData['id'];
            $user = $this->userSettingRepository->fetchUserDetails($userId);

            // check if user details exists
            if (__isEmpty($user)) {
                return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
            }

            $uid = $user->_uid;
            $fullName = $user->first_name . ' ' . $user->last_name;
            $inputData['uid'] = $uid;
        } else {
            $userId = getUserID();
            $uid = authUID();
            $userInfo = getUserAuthInfo();
            $fullName = array_get($userInfo, 'profile.full_name');
        }

        $uploadedFile = $this->mediaEngine->processUploadCoverPhoto($inputData, $requestType);
        $isCoverPictureUpdated = false;
        // check if file uploaded successfully
        if ($uploadedFile['reaction_code'] == 1) {
            $uploadedFileData = $uploadedFile['data'];
            $fileName = $uploadedFileData['fileName'];
            
            // get user profile data
            $userProfile = $this->userSettingRepository->fetchUserProfile($userId);
            $userProfileData = [
                'cover_picture' => $fileName
            ];

            // check if user profile exists
            if (__isEmpty($userProfile)) {
                $userProfileData['user_id'] = $userId;
                // Check if user profile stored
                if ($this->userSettingRepository->storeUserProfile($userProfileData)) {
                    activityLog($fullName . ' store cover picture.');
                    $isCoverPictureUpdated = true;
                }
            } else {

                // check if existing profile picture exists
                if (!__isEmpty($userProfile->cover_picture)) {
                    $profileFolderPath = getPathByKey('cover_photo', ['{_uid}' => $uid]);

                    $this->mediaEngine->delete($profileFolderPath, $userProfile->cover_picture);
                }
                // Check if user profile updated
                if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileData)) {
                    activityLog($fullName . ' update cover picture.');
                    $isCoverPictureUpdated = true;
                }
            }
        }
        // check if cover picture updated successfully.
        if ($isCoverPictureUpdated) {
            return $this->engineReaction(1, [
                'image_url' => $uploadedFileData['path']
            ], __tr('Profile cover picture updated successfully.'));
        }

        return $uploadedFile;
    }

    /*
      * Process Store User Profile Data
      *
      * @param array $inputData
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public function processStoreUserProfileSetting($inputData)
    {
        $userId = getUserID();

        if (isset($inputData['users__id'])) {
            $userId = $inputData['users__id'];
        }
        
        $userSpecifications = $storeOrUpdateData = [];
        // Get collection of user specifications
        $userSpecificationCollection = $this->userSettingRepository->fetchUserSpecificationById($userId);
        // check if user specification exists
        if (!__isEmpty($userSpecificationCollection)) {
            $userSpecifications = $userSpecificationCollection->pluck('_id', 'specification_key')->toArray();
        }

        $index = 0;
        foreach ($inputData as $inputKey => $inputValue) {
            if (!__isEmpty($inputValue)) {
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

        // Check if user profile updated or store
        if ($this->userSettingRepository->storeOrUpdateUserSpecification($storeOrUpdateData)) {
            $userInfo = getUserAuthInfo();
            $fullName = array_get($userInfo, 'profile.full_name');
            activityLog($fullName . ' update own user settings.');
            return $this->engineReaction(1, null, __tr('Profile updated successfully.'));
        }

        return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
    }

    /**
     * Prepare user photo settings.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function prepareUserPhotosSettings()
    {
        $userPhotoCollection = $this->userSettingRepository->fetchUserPhotos(getUserID());
        $userPhotos = [];
        $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => authUID()]);
        // check if user photos exists
        if (!__isEmpty($userPhotoCollection)) {
            foreach ($userPhotoCollection as $photo) {
                $removePhotoUrl = route('user.upload_photos.write.delete', ['photoUid' => $photo->_uid]);
                //if mobile request
                if (isMobileAppRequest()) {
                    $removePhotoUrl = route('api.user.upload_photos.write.delete', ['photoUid' => $photo->_uid]);
                }

                $userPhotos[] = [
                    '_id' => $photo->_id,
                    '_uid' => $photo->_uid,
                    'removePhotoUrl' => $removePhotoUrl,
                    'image_url' => getMediaUrl($userPhotosFolderPath, $photo->file)
                ];
            }
        }
        return $this->engineReaction(1, [
            'userPhotos' => $userPhotos,
            'photosCount' => $userPhotoCollection->count(),
            'photosMediaRestriction' => getMediaRestriction('photos')
        ]);
    }

    /**
     * Process Upload Multiple Photos.
     *
     * @param array $inputData
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUploadPhotos($inputData,$userid)
    {
            
        $userPhotoCollection = $this->userSettingRepository->fetchUserPhotos($userid);
        $userDetails = User::where('_id', $userid)->first();
           

        // Check if user photos count is greater than or equal to 10
        if ($userPhotoCollection->count() >= getStoreSettings('user_photo_restriction')) {
            
            return $this->engineReaction(2, null, __tr("You cannot upload more than __limit__ photos.", ['__limit__' => getStoreSettings('user_photo_restriction')]));
        }
        
        $uploadedFile = $this->mediaEngine->uploadUserPhotos($inputData, 'photos',$userid);

        // check if file uploaded successfully
        if ($uploadedFile['reaction_code'] == 1) {
            $userPhotoData = [
                'users__id' => $userid,
                'file' => $uploadedFile['data']['fileName']
            ];

            if ($newUserPhoto = $this->userSettingRepository->storeUserPhoto($userPhotoData)) {
                $userInfo = getUserAuthInfo();
                $fullName = array_get($userInfo, 'profile.full_name');
                activityLog($fullName . ' upload new photos.');
                return $this->engineReaction(1, [
                    'stored_photo' => [
                        '_id' => $newUserPhoto->_id,
                        '_uid' => $newUserPhoto->_uid,
                        'removePhotoUrl' => route('user.upload_photos.write.delete', ['photoUid' => $newUserPhoto->_uid,'userId' => $userid]),
                        'image_url' => $uploadedFile['data']['path']
                    ]
                ], __tr('Photos uploaded successfully.'));
            }
        }

            
        return $uploadedFile;
    }
    public function processUploadPhotosFront($inputData)
    {
        $userPhotoCollection = $this->userSettingRepository->fetchUserPhotos(getUserID());

        // Check if user photos count is greater than or equal to 10

        $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
    

        $plan_id ="";
        $config_detail = "";
        $title ="";
        if (!empty($UserSubscription_plan)) {
            $plan_id = $UserSubscription_plan->plan_id;
            $plan_detail = PlanModel::where('_id', $plan_id)->first();
            $plan_type = $plan_detail->plan_type;
            $title = $plan_detail->title;

            $title_lower = strtolower($title);
            $replace_title = str_replace(' ', '_', $title_lower);
            $final_title = $replace_title.'_'.$plan_type;

            $config_detail = getStoreSettings('user_photo_restriction_'.$final_title);

        }
            
            

        if ($title != "Platinum") {
            
            if ($userPhotoCollection->count() >= $config_detail) {
                return $this->engineReaction(2, null, __tr("You cannot upload more than __limit__ photos.", ['__limit__' => $config_detail]));
            }
        }


        $uploadedFile = $this->mediaEngine->uploadUserPhotos($inputData, 'photos',getUserID());
           // echo "<pre>";print_r($uploadedFile);exit();

        // check if file uploaded successfully
        if ($uploadedFile['reaction_code'] == 1) {
            $userPhotoData = [
                'users__id' => getUserID(),
                'file' => $uploadedFile['data']['fileName']
            ];

            if ($newUserPhoto = $this->userSettingRepository->storeUserPhoto($userPhotoData)) {
                $userInfo = getUserAuthInfo();
                $fullName = array_get($userInfo, 'profile.full_name');
                activityLog($fullName . ' upload new photos.');
                return $this->engineReaction(1, [
                    'stored_photo' => [
                        '_id' => $newUserPhoto->_id,
                        '_uid' => $newUserPhoto->_uid,
                        'removePhotoUrl' => route('user.upload_photos.write.delete', ['photoUid' => $newUserPhoto->_uid]),
                        'image_url' => $uploadedFile['data']['path']
                    ]
                ], __tr('Photos uploaded successfully.'));
            }
        }

        return $uploadedFile;
    }

    public function processUploadVideo($inputData)
    {


        $userVideoCollection = $this->userSettingRepository->fetchUserVideos(getUserID());


        $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
           

        $plan_id ="";
        $config_detail = "";
        $title ="";
        if (!empty($UserSubscription_plan)) {
            $plan_id = $UserSubscription_plan->plan_id;
            $plan_detail = PlanModel::where('_id', $plan_id)->first();
            $plan_type = $plan_detail->plan_type;
            $title = $plan_detail->title;

            $title_lower = strtolower($title);
            $replace_title = str_replace(' ', '_', $title_lower);
            $final_title = $replace_title.'_'.$plan_type;
            $config_detail = getStoreSettings('user_video_restriction_'.$final_title);

        }

           // echo "<pre>";print_r($config_detail);exit();

        // Check if user photos count is greater than or equal to 10
        if ($userVideoCollection->count() >= $config_detail) {

             $data['success'] = 2;
             $data['message'] = 'You cannot upload video more than '.$config_detail.' photos!';

             return response()->json($data);
            
        }
            if (!empty($_FILES)) {  

                ini_set('post_max_size', '50M');
                ini_set('upload_max_filesize', '50M');
                ini_set('max_execution_time', '3600');
                ini_set('max_input_time', '3600');
                ini_set('memory_limit', '256M');
                
                $file_type = $_FILES['file']['type'];
                if (($file_type == "video/mov") || ($file_type == "video/mp4") || ($file_type == "video/wmv") || ($file_type == "video/avi") || ($file_type == "video/mkv"))
                {
                    
                  

                if ($_FILES["file"]["size"] > 30000000) {
                    $data['success'] = 2;
                     $data['message'] = "You cannot upload more than 30 MB.";
                     return response()->json($data);
                }



                $filename = $_FILES['file']['name'];
                $store_filename = time().$filename;
                //$path = public_path().'/videos/';

                $target_dir = public_path()."/videos/";
                $target_file = $target_dir . basename($store_filename);

                $move =  move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);


                $video_detail = array('file' => $store_filename,
                            'exicute_url' => url('/videos').'/'.$store_filename,
                            'users_id' => getUserID(),
                            'status' => 1
                                );

                $uservideomodel = new UserVideosModel;
                $uservideomodel->users_id = $video_detail['users_id'];
                $uservideomodel->file = $video_detail['file'];
                $uservideomodel->exicute_url = $video_detail['exicute_url'];
                $uservideomodel->status = $video_detail['status'];
                $uservideomodel->created_at = now();
                $uservideomodel->updated_at = now();
                $uservideomodel->save();
            }else{
                $data['success'] = 2;
                     $data['message'] = 'You cannot upload '.$file_type.' file!';

                     return response()->json($data);
                
            }

            }

            $data['success'] = 1;
             $data['message'] = 'Your Video Uploaded Successfully!';

       
        return response()->json($data);
    }

    /**
     * Process delete user photos.
     *
     * @param array $photoUid
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processDeleteUserPhotos($photoUid,$Uid)
    {
        $userPhotos = $this->userSettingRepository->fetchUserPhotosById($photoUid,$Uid);

        //check is not empty
        if (__isEmpty($userPhotos)) {
            return $this->engineReaction(2, null, __tr("User photo not found."));
        }

        //delete photo
        if ($photo = $this->userSettingRepository->deletePhoto($userPhotos)) {
            //delete photo from user photos in media storage
            $photoImageFolderPath = getPathByKey('user_photos', ['{_uid}' => $Uid]);
            // Get user photos collection
            $image_url = getMediaUrl($photoImageFolderPath, $photo->file);
            $userprofile_img = storage_path('app/'.$image_url);
            if(File::exists($userprofile_img)){
                   unlink($userprofile_img);
            }
            
            //delete photo from media storage
            $this->mediaEngine->delete($photoImageFolderPath, $photo->file);

            //success response
            return $this->engineReaction(1, [
                'show_message' => true,
                'photoUid' => $photo->_uid
            ], __tr('Photo deleted successfully.'));
        }

        //error response
        return $this->engineReaction(18, null, __tr('Gift not deleted.'));
    }

    public function processDeleteUserVideo($video_id){

            $user_id = getUserID();
            $user_video = UserVideosModel::where('id',$video_id)->where('users_id',$user_id)->first();
            //echo "<pre>"; print_r($user_video); exit;
            if (!empty($user_video)) {
                // code...
                $video_url = public_path('videos/'.$user_video['file']);
                if(File::exists($video_url)){
                    unlink($video_url);
                }
                $delete = UserVideosModel::where('id',$video_id)->where('users_id',$user_id)->delete();
            }
            return $this->engineReaction(1, null, __tr('Video deleted successfully!.'));
    }


    /**
     * Process delete user photos.
     *
     * @param array $searchQuery
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function searchStaticCities($searchQuery)
    {
        if (!$searchQuery or !is_string($searchQuery)) {
            return $this->engineReaction(2, null, __tr("Search query should be valid string"));
        }

        if (Str::length($searchQuery) < 2) {
            return $this->engineReaction(2, null, __tr("At least 2 characters are required"));
        }

        $searchQueryResult = $this->userSettingRepository->searchStaticCities($searchQuery);
        // dd( $searchQueryResult);
        return $this->engineReaction(1, [
            'show_message' => false,
            'search_result' => $searchQueryResult
        ], __tr('Search result'));
    }

    /**
     * Process Store Location City
     *
     * @param array $inputData
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processStoreCity($cityId)
    {

        $cityData = $this->userSettingRepository->fetchCity($cityId);

        //check is empty then show error message
        if (__isEmpty($cityData)) {
            return $this->engineReaction(18, null, __tr('Selected city not found'));
        }

        $cityName = $cityData->name;
        // Fetch Country code
        $countryDetails = $this->countryRepository->fetchByCountryCode($cityData->country_code);

        //check is empty then show error message
        if (__isEmpty($countryDetails)) {
            return $this->engineReaction(18, null, __tr('Country not found'));
        }

        $countryId = $countryDetails->_id;
        $countryName = $countryDetails->name;
        $isUserLocationUpdated = false;
        $userId = getUserID();
        $user = $this->userSettingRepository->fetchUserDetails($userId);
        // Check if user details exists
        if (\__isEmpty($user)) {
            return $this->engineReaction(18, null, __tr('User does not exists.'));
        }
        $userProfileDetails = [
            'countries__id' => $countryId,
            'city' => $cityName,
            'location_latitude' => $cityData->latitude,
            'location_longitude' => $cityData->longitude
        ];
        // get user profile
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        // check if user profile exists
        if (\__isEmpty($userProfile)) {
            $userProfileDetails['user_id'] = $userId;
            if ($this->userSettingRepository->storeUserProfile($userProfileDetails)) {
                activityLog($user->first_name . ' ' . $user->last_name . ' store own location.');
                $isUserLocationUpdated = true;
            }
        } else {
            if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                activityLog($user->first_name . ' ' . $user->last_name . ' update own location.');
                $isUserLocationUpdated = true;
            }
        }

        // check if user profile stored or update
        if ($isUserLocationUpdated) {
            return $this->engineReaction(1, [
                'country_name' => $countryName,
                'city' => $cityName
            ], __tr('Location stored successfully.'));
        }

        return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
    }
}
