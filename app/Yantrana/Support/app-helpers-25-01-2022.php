<?php

use Carbon\Carbon;

use App\Yantrana\Components\User\Models\User;
use App\Yantrana\Components\User\Models\UserProfile;
use App\Yantrana\Components\User\Models\UserAuthorityModel;
use App\Yantrana\Components\User\Models\LikeDislikeModal;
use App\Yantrana\Components\User\Models\FavouriteModal;
use App\Yantrana\Components\User\Models\CreditWalletTransaction;
use App\Yantrana\Components\User\Models\UserSubscription;
use App\Yantrana\Components\User\Models\ProfileBoost;
use App\Yantrana\Components\User\Models\UserBlock;
use App\Yantrana\Components\User\Models\NotificationLog;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Components\User\Repositories\{UserRepository};

/*
|--------------------------------------------------------------------------
| App Helpers
|--------------------------------------------------------------------------
|
*/

/**
 * Get the technical items from tech items
 *
 * @param string 	$key
 * @param mixed    $requireKeys
 *
 * @return mixed
 *-------------------------------------------------------- */

if (!function_exists('configItem')) {
    function configItem($key, $requireKeys = null)
    {
        
        if (!__isEmpty($requireKeys) and !is_array($requireKeys)) {
            return config('__tech.' . $key . '.' . $requireKeys);
        }
        return $geItem = array_get(config('__tech'), $key);
    }
}

/**
 * Check if user logged in
 *
 * @return boolean
 *---------------------------------------------------------------- */

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return Auth::check();
    }
}

/*
      * Convert date with setting time zone
      *
      * @param string $rawDate
      *
      * @return date
      *-------------------------------------------------------- */

if (!function_exists('appTimezone')) {
    function appTimezone($rawDate)
    {
        $carbonDate = Carbon::parse($rawDate);

        $appTimezone = getStoreSettings('timezone');
        if (!__isEmpty($appTimezone)) {
            $carbonDate->timezone = $appTimezone;
        }

        return $carbonDate;
    }
}

/**
 * Get formatted date from passed raw date using timezone
 *
 * @param string $rawDateTime
 * @param string $format
 *
 * @return date
 *-------------------------------------------------------- */

if (!function_exists('formatDate')) {
    function formatDate($rawDateTime, $format = 'l jS F Y')
    {
        $date = appTimezone($rawDateTime);

        return __tr($date->translatedFormat($format));
    }
}

/**
 * Get formatted date from passed raw date using timezone
 *
 * @param string $rawDateTime
 * @param string $format
 *
 * @return date
 *-------------------------------------------------------- */

if (!function_exists('formatDiffForHumans')) {
    function formatDiffForHumans($rawDateTime)
    {
        $date = appTimezone($rawDateTime);

        return $date->diffForHumans();
    }
}

/**
 * Get user authentication
 *
 * @return array
 *---------------------------------------------------------------- */

