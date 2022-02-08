<?php
/**
* UserEncounterEngine.php - Main component file
*
* This file is part of the UserEncounter User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use Carbon\Carbon;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Components\User\Repositories\{UserEncounterRepository, UserRepository};

class UserEncounterEngine extends BaseEngine
{
    /**
     * @var  UserEncounterRepository $userEncounterRepository - UserEncounter Repository
     */
    protected $userEncounterRepository;

    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * Constructor
     *
     * @param  UserEncounterRepository $userEncounterRepository - UserEncounter Repository
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(
        UserEncounterRepository $userEncounterRepository,
        UserRepository $userRepository
    ) {
        $this->userEncounterRepository     = $userEncounterRepository;
        $this->userRepository            = $userRepository;
    }

    /**
     * Prepare User Encounter List Data.
     *
     *
     *---------------------------------------------------------------- */
    public function getEncounterUserData()
    {
        //delete old encounter User
        $this->userEncounterRepository->deleteOldEncounterUser();
        //fetch all user like dislike data
        $getLikeDislikeData = $this->userRepository->fetchAllUserLikeDislike();
        //pluck to_users_id in array
        $toUserIds = $getLikeDislikeData->pluck('to_users__id')->toArray();
        //fetch encounter user data
        $userEncounterData = $this->userEncounterRepository->fetchEncounterUser();
        //collect encounter user ids
        $encounterUserIds = $userEncounterData->pluck('to_users__id')->toArray();
        //all blocked user list
        $blockUserCollection = $this->userRepository->fetchAllBlockUser();
        //blocked user ids
        $blockUserIds = $blockUserCollection->pluck('to_users__id')->toArray();
        //blocked me user list
        $allBlockMeUser = $this->userRepository->fetchAllBlockMeUser();
        //blocked me user ids
        $blockMeUserIds = $allBlockMeUser->pluck('by_users__id')->toArray();
        //can admin show in featured user
        $adminIds = [];
        //check condition is false
        if (!getStoreSettings('include_exclude_admin')) {
            //array merge of unique users ids
            $adminIds = $this->userRepository->fetchAdminIds();
        }

        //array merge of unique users ids
        $ignoreUserIds = array_unique(array_merge($toUserIds, $encounterUserIds, $blockUserIds, $blockMeUserIds, [getUserID()], $adminIds));

        $randomUser = [];
        //check user encounter feature enable or not
        if (getFeatureSettings('user_encounter')) {
            //fetch encounter user daily view user count
            $dailyEncounterCount    = getFeatureSettings('user_encounter', 'encounter_all_user_count');
            $encounterSelectUser     = getFeatureSettings('user_encounter', 'select_user');
            //fetch all user like dislike data
            $dailyUserLikeDislikeCount = $this->userEncounterRepository->fetchDailyUserLikeDislikeCount();
            //total like or dislike and encounter user count
            $totalEncounterCount = $userEncounterData->count() + $dailyUserLikeDislikeCount;
            //check encounter select "All User (1)" then check total encounter count greater than daily encounter user count then don't show random users
            if ($encounterSelectUser == 1 and $totalEncounterCount >= $dailyEncounterCount) {
                //blank random users
                $randomUser = [];
                //else fetch random users
            } else {
                //fetch random users
                $randomUser = $this->userEncounterRepository->fetchRandomUser($ignoreUserIds);
            }
        }

        $randomUserData = [];
        //check is not empty
        if (!__isEmpty($randomUser)) {
            $userImageUrl = '';
            //check is not empty
            if (!__isEmpty($randomUser->profile_picture)) {
                $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => $randomUser->_uid]);
                $userImageUrl = getMediaUrl($profileImageFolderPath, $randomUser->profile_picture);
            } else {
                $userImageUrl = noThumbImageURL();
            }
            $userCoverUrl = '';
            //check is not empty
            if (!__isEmpty($randomUser->cover_picture)) {
                $coverPath = getPathByKey('cover_photo', ['{_uid}' => $randomUser->_uid]);
                $userCoverUrl = getMediaUrl($coverPath, $randomUser->cover_picture);
            } else {
                $userCoverUrl = noThumbCoverImageURL();
            }

            $userAge = isset($randomUser->dob) ? Carbon::parse($randomUser->dob)->age : null;
            $gender = isset($randomUser->gender) ? configItem('user_settings.gender', $randomUser->gender) : null;

            //random user data
            $randomUserData = [
                '_id'             => $randomUser->_id,
                '_uid'            => $randomUser->_uid,
                'username'         => $randomUser->username,
                'userFullName'     => $randomUser->userFullName,
                'stats'         => $randomUser->status,
                'userImageUrl'     => $userImageUrl,
                'userCoverUrl'     => $userCoverUrl,
                'gender'         => $gender,
                'dob'             => $randomUser->dob,
                'userAge'        => $userAge,
                'countryName'     => $randomUser->countryName,
                'userOnlineStatus'     => $this->getUserOnlineStatus($randomUser->userAuthorityUpdatedAt),
                'isPremiumUser'        => isPremiumUser($randomUser->_id),
                'detailString'    => implode(", ", array_filter([$userAge, $gender]))
            ];
        }

        return $this->engineReaction(1, [
            'randomUserData'        => $randomUserData,
            'loggedInUserIsPremium' => isPremiumUser(getUserID())
        ]);
    }

    /**
     * Process Skip Encounter User.
     *
     * @param array $inputData
     * 
     *-----------------------------------------------------------------------*/
    public function processSkipEncounterUser($toUserUid)
    {
        //delete old encounter User
        $this->userEncounterRepository->deleteOldEncounterUser();

        // fetch User by toUserUid
        $user = $this->userRepository->fetch($toUserUid);

        // check if user exists
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User does not exists.'));
        }

        //store encounter User Data
        $storeData = [
            'status' => 1,
            'to_users__id' => $user->_id,
            'by_users__id' => getUserID()
        ];

        //store encounter user
        if ($this->userEncounterRepository->storeEncounterUser($storeData)) {
            return $this->engineReaction(1, null, __tr('Skip user successfully.'));
        }

        return $this->engineReaction(2, null, __tr('Something went wrong.'));
    }
}
