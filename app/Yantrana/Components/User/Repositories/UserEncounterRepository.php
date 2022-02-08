<?php
/**
* UserEncounterRepository.php - Repository file
*
* This file is part of the UserEncounter User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Repositories;

use DB;
use Exception;
use Carbon\Carbon;
use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\User\Models\{
    User as UserModel,
    UserEncounter,
    LikeDislikeModal
};

class UserEncounterRepository extends BaseRepository
{
    /**
     * fetch encounter user list.
     *
     * @return object
     *---------------------------------------------------------------- */
    public function fetchEncounterUser()
    {
        return UserEncounter::where('by_users__id', getUserID())
            ->where('created_at', '>', Carbon::now()->subDays(1)->toDateTimeString())
            ->get();
    }

    /**
     * Fetch the record of Daily User liked / Dislike count
     *
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchDailyUserLikeDislikeCount()
    {
        return LikeDislikeModal::where('by_users__id', getUserID())
            ->where('created_at', '>', Carbon::now()->subDays(1)->toDateTimeString())
            ->count();
    }

    /**
     * fetch all random user list.
     *
     * @return array|object
     *---------------------------------------------------------------- */
    public function fetchRandomUser($toUserIds)
    {
        //if data exist then show records else return blank array
        try {
            $currentTime = Carbon::now();
            $randomUserQuery = UserModel::leftJoin('user_authorities', 'users._id', '=', 'user_authorities.users__id')
                ->leftJoin('user_profiles', 'users._id', '=', 'user_profiles.users__id')
                ->leftJoin('countries', 'user_profiles.countries__id', '=', 'countries._id')
                ->leftJoin('profile_boosts', 'users._id', '=', 'profile_boosts.for_users__id')
                ->select(
                    __nestedKeyValues([
                        'users' => [
                            '_id',
                            '_uid',
                            'status',
                            'username',
                            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS userFullName')
                        ],
                        'user_profiles' => [
                            '_id as profileId',
                            'profile_picture',
                            'cover_picture',
                            'countries__id',
                            'gender',
                            'dob'
                        ],
                        'countries' => [
                            'name as countryName'
                        ],
                        'profile_boosts' => [
                            '_id as profileBoostId'
                        ],
                        'user_authorities' => [
                            'user_roles__id',
                            'updated_at as userAuthorityUpdatedAt'
                        ]
                    ])
                )
                ->whereNotIn('users._id', $toUserIds)
                ->where('users.status', 1);

            if (!getStoreSettings('include_exclude_admin')) {
                $randomUserQuery->where('user_authorities.user_roles__id', '!=', 1);
            }

            return $randomUserQuery->orWhere('profile_boosts.expiry_at', '>=', $currentTime)
                ->orderBy('profile_boosts.created_at', 'desc')
                ->get()->random();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Store encounter (skip) user data.
     *
     * @param array $storeData
     * 
     *-----------------------------------------------------------------------*/
    public function storeEncounterUser($storeData)
    {
        $keyValues = [
            'status',
            'to_users__id',
            'by_users__id'
        ];
        // Get Instance of user User Encounter model
        $userEncounter = new UserEncounter;

        // Store encounter (skip) user data
        if ($userEncounter->assignInputsAndSave($storeData, $keyValues)) {
            return true;
        }
        return false;
    }

    /**
     * Delete old password reminder.
     *
     * @param string $email
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function deleteOldEncounterUser()
    {
        return UserEncounter::where('user_encounters.created_at', '>=', Carbon::today()->addHours(24))
            ->where('by_users__id', getUserID())
            ->delete();
    }
}