if (!function_exists('getUserAuthInfo')) {
    function getUserAuthInfo($statusCode = null)
    {
        $userAuthInfo = [
            'authorized' => false,
            'reaction_code' => 9
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $userProfile = UserProfile::where('users__id', $user->_id)->first();
            $userRole = UserAuthorityModel::join('user_roles', 'user_roles._id', '=', 'user_authorities.user_roles__id')
                ->where([
                    'users__id'    => $user->_id
                ])->select('user_authorities._id AS user_authority_id', 'user_roles._id', 'user_roles.title')->first();

            $authenticationToken = md5(uniqid(true));

            $profilePictureUrl = noThumbImageURL();
            $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => authUID()]);

            if (!__isEmpty($userProfile)) {
                if (!__isEmpty($userProfile->profile_picture)) {
                    $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $userProfile->profile_picture);
                }
            }

            $userAuthInfo = [
                'authorization_token' => $authenticationToken,
                'authorized'          => true,
                'reaction_code'       => !empty($statusCode) ? $statusCode : 10,
                'isProfileComplete'   => isProfileComplete($user->_id),
                'profile' => [
                    '_id'             => $user->_id,
                    '_uid'             => $user->_uid,
                    'username'        => !__isEmpty($user) ? $user->username : '',
                    'full_name'     => !__isEmpty($user) ?  $user->first_name . ' ' . $user->last_name : '',
                    'first_name'    => !__isEmpty($user) ? $user->first_name : '',
                    'last_name'        => !__isEmpty($user) ? $user->last_name : '',
                    'country'        => !__isEmpty($userProfile) ? $userProfile->countries__id : '',
                    'email'         => $user->email,
                    'role_id'       => $userRole->_id,
                    'role'          => $userRole->title,
                    'authority_id'  => $userRole->user_authority_id,
                    'profile_picture' => (!__isEmpty($userProfile))
                        ? $userProfile->profile_picture
                        : '',
                    'profile_picture_url' => $profilePictureUrl,
                    'about_me'          => (!__isEmpty($userProfile))
                        ? $userProfile->about_me
                        : '',
                ],
                'personnel'   => $user->_id,
                'timezone'      => $user->timezone ?? false
            ];
        }

        if (is_string($statusCode)) {
            return array_get($userAuthInfo, $statusCode, null);
        }

        return $userAuthInfo;
    }
}

/**
 * Get current date time
 *
 * @return void
 *-------------------------------------------------------- */

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return (getUserAuthInfo('profile.role_id') == 1) ? true : false;
    }
}

/**
 * Get current date time
 *
 * @return void
 *-------------------------------------------------------- */

if (!function_exists('getCurrentDateTime')) {
    function getCurrentDateTime()
    {
        return new DateTime();
    }
}

/*
    * Check if access social account is valid
    *
    * @param string $providerKey
    *
    * @return bool.
    *-------------------------------------------------------- */

if (!function_exists('getSocialProviderName')) {
    function getSocialProviderName($providerKey)
    {
        if (!__ifIsset($providerKey)) {
            return false;
        }

        $socialLoginDriver = Config('__tech.social_login_driver');

        if (array_key_exists($providerKey, $socialLoginDriver) !== false) {
            return $socialLoginDriver[$providerKey];
        }


        return false;
    }
}

/*
    * Check if access social account is valid
    *
    * @param string $providerKey
    *
    * @return bool.
    *-------------------------------------------------------- */

if (!function_exists('getSocialProviderKey')) {
    function getSocialProviderKey($providerKey)
    {
        if (!__ifIsset($providerKey)) {
            return false;
        }

        $socialLoginDriver = Config('__tech.social_login_driver_keys');

        if (array_key_exists($providerKey, $socialLoginDriver)) {
            return $socialLoginDriver[$providerKey];
        }


        return false;
    }
}

/**
 * Auth Uid
 */
if (!function_exists('authUID')) {
    function authUID()
    {
        if (isLoggedIn()) {
            return Auth::user()->_uid;
        }
        return false;
    }
}

/**
 * Auth id
 */
if (!function_exists('getUserID')) {
    function getUserID()
    {
        $user = Auth::user();
        if (!__isEmpty($user)) {
            return $user->_id;
        }
        return null;
    }
}

/**
 * Auth uid
 */
if (!function_exists('getUserUID')) {
    function getUserUID()
    {
        if (isLoggedIn()) {
            return Auth::user()->_uid;
        }
        return false;
    }
}

/**
 * Auth uid
 */
if (!function_exists('fetchTotalUserLikedCount')) {
    function fetchTotalUserLikedCount($toUserId)
    {
        return LikeDislikeModal::leftJoin('users', 'like_dislikes.by_users__id', '=', 'users._id')
            ->select(
                __nestedKeyValues([
                    'like_dislikes.*',
                    'users' => [
                        '_id',
                        'status as userStatus'
                    ]
                ])
            )
            ->where('like_dislikes.to_users__id', $toUserId)
            ->where('like_dislikes.like', 1)
            ->where('users.status', 1)
            ->count();
    }
}



