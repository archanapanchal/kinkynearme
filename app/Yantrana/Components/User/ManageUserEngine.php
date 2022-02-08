<?php

/**
* ManageUserEngine.php - Main component file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\User\Repositories\{ManageUserRepository, CreditWalletRepository};
use App\Yantrana\Support\Country\Repositories\CountryRepository;
use App\Yantrana\Components\UserSetting\Repositories\UserSettingRepository;
use App\Yantrana\Components\User\Repositories\UserRepository;
use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\Item\Repositories\ManageItemRepository;

class ManageUserEngine extends BaseEngine
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var  ManageUserRepository $manageUserRepository - ManageUser Repository
     */
    protected $manageUserRepository;

    /**
     * @var  CountryRepository $countryRepository - Country Repository
     */
    protected $countryRepository;

    /**
     * @var  Faker $faker - Faker
     */
    protected $faker;

    /**
     * @var  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     */
    protected $creditWalletRepository;

    /**
     * @var  MediaEngine $mediaEngine - MediaEngine
     */
    protected $mediaEngine;

    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * @var  UserSettingRepository $userSettingRepository - UserSetting Repository
     */
    protected $userSettingRepository;

    /**
     * @var ManageItemRepository - ManageItem Repository
     */
    protected $manageItemRepository;

    /**
     * Constructor
     *
     * @param  ManageUserRepository $manageUserRepository - ManageUser Repository
     * @param  CountryRepository $countryRepository - Country Repository
     * @param  Faker $faker - Faker
     * @param  MediaEngine $mediaEngine - MediaEngine
     * @param  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(ManageUserRepository $manageUserRepository, CountryRepository $countryRepository, Faker $faker, CreditWalletRepository $creditWalletRepository, MediaEngine $mediaEngine, UserRepository $userRepository, UserSettingRepository $userSettingRepository, ManageItemRepository $manageItemRepository)
    {
        $this->manageUserRepository     = $manageUserRepository;
        $this->countryRepository         = $countryRepository;
        $this->faker                     = $faker;
        $this->creditWalletRepository     = $creditWalletRepository;
        $this->mediaEngine                 = $mediaEngine;
        $this->userRepository                = $userRepository;
        $this->userSettingRepository        = $userSettingRepository;
        $this->manageItemRepository         = $manageItemRepository;
    }

    /**
     * Prepare User Data table list.
     *
     * @param int $status
     *
     *---------------------------------------------------------------- */
    public function prepareUsersDataTableList($status)
    {
        $userCollection = $this->manageUserRepository->fetchUsersDataTableSource($status);

        $requireColumns = [
            '_id',
            '_uid',
            'first_name',
            'last_name',
            'full_name',
            'created_at' => function ($key) {
                return formatDate($key['created_at']);
            },
            'status',
            'email',
            'username',
            'is_fake',
            'is_verified' => function ($key) {

                if (isset($key['is_verified']) and $key['is_verified'] == 1) {
                    return true;
                }
                return false;
            },
            'is_premium_user' => function ($key) {
                return isPremiumUser($key['_id']);
            },
            'dob' => function ($key) {
                //check is not empty
                if (!__isEmpty($key['dob'])) {
                    return $key['dob'];
                }

                return '-';
            },
            'gender',
            'city',
            'country',
            'location',
            'formattedGender' => function ($key) {
                //check is not empty
                if (!__isEmpty($key['gender'])) {
                    return configItem('user_settings.gender', $key['gender']);
                }

                return '-';
            },
            'registered_via' => function ($key) {
                if (!__isEmpty($key['registered_via'])) {
                    return $key['registered_via'];
                }
                return '-';
            },
            'profile_picture' => function ($key) {

                if (isset($key['profile_picture']) and !__isEmpty($key['profile_picture'])) {
                    $imagePath = getPathByKey('profile_photo', ['{_uid}' => $key['_uid']]);
                    return getMediaUrl($imagePath, $key['profile_picture']);
                }

                return noThumbImageURL();
            },
            'profile_url' => function ($key) {
                return route('user.profile_view', ['username' => $key['username']]);
            },
            'user_roles__id'
        ];

        return $this->dataTableResponse($userCollection, $requireColumns);
    }

    /**
     * Prepare User photos Data table list.
     *
     * @param int $status
     *
     *---------------------------------------------------------------- */
    public function userPhotosDataTableList()
    {
        $userCollection = $this->manageUserRepository->fetchUserPhotos();

        $requireColumns = [
            '_id',
            '_uid',
            'first_name',
            'last_name',
            'full_name',
            'profile_image' => function ($key) {

                if (isset($key['image_name'])) {
                    $path = getPathByKey('user_photos', ['{_uid}' => $key['_uid']]);
                    return getMediaUrl($path, $key['image_name']);
                } else if (isset($key['profile_picture'])) {
                    $path = getPathByKey('profile_photo', ['{_uid}' => $key['_uid']]);
                    return getMediaUrl($path, $key['profile_picture']);
                } else if (isset($key['cover_picture'])) {
                    $path = getPathByKey('cover_photo', ['{_uid}' => $key['_uid']]);
                    return getMediaUrl($path, $key['cover_picture']);
                }

                return null;
            },
            'updated_at' => function ($key) {
                return formatDate($key['updated_at'], "l jS F Y g:i A");
            },
            'type' => function ($key) {
                if (isset($key['image_name'])) {
                    return 'photo';
                } else if (isset($key['profile_picture'])) {
                    return 'profile';
                } else if (isset($key['cover_picture'])) {
                    return 'cover';
                }
                return null;
            },
            'profile_url' => function ($key) {
                return route('user.profile_view', ['username' => $key['username']]);
            },
            "deleteImageUrl" => function ($key) {

                if (isset($key['image_name'])) {
                    return route('manage.user.write.photo_delete', [
                        'userUid' => $key['_uid'],
                        'type' => 'photo',
                        'profileOrPhotoUid' => $key['user_photo_id']
                    ]);
                } else if (isset($key['profile_picture'])) {
                    return route('manage.user.write.photo_delete', [
                        'userUid' => $key['_uid'],
                        'type' => 'profile',
                        'profileOrPhotoUid' => $key['user_profile_id']
                    ]);
                } else if (isset($key['cover_picture'])) {
                    return route('manage.user.write.photo_delete', [
                        'userUid' => $key['_uid'],
                        'type' => 'cover',
                        'profileOrPhotoUid' => $key['user_profile_id']
                    ]);
                }
            }
        ];

        return $this->dataTableResponse($userCollection, $requireColumns);
    }

    /**
     * Prepare User List.
     *
     * @param int $status
     *
     *---------------------------------------------------------------- */
    public function prepareUserList($status)
    {
        $userCollection = $this->manageUserRepository->fetchList($status);
        $userData = [];
        // check if user collection exists
        if (!__isEmpty($userCollection)) {
            foreach ($userCollection as $user) {
                $userData[] = [
                    'uid'   => $user->_uid,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'created_on' => formatDate($user->created_at),
                ];
            }
        }
        return $this->engineReaction(1, [
            'userData' => $userData
        ]);
    }

    /**
     * Prepare User List.
     *
     * @param array $inputData
     *
     *---------------------------------------------------------------- */
    public function processAddUser($inputData)
    {
        $transactionResponse = $this->manageUserRepository->processTransaction(function () use ($inputData) {
            // Store user
            $newUser = $this->manageUserRepository->storeUser($inputData);
            // Check if user not stored successfully
            if (!$newUser) {
                return $this->manageUserRepository->transactionResponse(2, ['show_message' => true], __tr('User not added.'));
            }
            $userAuthorityData = [
                'user_id' => $newUser->_id,
                'user_roles__id' => 2
            ];
            // Add user authority
            if ($this->manageUserRepository->storeUserAuthority($userAuthorityData)) {
                return $this->manageUserRepository->transactionResponse(1, ['show_message' => true], __tr('User added successfully.'));
            }
            // Send failed server error message
            return $this->manageUserRepository->transactionResponse(2, ['show_message' => true], __tr('Something went wrong on server.'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Prepare User edit data.
     *
     * @param array $userUid
     *
     *---------------------------------------------------------------- */
    public function prepareUserEditData($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $userId = $userDetails->_id;
        $userUid = $userDetails->_uid;

        
        //$isOwnProfile = ($userDetails == getUserID()) ? true : false;
        $isOwnProfile = true;

        $userData = [
            'id'                => $userDetails->_id,
            'uid'               => $userDetails->_uid,
            'first_name'        => $userDetails->first_name,
            'last_name'         => $userDetails->last_name,
            'email'             => $userDetails->email,
            'username'          => $userDetails->username,
            'password'          => $userDetails->password,
            'confirm_password'  => $userDetails->confirm_password,
            'designation'       => $userDetails->designation,
            'mobile_number'     => $userDetails->mobile_number,
            'status'            => $userDetails->status
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

        $peopleILikes = $peopleIDislikes = $mutualLikeCollection = $peopleIFavourites = $blockUserCollection = [];
        // check if user profile exists
        if (!\__isEmpty($userProfile)) {
            // Get country name
            $countryName = '';
            if (!__isEmpty($userProfile->countries__id)) {
                $country = $this->countryRepository->fetchById($userProfile->countries__id, ['name']);
                $countryName = $country->name;
            }


            ## All Users Specifications Start ##

//            $allSpecificationCollection = $this->userSettingRepository->fetchAllUsersSpecification()->toArray();
            $allSpecificationCollection = $this->userSettingRepository->fetchAllUsersSpecification($userProfile->users__id)->toArray();

            $allUsersSpecifications = [];
            if (!\__isEmpty($allSpecificationCollection)) {
                foreach ($allSpecificationCollection as $key => $specification) {
                    $allUsersSpecifications[$specification['users__id']][$specification['specification_key']] = $specification['specification_value'];
                }
            }

            ## All Users Specifications End ##

            ## User likes Start ##
            $peopleILikes = $this->userRepository->fetchUserLikeData(1, false, $userDetails->_id)->toArray();
            $peopleILikes = $this->formatUsers($userDetails->_id, $peopleILikes, $allUsersSpecifications);

            $peopleWhoLikesMe = $this->userRepository->fetchUserLikeMeData(false, $userDetails->_id)->toArray();
            $peopleWhoLikesMe = $this->formatUsers($userDetails->_id, $peopleWhoLikesMe, $allUsersSpecifications);

            ## User likes End ##


            ## User matches Start ##

            //fetch user liked data by to user id
            $likedCollection = $this->userRepository->fetchUserLikeData(1, false, $userDetails->_id);
            //pluck people like ids
            $peopleLikeUserIds = $likedCollection->pluck('to_users__id')->toArray();
            //get people likes me data
            $userLikedMeData = $this->userRepository->fetchUserLikeMeData(false, $userDetails->_id)->whereIn('by_users__id', $peopleLikeUserIds);
            //pluck people like me ids
            $mutualLikeIds = $userLikedMeData->pluck('_id')->toArray();
            //get mutual like data
            $mutualLikeCollection = $this->userRepository->fetchMutualLikeUserData($mutualLikeIds, false, $userDetails->_id)->toArray();
            $mutualLikeCollection = $this->formatUsers($userDetails->_id, $mutualLikeCollection, $allUsersSpecifications);

            ## User matches End ##


            ## User favourites Start ##

            $peopleIFavourites = $this->userRepository->fetchUserFavouriteData(1, false, $userDetails->_id)->toArray();
            $peopleIFavourites = $this->formatUsers($userDetails->_id, $peopleIFavourites, $allUsersSpecifications);

            $peopleWhoFavouritesMe = $this->userRepository->fetchUserFavouriteMeData(false, $userDetails->_id)->toArray();
            $peopleWhoFavouritesMe = $this->formatUsers($userDetails->_id, $peopleWhoFavouritesMe, $allUsersSpecifications);

            ## User favourites End ##


            ## Blocked Users Start ##

            $blockUserCollection = $this->userRepository->fetchAllBlockUser(false, $userDetails->_id)->toArray();
            $blockUserCollection = $this->formatUsers($userDetails->_id, $blockUserCollection, $allUsersSpecifications);

            ## Blocked Users End ##


            //fetch user liked data by to user id
            $peopleILikeUserIds = $this->userRepository->fetchMyLikeDataByUserId($userDetails->_id)->pluck('to_users__id')->toArray();

            $showMobileNumber = true;
            //check login user exist then don't apply this condition.
            if ($userDetails->_id != getUserID()) {
                //check admin can set true mobile number not display of user
                if (getStoreSettings('display_mobile_number') == 1) {
                    $showMobileNumber = false;
                }
                //check admin can set user choice user can show or not mobile number
                if (getStoreSettings('display_mobile_number') == 2 and getUserSettings('display_user_mobile_number', $userDetails->_id) == 1) {
                    $showMobileNumber = false;
                }
                //check admin can set user choice and user can select people I liked user
                if (getStoreSettings('display_mobile_number') == 2 and getUserSettings('display_user_mobile_number', $userDetails->_id) == 2 and !in_array(getUserID(), $peopleILikeUserIds)) {
                    $showMobileNumber = false;
                }
            }

            $userProfileData = [
                'aboutMe'               => $userProfile->about_me,
                'city'                  => $userProfile->city,
                'mobile_number'         => $userDetails->mobile_number,
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
                $multiple = (isset($item['multiple']) and ($item['multiple'] == true))
                            ? true
                            : false;


                if (!$isOwnProfile and array_key_exists($itemKey, $userSpecifications)) {
                    $userSpecKey =  $userSpecifications[$itemKey];
                    $itemValue = (isset($item['options']) and isset($item['options'][$userSpecKey]))
                            ? $item['options'][$userSpecKey]
                            : $userSpecifications[$itemKey];

                    if ($multiple == true) {
                        //$itemValue = unserialize($itemValue);
                    }
                    $items[] = [
                        'name'  => $itemKey,
                        'label'  => $item['name'],
                        'input_type' => $item['input_type'],
                        'value' => $itemValue,
                        'options' => isset($item['options']) ? $item['options'] : '',
                        'selected_options' => (!__isEmpty($userSpecKey)) ? $userSpecKey : '',
                        'multiple' => (isset($item['multiple']) and ($item['multiple'] == true))
                            ? true
                            : false,
                    ];
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
                    if ($multiple == true) {
                        //$itemValue = unserialize($itemValue);
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
        $likeDislikeData = $this->userRepository->fetchLikeDislike($userDetails->_id);

        $userLikeData = [];
        //check is not empty
        if (!__isEmpty($likeDislikeData)) {
            $userLikeData = [
                '_id' =>  $likeDislikeData->_id,
                'like' => $likeDislikeData->like
            ];
        }

        //fetch total visitors data
        $visitorData = $this->userRepository->fetchProfileVisitor($userId);

        //fetch block me users
        $blockMeUser =  $this->userRepository->fetchBlockMeUser($userDetails->_id);
        $isBlockUser = false;
        //check if not empty then set variable is true
        if (!__isEmpty($blockMeUser)) {
            $isBlockUser = true;
        }

        //fetch block by me user
        $blockUserData = $this->userRepository->fetchBlockUser($userDetails->_id);
        $blockByMe = false;
        //if it is empty
        if (!__isEmpty($blockUserData)) {
            $blockByMe = true;
        }

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
            'userLikeData'          => $userLikeData,
            'totalUserLike'         => count($peopleILikes),
            'totalUserLikesMe'      => count($peopleWhoLikesMe),
            'totalMutualLikes'      => count($mutualLikeCollection),
            'totalUserFavourite'    => count($peopleIFavourites),
            'totalUserFavouritesMe'    => count($peopleWhoFavouritesMe),
            'totalBlockedUsers'     => count($blockUserCollection),
            'totalVisitors'         => $visitorData->count(),
            'isBlockUser'           => $isBlockUser,
            'blockByMeUser'         => $blockByMe,
            'userOnlineStatus'      => $this->getUserOnlineStatus($userDetails->userAuthorityUpdatedAt),
            'isPremiumUser'         => isPremiumUser($userId),
            'peopleILikes'          => $peopleILikes,
            'peopleWhoLikesMe'      => $peopleWhoLikesMe,
            'mutualLikes'           => $mutualLikeCollection,
            'peopleIFavourites'     => $peopleIFavourites,
            'peopleWhoFavouritesMe' => $peopleWhoFavouritesMe,
            'blockedUsers'          => $blockUserCollection,
        ]);
    }

    /**
     * Process User Update.
     *
     * @param string $userUid
     * @param array $inputData
     *
     *---------------------------------------------------------------- */
    public function processUserUpdate($userUid, $inputData)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }


        $cityId = isset($inputData['city_id']) ? $inputData['city_id'] : 0;
        $userId = $userDetails->_id;
        $isUpdated = false;

       /* if($inputData['first_name'] == 'Viraj'){
              echo "<pre>"; print_r($inputData); exit;
        }*/
        ## Location info ##

        if (!__isEmpty($cityId)) {

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

            $userProfileDetails = [
                'countries__id' => $countryId,
                'city' => $cityName,
                'location_latitude' => $cityData->latitude,
                'location_longitude' => $cityData->longitude
            ];
            // get user profile
            $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

            
            if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
                activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' update own location.');
                $isUpdated = true;
            }
        }


        // Prepare Update User data
        $updateData = [
            'first_name'        => $inputData['first_name'],
            'last_name'         => $inputData['last_name'],
            'email'             => $inputData['email'],
            'username'          => $inputData['username'],
            'designation'       => array_get($inputData, 'designation'),
            'mobile_number'     => $inputData['mobile_number'],
            'status'            => array_get($inputData, 'status', 2)
        ];
        

        ## Profile Information ##
        // Prepare User profile details
        $userProfileDetails = [
            'gender'                => array_get($inputData, 'gender'),
            'dob'                   => array_get($inputData, 'birthday'),
        ];

        // get user profile
        $userProfile = $this->userSettingRepository->fetchUserProfile($userId);

        // update user profile
        if ($this->userSettingRepository->updateUserProfile($userProfile, $userProfileDetails)) {
            
            // Adding activity log for update user
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user profile info updated.');

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
                } else if($inputKey == 'looking_for'){
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
        
        // Check if user profile updated or store
        if ($this->userSettingRepository->storeOrUpdateUserSpecification($storeOrUpdateData)) {
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' update own user settings.');

            $isUpdated = true;
        }


        // check if user updated 
        if ($this->manageUserRepository->updateUser($userDetails, $updateData)) {
            // Adding activity log for update user
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user info updated.');

            $isUpdated = true;
        }

        if ($isUpdated) {
            return $this->engineReaction(1, ['show_message' => true], __tr('User updated successfully.'));
        }

        return $this->engineReaction(14, ['show_message' => true], __tr('Nothing updated.'));
    }

    /**
     * Process Soft Delete User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processSoftDeleteUser($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }
        // Prepare Update User data
        $updateData = [
            'status' => 5
        ];

        // check if user soft deleted 
        if ($this->manageUserRepository->updateUser($userDetails, $updateData)) {
            // Add activity log for user soft deleted
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user soft deleted.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid, 'show_message' => true], __tr('User soft deleted successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process Soft Delete User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processPermanentDeleteUser($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }
        // check if user soft deleted first
        if ($userDetails->status != 5) {
            return $this->engineReaction(2, ['show_message' => true], __tr('To delete user permanently you have to soft delete first.'));
        }
        // check if user deleted 
        if ($this->manageUserRepository->deleteUser($userDetails)) {
            // Add activity log for user permanent deleted
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user permanent deleted.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid, 'show_message' => true], __tr('User permanent deleted successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process Restore User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processUserRestore($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }
        // Prepare Update User data
        $updateData = [
            'status' => 1
        ];

        // check if restore deleted 
        if ($this->manageUserRepository->updateUser($userDetails, $updateData)) {
            // Add activity log for user restored
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user restored.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid, 'show_message' => true], __tr('User restore successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process Block User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processBlockUser($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }
        // Prepare Update User data
        $updateData = [
            'status' => 3 // Blocked
        ];

        // check if user blocked 
        if ($this->manageUserRepository->updateUser($userDetails, $updateData)) {
            // Add activity log for user blocked
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user blocked.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid, 'show_message' => true], __tr('User blocked successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process Unblock User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processUnblockUser($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }
        // Prepare Update User data
        $updateData = [
            'status' => 1 // Active
        ];

        // check if user soft deleted 
        if ($this->manageUserRepository->updateUser($userDetails, $updateData)) {
            // Add activity log for user unblocked
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user unblocked.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid, 'show_message' => true], __tr('User unblocked successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Prepare User edit data.
     *
     * @param array $userUid
     *
     *---------------------------------------------------------------- */
    public function prepareUserDetails($userUid)
    {
        $user = $this->manageUserRepository->fetchUser($userUid);
        // check if user details exists
        if (__isEmpty($user)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $userDetails = [
            'full_name'         => $user->first_name . ' ' . $user->last_name,
            'email'             => $user->email,
            'username'          => $user->username,
            'designation'       => $user->designation,
            'mobile_number'     => $user->mobile_number
        ];

        return $this->engineReaction(1, [
            'userDetails' => $userDetails
        ]);
    }

    /**
     * prepare Fake User Generator Options.
     *
     *---------------------------------------------------------------- */
    public function prepareFakeUserOptions()
    {
        //user options
        $userSettings = configItem('user_settings');
        $fakerGeneratorOptions = configItem('fake_data_generator');

        //countries
        $countries = $this->countryRepository->fetchAll();
        $countryIds = $countries->pluck('id')->toArray();

        return $this->engineReaction(1, [
            'gender' => $userSettings['gender'],
            'languages' =>  $userSettings['preferred_language'],
            'default_password' => $fakerGeneratorOptions['default_password'],
            'recordsLimit' => $fakerGeneratorOptions['records_limits'],
            'countries' => $countries->toArray(),
            'randomData' => [
                'country' => array_rand($countryIds),
                'gender'  => array_rand(($userSettings['gender'])),
                'language'  => array_rand(($userSettings['preferred_language']))
            ],
            'ageRestriction' => configItem('age_restriction')
        ]);
    }

    /**
     * prepare Fake User Generator Options.
     *
     *---------------------------------------------------------------- */
    public function processGenerateFakeUser($options)
    {
        $transactionResponse = $this->manageUserRepository->processTransaction(function () use ($options) {

            $countries = $this->countryRepository->fetchAll()->pluck('id')->toArray();

            //for page number
            if (__isEmpty(session('fake_user_api_page_no')) or (session('fake_user_api_page_no') >= 9)) {
                session(['fake_user_api_page_no' => 1]);
            } else {
                $page = session('fake_user_api_page_no');
                session(['fake_user_api_page_no' => $page + 1]);
            }

            $page = session('fake_user_api_page_no');

            //get All photo ids
            $photoIds = collect(getPhotosFromAPI($page))->pluck('id')->toArray();
            //user options
            $userSettings = configItem('user_settings');

            $specificationConfig = $this->getUserSpecificationConfig();

            $usersAdded = $authoritiesAdded = $profilesAdded = $creditWallets = $specsAdded = false;
            $users = [];
            $creditWalletStoreData = [];

            //for users
            for ($i = 0; $i < $options['number_of_users']; $i++) {
                //random timezone
                $timezone =  $this->faker->timezone;
                $createdDate = Carbon::now()->addMinutes($i + 1);

                $users[] =     [
                    'first_name'     => $this->faker->firstname,
                    'last_name'     => $this->faker->lastname,
                    'email'         => $this->faker->unique()->safeEmail,
                    'username'         => $this->faker->unique()->userName,
                    'created_at'     => $createdDate,
                    'updated_at'     => $createdDate,
                    'password'         => bcrypt($options['default_password']),
                    'status'         => 1,
                    'mobile_number' => $this->faker->e164PhoneNumber,
                    'timezone'         => $timezone,
                    'is_fake'         => 1
                ];
                unset($createdDate);
            }

            // Store users
            $addedUsersIds = $this->manageUserRepository->storeMultipleUsers($users);

            //check if users added
            if ($addedUsersIds) {
                $usersAdded = true;
                $authorities = $profiles = $specifications = [];
                // for authority
                foreach ($addedUsersIds as $key => $addedUserID) {
                    $createdDate = Carbon::now()->addMinutes($key + 1);
                    //authorities
                    $authorities[] = [
                        'created_at' => $createdDate,
                        'updated_at' => $createdDate,
                        'status' => 1,
                        'users__id' => $addedUserID,
                        'user_roles__id' => 2,
                    ];

                    //random age
                    $age = rand($options['age_from'], $options['age_to']);

                    $country = $options['country'];

                    //check if country is random or not set
                    if ($options['country'] == 'random' or __isEmpty($options['country'])) {
                        $randomKey = array_rand($countries);
                        $country = $countries[$randomKey];
                    }

                    //check if gender is random or not set
                    $gender = $options['gender'];
                    if ($options['gender'] == 'random' or __isEmpty($options['gender'])) {
                        $gender = array_rand($userSettings['gender']);
                    }

                    //check if language is random or not set
                    $language = $options['language'];
                    if ($options['language'] == 'random' or __isEmpty($options['language'])) {
                        $language = array_rand($userSettings['preferred_language']);
                    }

                    //for profiles
                    $profiles[] = [
                        'created_at'         => $createdDate,
                        'updated_at'         => $createdDate,
                        'users__id'         => $addedUserID,
                        'countries__id'     => $country,
                        'gender'             => $gender,
                        'profile_picture'     => strtr("https://picsum.photos/id/__imageID__/360/360", ['__imageID__' => array_rand($photoIds)]),
                        'cover_picture'     => strtr("https://picsum.photos/id/__imageID__/820/360", ['__imageID__' => array_rand($photoIds)]),
                        'dob'                 => Carbon::now()->subYears($age)->format('Y-m-d'),
                        'city'                 => $this->faker->city,
                        'about_me'            => $this->faker->text(rand(50, 500)),
                        'work_status'        => array_rand($userSettings['work_status']),
                        'education'         => array_rand($userSettings['educations']),
                        'is_verified'         => rand(0, 1),
                        'location_latitude'     => $this->faker->latitude,
                        'location_longitude'     => $this->faker->longitude,
                        'preferred_language'     => $language,
                        'relationship_status'     => array_rand($userSettings['relationship_status'])
                    ];
                    unset($createdDate);
                    //check enable bonus credits for new user
                    if (getStoreSettings('enable_bonus_credits')) {
                        $creditWalletStoreData[] = [
                            'status'     => 1,
                            'users__id' => $addedUserID,
                            'credits'     => getStoreSettings('number_of_credits'),
                            'credit_type' => 1 //Bonuses
                        ];
                    }

                    if (!__isEmpty($specificationConfig['groups'])) {
                        foreach ($specificationConfig['groups'] as $key => $group) {
                            if (in_array($key, ["looks", "personality", "lifestyle"])) {
                                if (!__isEmpty($group['items'])) {
                                    foreach ($group['items'] as $key2 => $item) {
                                        $specifications[] = [
                                            'type'                  => 1,
                                            'status'                => 1,
                                            'specification_key'     => $key2,
                                            'specification_value'   => isset($item['options']) ? array_rand($item['options']) : null,
                                            'users__id'             => $addedUserID
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }

                //check if authorities added
                if ($this->manageUserRepository->storeUserAuthorities($authorities)) {
                    $authoritiesAdded = true;
                }

                //check if profiles added
                if ($this->manageUserRepository->storeUserProfiles($profiles)) {
                    $profilesAdded = true;
                }

                //check if profiles added
                if (!__isEmpty($specifications)) {
                    $this->manageUserRepository->storeUserSpecifications($specifications);
                }

                if (!__isEmpty($creditWalletStoreData)) {
                    //store user credit transaction data
                    $this->manageUserRepository->storeCreditWalletTransactions($creditWalletStoreData);
                }
            }

            //if all data inserted
            if ($usersAdded and $authoritiesAdded and $profilesAdded) {
                return $this->manageUserRepository->transactionResponse(1, ['show_message' => true], __tr('Fake users added successfully.'));
            }

            // // Send failed server error message
            return $this->manageUserRepository->transactionResponse(2, ['show_message' => true], __tr('Fake users not added.'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Process Block User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processVerifyUserProfile($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);

        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $profileAddedAndVerified = $profileVerified = false;

        $profile = $this->manageUserRepository->fetchUserProfile($userDetails->_id);

        // check if profile is empty , if true then create profile
        if (__isEmpty($profile)) {
            if ($this->manageUserRepository->storeUserProfile(["users__id" => $userDetails->_id, 'is_verified' => 1])) {
                $profileAddedAndVerified = true;
            }
        } else {
            if ($this->manageUserRepository->updateUserProfile($profile, ['is_verified' => 1])) {
                $profileVerified = true;
            }
        }

        // check if user added and verified  
        if ($profileAddedAndVerified or $profileVerified) {
            // Add activity log for user blocked
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user verified.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid], __tr('User verified successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process Block User.
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processUnverifyUserProfile($userUid)
    {
        $userDetails = $this->manageUserRepository->fetchUser($userUid);

        // check if user details exists
        if (__isEmpty($userDetails)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('User does not exists.'));
        }

        $profileAddedAndVerified = $profileVerified = false;

        $profile = $this->manageUserRepository->fetchUserProfile($userDetails->_id);

        // check if profile is empty , if true then create profile
        if (__isEmpty($profile)) {
            if ($this->manageUserRepository->storeUserProfile(["users__id" => $userDetails->_id, 'is_verified' => 0])) {
                $profileAddedAndVerified = true;
            }
        } else {
            if ($this->manageUserRepository->updateUserProfile($profile, ['is_verified' => 0])) {
                $profileVerified = true;
            }
        }

        // check if user added and verified  
        if ($profileAddedAndVerified or $profileVerified) {
            // Add activity log for user blocked
            activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user unverified.');
            return $this->engineReaction(1, ['userUid' => $userDetails->_uid], __tr('User unverified successfully.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }
    

    /**
     * get manage  user transaction list data.
     *
     * @param $userUid
     * @return object
     *---------------------------------------------------------------- */
    public function getUserTransactionList($userUid)
    {
        $user = $this->manageUserRepository->fetchUser($userUid);
        //if user not exist
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User does not exist.'));
        }

        $transactionCollection = $this->creditWalletRepository->fetchUserTransactionListData($user->_id);

          // echo "<pre>";print_r($transactionCollection);exit();

        $requireColumns = [
            'created_at' => function ($key) {
                return formatDate($key['created_at']);
            },
            'payment_status',
            'formattedStatus' => function ($key) {
                return configItem('payments.status_codes', $key['payment_status']);
            },
            'amount',
            'formattedAmount' => function ($key) {
                return priceFormat($key['amount'], true, true);
            },
            'paln_title_detail',
            'formattedAmount' => function ($key) {
                return priceFormat($key['paln_title_detail'], true, true);
            },
            'plan_type_detail',
            'formattedPlanType' => function ($key) {
                //check is not empty
                if (!__isEmpty($key['plan_type_detail'])) {
                   
                    return configItem('plan_settings.type', $key['plan_type_detail']);
                }

                return '-';
            },            
            'plan_active_status',
            'formattedAmount' => function ($key) {
                return priceFormat($key['plan_active_status'], true, true);
            },
            'currency',
            'formattedIsTestMode' => function ($key) {
                return configItem('payments.payment_checkout_modes', $key['currency']);
            },
            'transaction_id',
            'formattedCreditType' => function ($key) {
                return configItem('payments.credit_type', $key['transaction_id']);
            }
        ];
        //echo "<pre>"; print_r($transactionCollection); exit;
        return $this->dataTableResponse($transactionCollection, $requireColumns);
    }

    /**
     * Delete photo, cover or profile of user .
     *
     * @param string $userUid
     *
     *---------------------------------------------------------------- */
    public function processUserPhotoDelete($userUid, $type, $profileOrPhotoUid)
    {
        $transactionResponse = $this->manageUserRepository->processTransaction(function () use ($userUid, $type, $profileOrPhotoUid) {

            $userDetails = $this->manageUserRepository->fetchUser($userUid);

            // check if user details exists
            if (__isEmpty($userDetails)) {
                return $this->manageUserRepository->transactionResponse(18, null, __tr('User does not exists.'));
            }

            //if type is photo
            if ($type == 'photo') {

                $userPhoto = $this->manageUserRepository->getUsersPhoto($userDetails->_id, $profileOrPhotoUid);
                $imagePath = getPathByKey('user_photos', ['{_uid}' => $userDetails->_uid]);

                //if deleted 
                if ($this->manageUserRepository->deleteUserPhoto($userPhoto)) {
                    $this->mediaEngine->processDeleteFile($imagePath, $userPhoto->file);
                    return $this->manageUserRepository->transactionResponse(1, ['show_message' => true], __tr('Photo removed successfully.'));
                }
            } else if ($type == 'profile') {

                $profile = $this->manageUserRepository->fetchUserProfile($userDetails->_id);

                //if deleted 
                if ($this->manageUserRepository->updateUserProfile($profile, ['profile_picture' => null])) {

                    //check if url
                    if (!isImageUrl($profile->profile_picture)) {
                        $imagePath = getPathByKey('profile_photo', ['{_uid}' => $userDetails->_uid]);
                        $this->mediaEngine->processDeleteFile($imagePath, $profile->profile_picture);
                    }
                    // Add activity log for user soft deleted
                    activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user profile photo deleted.');

                    return $this->manageUserRepository->transactionResponse(1, ['show_message' => true], __tr('Photo removed successfully.'));
                }
            } else if ($type == 'cover') {
                $profile = $this->manageUserRepository->fetchUserProfile($userDetails->_id);

                //if deleted 
                if ($this->manageUserRepository->updateUserProfile($profile, ['cover_picture' => null])) {

                    //check if url
                    if (!isImageUrl($profile->profile_picture)) {

                        $imagePath = getPathByKey('cover_photo', ['{_uid}' => $userDetails->_uid]);

                        $this->mediaEngine->processDeleteFile($imagePath, $profile->cover_picture);
                    }
                    // Add activity log for user soft deleted
                    activityLog($userDetails->first_name . ' ' . $userDetails->last_name . ' user cover photo soft deleted.');

                    return $this->manageUserRepository->transactionResponse(1, ['show_message' => true], __tr('Photo removed successfully.'));
                }
            }

            return $this->manageUserRepository->transactionResponse(2, ['show_message' => true], __tr('Something went wrong on server.'));
        });

        return $this->engineReaction($transactionResponse);
    }

    /**
     * Format user list
     *
     * @param array $users
     *
     *---------------------------------------------------------------- */
    private function formatUsers($userId, $users = array(), $specifications = array()) {

        foreach ($users as $key => $user) {

            $user = (object) $user;
            $userAge = isset($user->dob) ? Carbon::parse($user->dob)->age : null;
            $gender = isset($user->gender) ? configItem('user_settings.gender', $user->gender) : null;

            $userImageUrl = '';
            //check is not empty
            if (!__isEmpty($user->profile_picture)) {
                $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $user->userUId]);
                $userImageUrl = getMediaUrl($profileImageFolderPath, $user->profile_picture);
            } else {
                $userImageUrl = noThumbImageURL();
            }
            $users[$key]['userImageUrl'] = $userImageUrl;
            $users[$key]['userAge'] = $userAge;
            $users[$key]['gender'] = $gender;
            $users[$key]['detailString'] = implode(", ", array_filter([$userAge, $gender]));
            $users[$key]['created_at'] = formatDiffForHumans($user->created_at);
            $users[$key]['updated_at'] = formatDiffForHumans($user->updated_at);

            if  ($userId == $user->to_users__id) {
                $id = $user->by_users__id;
            } else {
                $id = $user->to_users__id;
            }

            if (isset($specifications[$id])) {
                $specification = $specifications[$id];
                $users[$key] = array_merge($users[$key], $specification);
            }
        }

        return $users;
    }

    /**
     * Prepare User Data table list.
     *
     * @param int $status
     *
     *---------------------------------------------------------------- */
    public function prepareUsersExportList($status)
    {
        $allSpecificationCollection = $this->userSettingRepository->fetchAllUsersSpecification()->toArray();

        $allUsersSpecifications = [];
        if (!\__isEmpty($allSpecificationCollection)) {
            foreach ($allSpecificationCollection as $key => $specification) {
                $allUsersSpecifications[$specification['users__id']][$specification['specification_key']] = $specification['specification_value'];
            }
        }

        $userCollection = $this->manageUserRepository->fetchUsersExportSource($status);

        $users = [];
        foreach ($userCollection  as $key => $user) {
            if (isset($allUsersSpecifications[$user['_id']])) {
                $specification = $allUsersSpecifications[$user['_id']];
            } else {
                $specification = [];
            }
            $user = array_merge($user, $specification);
            $users[$key] = $user;
        }

        $columns = array(
                        'first_name'        => 'First Name',
                        'last_name'         => 'Last Name',
                        'username'          => 'Username',
                        'email'             => 'Email',
                        'created_at'        => 'Created On',
                        'dob'               => 'Dob',
                        'gender'            => 'Gender',
                        'location'          => 'Location',
                        'ethnicity'         => 'Ethnicity',
                        'body_type'         => 'Body Type',
                        'height'            => 'Height',
                        'hair_color'        => 'Hair Color',
                        'eye_color'         => 'Eye Color',
                        'body_piercing'     => 'Do you have body piercing?',
                        'no_of_piercing'    => 'How many piercing?',
                        'tattoo'            => 'Do you have tattoo?',
                        'no_of_tattoo'      => 'How many tattoo?',
                        'smoke'             => 'Do you smoke?',
                        'drink'             => 'Do you drink?',
                        'married'           => 'Are you married?',
                        'children'          => 'Do you have children?',
                        'no_of_children'    => 'No. of children?',
                        'relocate'          => 'Are you willing to relocate?',
                        'looking_for'       => 'Looking for',
                        'kinks'             => 'Your Interest/Kinks'
                    );

        $specificationConfig = $this->getUserSpecificationConfig();

        $callback = function() use($users, $columns, $specificationConfig) {
            $file = fopen('php://output', 'w');
            $columnsTitle = array_values($columns);
            fputcsv($file, $columnsTitle);

            foreach ($users as $key => $user) {
                $row = [];

                $userAge = isset($user['dob']) ? Carbon::parse($user['dob'])->age : null;
                $gender = isset($user['gender']) ? configItem('user_settings.gender', $user['gender']) : null;

                foreach ($columns as $field => $title) {

                    $value = (isset($user[$field])) ? $user[$field] : null;

                    if ($field == 'gender') {
                        $row[] = $gender;
                    } else if ($field == 'created_at') {
                        $row[] = formatDate($value, "l jS F Y g:i A");
                    } else if ($value) {

                        foreach ($specificationConfig['groups'] as $specKey => $specification) {
                            if (isset(($specification['items'][$field]))) {
                                $item = $specification['items'][$field];
                                if ($item['input_type'] == 'select') {
                                    $value = (isset($item['options'][$value])) ? $item['options'][$value] : null;
                                }
                            }
                        }

                        $row[] = $value;
                    } else {

                        $row[] = $value;
                    }                   
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return $callback;
    }
}
