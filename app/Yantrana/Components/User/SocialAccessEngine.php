<?php

/**
* SocialAccessEngine.php - Main component file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\User\Blueprints\SocialAccessEngineBlueprint;
use Auth;
use Socialite;
use Str;

class SocialAccessEngine extends BaseEngine implements SocialAccessEngineBlueprint
{
    /**
     * @var UserRepository $userRepository - User Repository
     */
    protected $userRepository;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository - User Repository
     *
     * @return void
     *-----------------------------------------------------------------------*/

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Process of login social account  or create new user
     *
     * @param string $provider
     *
     * @return response
     *---------------------------------------------------------------- */
    public function processSocialAccess($provider)
    {
        //get user data from socialite driver in try catch block
        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return $this->engineReaction(18, null, __tr('Something is wrong form social account, Please contact with administrator.'));
        }

        // check the record is empty
        if (__isEmpty($social)) {
            return $this->engineReaction(2, null, __tr('Authentication failed. Please check your  email/password & try again.'));
        }

        $userName = explode(" ", $social->getName());
        $socialUser = [
            'fname'        => $userName[0],
            'lname'        => isset($userName[1]) ? $userName[1] : null,
            'fullName'   => $social->getName(),
            'account_id' => $social->getId(),
            'email'      => $social->getEmail(),
            'provider'   => $provider
        ];

        //check if empty then throw error
        if (__isEmpty($socialUser['email'])) {
            return $this->engineReaction(18, null, __tr('The email is required for user login.'));
        }

        //fetch user by email
        $userData = $this->userRepository->fetchByEmail($socialUser['email']);

        // if email available then
        if (__isEmpty($userData)) { // if already exists registered id
            $userName = Str::slug($socialUser['fullName']);
            //check social user name not exists
            if (!$userName) {
                $userName = uniqid();
            }
            //fetch user by email
            $existUser = $this->userRepository->fetchByName($userName);
            //check user name already exists.
            if (!__isEmpty($existUser)) {
                $userName = uniqid();
            }

            //assign user name to array
            $socialUser['username'] = isset($userName) ? $userName : uniqid();

            // This function call when the user totally new for system
            if ($newUser = $this->userRepository->storeSocialUser($socialUser)) {
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
                            return $this->userRepository->transactionResponse(2, [], __tr('Something went wrong, please contact to administrator.'));
                        }
                    }
                    return $this->processSocialLogin($newUser);
                }
            } else {
                return $this->engineReaction(2, null, __tr('Something went wrong on server.'));
            }
        } else {
            return $this->processSocialLogin($userData);
        }

        //error response
        return $this->engineReaction(2);
    }

    /**
     * Process social user login request using user repository & return
     * engine reaction.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */

    protected function processSocialLogin($userData)
    {
        //fetch user
        $user = $this->userRepository->fetchByID($userData['_id']);

        //check user is empty
        if (__isEmpty($user)) {
            return $this->engineReaction(2, null, __tr('User not exists.'));
        }

        //if user not active then show message
        if ($user->status != 1) {
            return $this->engineReaction(2, null, __tr('Your account currently __status__, Please contact to administrator.', ['__status__' => configItem('status_codes', $user->status)]));
        }

        // Get logged in if credentials valid
        if (Auth::loginUsingId($user->_id)) {
            // Clear login attempts of ip address
            $this->userRepository->clearLoginAttempts();

            //success response
            return $this->engineReaction(1, [
                'auth_info'     => getUserAuthInfo(1)
            ], __tr('Welcome, you are logged in successfully.'));
        }

        //error response
        return $this->engineReaction(2, null, __tr("Authentication failed, please check your credentials & try again."));
    }
}