/**
 * Auth uid
 */
if (!function_exists('fetchTotalUserFavouritedCount')) {
    function fetchTotalUserFavouritedCount($toUserId)
    {
        return FavouriteModal::leftJoin('users', 'favourites.by_users__id', '=', 'users._id')
            ->select(
                __nestedKeyValues([
                    'favourites.*',
                    'users' => [
                        '_id',
                        'status as userStatus'
                    ]
                ])
            )
            ->where('favourites.to_users__id', $toUserId)
            ->where('favourites.favourite', 1)
            ->where('users.status', 1)
            ->count();
    }
}

/*
    * get setting items
    *
    * @param string $name
    *
    * @return void
    *---------------------------------------------------------------- */

if (!function_exists('getStoreSettings')) {
    function getStoreSettings($itemName, $itemKeys = null)
    {
        if ($itemKeys) {
            return  Arr::get(getStoreSettings($itemName), $itemKeys);
        }

        $appSettings = [];

        $storeConfiguration = Cache::remember('cache.app.setting.all', (1000 * 60), function () use ($appSettings) {
            $configurationSettings = \App\Yantrana\Components\Configuration\Models\ConfigurationModel::select('name', 'value', 'data_type')->get();

            // check if configuration settings exists in db
            if (!__isEmpty($configurationSettings)) {
                foreach ($configurationSettings as $configurationSetting) {
                    $appSettings[$configurationSetting->name] = $configurationSetting->value;
                }
            }

            unset($configurationSettings);
            return $appSettings;
        });

        // Fetch default setting
        $defaultSettings = config('__settings.items');

        // check if default setting is empty
        if (__isEmpty($defaultSettings)) {
            return null;
        }

        // Loop over default items for finding item default value
        foreach ($defaultSettings as $defaultSetting) {
            // Check if item name exists in default settings
            if (array_key_exists($itemName, $defaultSetting)) {
                // check if requested item exists in store configuration array
                if (array_key_exists($itemName, $storeConfiguration)) {
                    switch ($defaultSetting[$itemName]['data_type']) {
                        case 1:
                            return (string) $storeConfiguration[$itemName];
                            break;
                        case 2:
                            return (bool) $storeConfiguration[$itemName];
                            break;
                        case 3:
                            return (int) $storeConfiguration[$itemName];
                            break;
                        case 4:
                            return json_decode($storeConfiguration[$itemName], true);
                            break;
                        default:
                            return $storeConfiguration[$itemName];
                            break;
                    }
                }
                // Return default value
                return $defaultSetting[$itemName]['default'];
            }
        }

        // Check if request for logo image url
        if ($itemName == 'logo_image_url') {
            $logoName = getStoreSettings('logo_name');
            $logoFilePath = getPathByKey('logo') . '/' . $logoName;
            $logoImageUrl = getMediaUrl($logoFilePath) ?:  url('imgs/' . configItem('logo_name'));
            return $logoImageUrl . '?ver=' . @filemtime($logoFilePath);
        }

        // Check if request for small logo image url
        if ($itemName == 'small_logo_image_url') {
            $smallLogoName = getStoreSettings('small_logo_name');
            $smallLogoFilePath = getPathByKey('small_logo') . '/' . $smallLogoName;
            $smallLogoImageUrl = getMediaUrl($smallLogoFilePath) ?:  url('imgs/' . configItem('small_logo_name'));
            return $smallLogoImageUrl . '?ver=' . @filemtime($smallLogoFilePath);
        }

        // Check if request for admin logo image url
        if ($itemName == 'admin_logo_image_url') {
            $adminLogoName = getStoreSettings('admin_logo_name');
            $adminLogoFilePath = getPathByKey('admin_logo') . '/' . $adminLogoName;
            $adminLogoImageUrl = getMediaUrl($adminLogoFilePath) ?:  url('imgs/' . configItem('admin_logo_name'));
            return $adminLogoImageUrl . '?ver=' . @filemtime($adminLogoFilePath);
        }

        // Check if request for favicon url
        if ($itemName == 'favicon_image_url') {
            $faviconName = getStoreSettings('favicon_name');
            $faviconFilePath = getPathByKey('favicon') . '/' . $faviconName;
            $faviconImageUrl = getMediaUrl($faviconFilePath) ?:  url('imgs/' . configItem('favicon_name'));
            return $faviconImageUrl . '?ver=' . @filemtime($faviconFilePath);
        }

        return null;
    }
}

/*
    * get user setting items
    *
    * @param string $name
    *
    * @return void
    *---------------------------------------------------------------- */

if (!function_exists('getUserSettings')) {
    function getUserSettings($itemName, $userID = null)
    {
        //if user is not exist then user loggedIn user id
        if (__isEmpty($userID)) {
            $userID = getUserID();
        }

        $userSettingConfiguration = \App\Yantrana\Components\UserSetting\Models\UserSettingModel::select('key_name', 'value', 'data_type')->where('users__id', $userID)->get();

        // check if configuration settings exists in db
        if (!__isEmpty($userSettingConfiguration)) {
            $storeUserSettings = [];
            foreach ($userSettingConfiguration as $userSetting) {
                $storeUserSettings[$userSetting->key_name] = $userSetting->value;
            }

            // check if requested item exists in store configuration array
            if (array_key_exists($itemName, $storeUserSettings)) {
                return $storeUserSettings[$itemName];
            }
        }

        // Fetch default setting
        $defaultSettings = CommonTrait::getUserSettingConfigItem()['items'];

        // check if default setting is empty
        if (__isEmpty($defaultSettings)) {
            return null;
        }

        // Loop over default items for finding item default value
        foreach ($defaultSettings as $defaultSetting) {
            // Check if item name exists in default settings
            if (array_key_exists($itemName, $defaultSetting)) {
                // Return default value
                return $defaultSetting[$itemName]['default'];
            }
        }
        return null;
    }
}

/*
    * get user setting items
    *
    * @param string $name
    *
    * @return void
    *---------------------------------------------------------------- */

if (!function_exists('getFeatureSettings')) {
    function getFeatureSettings($itemName, $arrayItem = null, $userID = null)
    {
        //if user is not exist then user loggedIn user id
        if (__isEmpty($userID)) {
            $userID = getUserID();
        }

        $userRepository = new UserRepository();
        //get feature plans
        $featurePlanSettings = getStoreSettings('feature_plans');
        // Fetch default setting
        $defaultSettings = config('__settings.items.premium-feature');
        $defaultFeaturePlans = $defaultSettings['feature_plans']['default'];

        //check is not empty
        if (!__isEmpty($featurePlanSettings)) {
            //collect feature plan json data into array
            $featureSettings = is_array($featurePlanSettings) ? $featurePlanSettings : json_decode($featurePlanSettings, true);
            $featurePlanCollection = combineArray($defaultFeaturePlans, $featureSettings);
            $featureUserSettings = [];
            $encounterAllUserCount = 0;
            foreach ($featurePlanCollection as $key => $features) {
                //check is enable setting or not
                if (isset($features['enable']) and $features['enable']) {
                    if (!__isEmpty($arrayItem)) {
                        $featureUserSettings[$key]     = $features;
                    } else {
                        $featureUserSettings[$key] = $features['select_user'];
                    }
                }
            }

            //if arrayItem exist then get array item value
            if (array_key_exists($itemName, $featureUserSettings) and !__isEmpty($arrayItem) and array_key_exists($arrayItem, $featureUserSettings[$itemName])) {
                //return array item value
                return $featureUserSettings[$itemName][$arrayItem];
            }

            // check if requested item exists in store configuration array
            if (array_key_exists($itemName, $featureUserSettings) and __isEmpty($arrayItem)) {
                //profile boost all user list
                $isPremiumUser = $userRepository->fetchPremiumUsers($userID);

                //if select user is 1 then all user can view data
                if ($featureUserSettings[$itemName] == 1) {
                    return true;
                    //if select user is 2 and is premium user then only premium user can view data
                } elseif ($featureUserSettings[$itemName] == 2 and !__isEmpty($isPremiumUser)) {
                    return true;
                    //nothing goes happen than return false
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}

/**
 * Generate currency array.
 *
 * @param string $pageType
 * @param array $inputData
 *
 * @return array
 *---------------------------------------------------------------- */
if (!function_exists('combineArray')) {
    function combineArray(&$defaultArray, &$dbArray)
    {
        $merged = $defaultArray;

        foreach ($dbArray as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = combineArray($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}

/*
    * Get Media Path
    *
    * @param string $name
    *
    * @return void
    *---------------------------------------------------------------- */

if (!function_exists('getMediaUrl')) {
    function getMediaUrl($storagePath, $filename = '')
    {
        // Check if already URL is given then return URL
        if (starts_with($filename, ['http://', 'https://'])) {
            return $filename;
        }
        // check if filename not exists
        if ($filename) {
            $separator = "/";
            if (substr($storagePath, -1) == '/') {
                $separator = "";
            }
            $storagePath .= $separator . $filename;
        }

        $currentFileSystemDriver = configItem('current_filesystem_driver');
        // check if current file system driver is public
        if ($currentFileSystemDriver == 'public-media-storage') {
            /* return File::exists(public_path($storagePath))
                        ? url($storagePath)
                        : false; */
            return url($storagePath);
        } else {
            $currentDisc = YesFileStorage::on($currentFileSystemDriver);
            // check if file is exists
            //if ($currentDisc->isExists($storagePath)) {
            return configItem('do_full_url') . $storagePath;
            //}
        }

        return null;
    }
}

/**
 * Return image or No image thumbnail
 *
 * @return string
 *-------------------------------------------------------- */

if (!function_exists('imageOrNoImageAvailable')) {
    function imageOrNoImageAvailable($image = '')
    {
        if ($image) {
            return $image;
        }
        return noThumbImageURL();
        // return url('/imgs/heart-loading.svg');
    }
}

/**
 * Get no thumb image URL
 *
 * @return string
 *-------------------------------------------------------- */

if (!function_exists('noThumbImageURL')) {
    function noThumbImageURL()
    {
        return url('/imgs/no_thumb_image.jpg');
        // return url('/imgs/heart-loading.svg');
    }
}

/**
 * Get no thumb image URL
 *
 * @return string
 *-------------------------------------------------------- */

if (!function_exists('noThumbCoverImageURL')) {
    function noThumbCoverImageURL()
    {
        return url('/imgs/no_thumb_image.jpg');
        // return url('/imgs/heart-loading.svg');
    }
}

/**
 * check if url
 *
 * @return string
 *-------------------------------------------------------- */

if (!function_exists('isImageUrl')) {
    function isImageUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }
}

/*
    * return formatted price
    *
    * @param float $amount
    *
    * @return float
    *---------------------------------------------------------------- */

if (!function_exists('priceFormat')) {
    function priceFormat($amount = null, $currencyCode = false, $currencySymbol = false, $options = [])
    {
        $currencySymbol = getCurrencySymbol();

        $formattedCurrency = html_entity_decode($currencySymbol) . number_format((float) $amount, 2) . ($currencyCode == true ? ' ' . getCurrency() : '');

        return $formattedCurrency;
    }
}

/**
 * get set currency
 *
 * @return string
 *---------------------------------------------------------------- */
if (!function_exists('getCurrency')) {
    function getCurrency()
    {
        return html_entity_decode(getStoreSettings('currency_value'));
    }
}

/**
 * get set currency Symbol
 *
 * @return string
 *---------------------------------------------------------------- */
if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol()
    {
        return html_entity_decode(getStoreSettings('currency_symbol'));
    }
}

/**
 * total user credit data
 *
 * @return string
 *---------------------------------------------------------------- */
if (!function_exists('totalUserCredits')) {
    function totalUserCredits()
    {
        //get wallet transaction data
        $walletTransactions = CreditWalletTransaction::where('users__id', getUserID())->get();
        //get wallet credits array
        $credits = $walletTransactions->pluck('credits')->toArray();
        //sum total credits
        return array_sum($credits);
    }
}

/**
 * check loggedIn user is Premium User
 *
 * @return string
 *---------------------------------------------------------------- */
if (!function_exists('isPremiumUser')) {
    function isPremiumUser($userID = null)
    {
        //if user is not exist then user loggedIn user id
        if (__isEmpty($userID)) {
            $userID = getUserID();
        }

        //get current date time
        $currentDateTime = Carbon::now();
        //get latest subscription data
        $userSubscription = UserSubscription::where('users__id', $userID)
            ->where('expiry_at', '>=', $currentDateTime)
            ->latest()
            ->first();

        //check data exist or not
        if (!__isEmpty($userSubscription)) {
            return true;
        }
        return false;
    }
}

/*
    * Add activity log entry
    *
    * @param string $activity
    *
    * @return void.
    *-------------------------------------------------------- */
if (!function_exists('activityLog')) {
    function activityLog($activity)
    {
        App\Yantrana\Components\User\Models\ActivityLog::create([
            'created_at'    => Carbon::now(),
            'user_id'         => getUserID(),
            '__data'         => $activity
        ]);
    }
}

/*
    * Add notification log entry
    *
    * @param string $message, $action
    *
    * @return void.
    *-------------------------------------------------------- */
if (!function_exists('notificationLog')) {
    function notificationLog($message, $action, $isRead, $userId)
    {
        NotificationLog::create([
            'status'    => 1,
            'users__id' => $userId,
            'message'     => $message,
            'action'     => $action,
            'is_read'     => $isRead
        ]);
    }
}

/*
    * Get Notification List
    *
    * @return void.
    *-------------------------------------------------------- */
if (!function_exists('getNotificationList')) {
    function getNotificationList($userID = null)
    {
        //if user is not exist then user loggedIn user id
        if (__isEmpty($userID)) {
            $userID = getUserID();
        }

        //fetch notifications
        $notification = NotificationLog::where('users__id', $userID)
            ->where('is_read', null)
            ->latest()->take(5)->get();

        $notificationData = [];
        //check is not empty
        if (!__isEmpty($notification)) {
            foreach ($notification as $key => $notify) {
                $notificationData[] = [
                    '_id'          => $notify->_id,
                    '_uid'          => $notify->_uid,
                    'created_at' => $notify->created_at->diffForHumans(),
                    'message'      => $notify->message,
                    'actionUrl'  => $notify->action,
                    'is_read'      => (isset($notify->is_read) and $notify->is_read == 1) ? 'Yes' : 'No'
                ];
            }
        }
        //return array
        return [
            'notificationData' => $notificationData,
            'notificationCount' => $notification->count()
        ];
    }
}

/*
    * Get restriction for media
    *
    * @param string $activity
    *
    * @return void.
    *-------------------------------------------------------- */

if (!function_exists('getMediaRestriction')) {
    function getMediaRestriction($mediaType, $encoded = true)
    {
        $mediaConfiguration = config('yes-file-storage.element_config');
        $allowedExtension = array_get($mediaConfiguration, $mediaType, null);
        // Check if allowed extension exists
        if (!__isEmpty($allowedExtension)) {
            $mediaRestriction = array_get($allowedExtension, 'restrictions.allowedFileTypes');
            if ($encoded) {
                return json_encode($mediaRestriction);
            }

            return $mediaRestriction;
        }

        return false;
    }
}

/**
 * get profile boost time.
 *
 *-----------------------------------------------------------------------*/
if (!function_exists('getProfileBoostTime')) {
    function getProfileBoostTime()
    {
        $currentTime = Carbon::now();
        $booster  = ProfileBoost::where('for_users__id', '=', getUserID())
            ->where('expiry_at', '>=', $currentTime)
            ->orderBy('expiry_at', 'desc')
            ->first();
        if (!__isEmpty($booster)) {
            return Carbon::now()->diffInSeconds($booster->expiry_at, false);
        }

        return 0;
    }
}

/**
 * get featured user list.
 *
 * @param array $storeData
 *
 *-----------------------------------------------------------------------*/
if (!function_exists('getFeatureUserList')) {
    function getFeatureUserList()
    {
        $userRepository = new UserRepository();
        //fetch all user like dislike data
        $getLikeDislikeData = $userRepository->fetchAllUserLikeDislike();
        //pluck to_users_id in array
        $toUserIds = $getLikeDislikeData->pluck('to_users__id')->toArray();
        //all blocked user list
        $blockUserCollection = $userRepository->fetchAllBlockUser();
        //blocked user ids
        $blockUserIds = $blockUserCollection->pluck('to_users__id')->toArray();
        //blocked me user list
        $allBlockMeUser = $userRepository->fetchAllBlockMeUser();
        //blocked me user ids
        $blockMeUserIds = $allBlockMeUser->pluck('by_users__id')->toArray();
        //can admin show in featured user
        $adminIds = [];
        //check condition is false
        if (!getStoreSettings('include_exclude_admin')) {
            //array merge of unique users ids
            $adminIds = $userRepository->fetchAdminIds();
        }
        //array merge of unique users ids
        $ignoreUserIds = array_values(array_unique(array_merge($toUserIds, $blockUserIds, $blockMeUserIds, $adminIds)));
        //profile boost all user list
        $allProfileBoostUser = $userRepository->fetchAllProfileBoostUsers();
        //profile boost all user ids
        $profileBoostUserIds = $allProfileBoostUser->pluck('for_users__id')->toArray();
        //profile boost all user list
        $allPremiumUser = $userRepository->fetchAllPremiumUsers();
        //profile boost all user ids
        $allPremiumUserIds = $allPremiumUser->pluck('users__id')->toArray();
        //array merge of unique users ids
        $acceptUserIds = array_values(array_unique(array_merge($profileBoostUserIds, $allPremiumUserIds)));
        //fetch all random featured users
        $randomFeatureUser = $userRepository->fetchAllRandomFeatureUsers($ignoreUserIds, $acceptUserIds);

        $randomUser = [];
        //check is not empty
        if (!__isEmpty($randomFeatureUser)) {
            $featureUserCount = configItem('random_feature_user_count');
            $randomUsersCollection = $randomFeatureUser;
            //check feature user count is less then user collection then use default feature users
            if ($randomFeatureUser->count() > $featureUserCount) {
                $randomUsersCollection = $randomFeatureUser->random($featureUserCount);
            }
            foreach ($randomUsersCollection as $key => $user) {
                $userImageUrl = noThumbImageURL();
                //check is not empty
                if (!__isEmpty($user->profile_picture)) {
                    $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $user->_uid]);
                    $userImageUrl = getMediaUrl($profileImageFolderPath, $user->profile_picture);
                }
                $randomUser[] = [
                    '_id'                 => $user->_id,
                    '_uid'                 => $user->_uid,
                    'username'             => $user->username,
                    'created_at'        => formatDiffForHumans($user->created_at),
                    'userFullName'         => $user->userFullName,
                    'profile_picture'     => $user->profile_picture,
                    'userImageUrl'         => $userImageUrl
                ];
            }
        }
        return $randomUser;
    }
}

/**
 * activate sidebar link by alias
 *
 * @param string $alias
 *-----------------------------------------------------------------------*/

if (!function_exists('makeLinkActive')) {
    function makeLinkActive($alias)
    {
        if (Route::getCurrentRoute()->getName() == $alias) {
            return " active ";
        }
    }
}

/**
 * activate sidebar link by alias
 *
 * @param string $alias
 *-----------------------------------------------------------------------*/

if (!function_exists('getPhotosFromAPI')) {
    function getPhotosFromAPI($page = 1)
    {
        $ch = curl_init();
        $url = strtr("https://picsum.photos/v2/list?page=__page__&limit=100", [
            '__page__' => $page
        ]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode(($result), true);
    }
}

/**
 * Get demo mode for Demo of site
 *
 * @return boolean.
 *-------------------------------------------------------- */

if (!function_exists('isDemo')) {
    function isDemo()
    {
        return (env('IS_DEMO_MODE', false)) ? true : false;
    }
}

/**
 * Get demo mode for Demo of site
 *
 * @return boolean.
 *-------------------------------------------------------- */

if (!function_exists('isProfileComplete')) {
    function isProfileComplete($userID)
    {
        // Ingnore Profile Wizard
        return true;

        $profile = UserProfile::where('users__id', $userID)->first();
        // items to check
        $checkKeys = array_merge(
            config('__tech.profile_update_wizard.step_one', []), // step one
            config('__tech.profile_update_wizard.step_two', []) // step two
        );

        if (!__isEmpty($profile)) {
            foreach ($profile->toArray() as $key => $value) {
                if (in_array($key, $checkKeys)) {
                    if (__isEmpty($value)) {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }

        return true;
    }
}

/**
  * Get the technical items from tech items
  *
  * @param string   $key
  * @param mixed    $requireKeys
  *
  * @return mixed
  *-------------------------------------------------------- */

if (!function_exists('slugIt')) {
    function slugIt($title, $separator = '-')
    {
        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
/**
 * Check request coming from mobile app
 *
 * @return number.
 *-------------------------------------------------------- */

if (!function_exists('isMobileAppRequest')) {
    config([
        'app.api_request_signature' => request()->header('Api-Request-Signature')
    ]);
    function isMobileAppRequest()
    {

        //Check request coming from mobile app
        if (config('app.api_request_signature') === 'mobile-app-request') {
            return true;
        }

        return false;
    }
}

/**
 * sets the Authentication token (jwt)
 *
 * @return boolean.
 *-------------------------------------------------------- */

if (!function_exists('setAccessToken')) {
    function setAccessToken($token)
    {
        //set token
        config(['app.additional' => [
            'token_refreshed' => $token
        ]]);
    }
}

/**
 * Get active translation languages
 *
 * @return array.
 *-------------------------------------------------------- */

if (!function_exists('getActiveTranslationLanguages')) {
    function getActiveTranslationLanguages()
    {
        $translationLanguages = getStoreSettings('translation_languages');
        if (__isEmpty($translationLanguages)) {
            $translationLanguages = [];
        }
        $translationLanguages[config('__tech.default_translation_language.id', 'en_US')] = configItem('default_translation_language');
        return array_where($translationLanguages, function ($languageItem) {
            if ($languageItem['status'] !== false) {
                return $languageItem;
            }
        });
    }
}

/**
 * Get the applicable theme
 *-----------------------------------------------------------------------*/

if (!function_exists('getApplicableTheme')) {
    function getApplicableTheme()
    {
        if (getStoreSettings('allow_user_to_change_theme')) {
            return getUserSettings('selected_theme') ?? getStoreSettings('color_theme');
        }
        return getStoreSettings('color_theme');
    }
}
