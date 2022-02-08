<?php
/**
* ManageUserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\User\Requests\{
    UserAddRequest,
    UserUpdateRequest,
    GenerateFakeUsers
};
use App\Yantrana\Components\UserSetting\Models\{
    UserPhotosModel
};
use App\Yantrana\Components\User\Models\{
    User
};
use App\Yantrana\Components\User\ManageUserEngine;
use App\Yantrana\Components\User\UserEngine;

class ManageUserController extends BaseController
{
    /**
     * @var  ManageUserEngine $manageUserEngine - ManageUser Engine
     */
    protected $manageUserEngine;

    /**
     * @var UserEngine - User Engine
     */
    protected $userEngine;

    /**
     * Constructor
     *
     * @param  ManageUserEngine $manageUserEngine - ManageUser Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(ManageUserEngine $manageUserEngine, UserEngine $userEngine)
    {
        $this->manageUserEngine = $manageUserEngine;
        $this->userEngine = $userEngine;
    }

    /**
     * Manage User List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userList($status)
    {
        return $this->loadManageView('user.manage.list');
    }

    public function uploadedPhotoList($userID)
    {

        $userDetails = User::where('_uid', $userID)->first();

            
        $userPhotoCollection =  UserPhotosModel::where('users__id', $userDetails->_id)->get();
        $userPhotos = [];
        $userPhotosFolderPath = getPathByKey('user_photos', ['{_uid}' => $userDetails->_id]);
        // check if user photos exists
        if (!__isEmpty($userPhotoCollection)) {
            foreach ($userPhotoCollection as $photo) {
                $removePhotoUrl = route('user.upload_photos.write.delete', ['photoUid' => $photo->_uid,'userId' => $userDetails->_id]);
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

        $PhotoCollection = array('userPhotos' => $userPhotos, 'photosCount' => $userPhotoCollection->count(),'photosMediaRestriction' => getMediaRestriction('photos'),'userDetails' => $userDetails);

        return $this->loadManageView('user.manage.portfoliolist',$PhotoCollection);
    }

    /**
     * Manage User List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userDataTableList($status)
    {
        return $this->manageUserEngine->prepareUsersDataTableList($status);
    }

    /**
     * Load User Photos view.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userPhotosView()
    {
        return $this->loadManageView('user.photos.list');
    }

    /**
     * Manage User Photos List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userPhotosList()
    {
        return $this->manageUserEngine->userPhotosDataTableList();
    }

    /**
     * Add new user view.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function addNewUserView()
    {
        return $this->loadManageView('user.manage.add');
    }

    /**
     * Add new user view.
     *
     * @param object UserAddRequest $request
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function processAddNewUser(UserAddRequest $request)
    {
        $processReaction = $this->manageUserEngine->processAddUser($request->all());

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo('manage.user.view_list', ['status' => 1])
        );
    }

    /**
     * Edit User.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function editUser($userUid)
    {
        $processReaction = $this->manageUserEngine->prepareUserEditData($userUid);
        //$processReaction = $this->userEngine->prepareUserProfile($userName);
        //echo '<pre>';
        //print_r($processReaction);exit;

        return $this->loadManageView('user.manage.edit', $processReaction['data']);
    }

    /**
     * Edit User.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUpdateUser($userUid, UserUpdateRequest $request)
    {
        $processReaction = $this->manageUserEngine->processUserUpdate($userUid, $request->all());

        if ($processReaction['reaction_code'] == 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.user.view_list', ['status' => 1])
            );
        }

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Process Soft delete user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserSoftDelete($userUid)
    {
        $processReaction = $this->manageUserEngine->processSoftDeleteUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process delete photo of user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserPhotoDelete($userUid, $type, $profileOrPhotoUid)
    {
        $processReaction = $this->manageUserEngine->processUserPhotoDelete($userUid, $type, $profileOrPhotoUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process Permanent delete user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserPermanentDelete($userUid)
    {
        $processReaction = $this->manageUserEngine->processPermanentDeleteUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process Restore user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processRestoreUser($userUid)
    {
        $processReaction = $this->manageUserEngine->processUserRestore($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process Block user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserBlock($userUid)
    {
        $processReaction = $this->manageUserEngine->processBlockUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Process Unblock user.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUserUnblock($userUid)
    {
        $processReaction = $this->manageUserEngine->processUnblockUser($userUid);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Get User Details.
     *
     * @param string $userUid
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function getUserDetails($userUid)
    {
        $processReaction = $this->manageUserEngine->prepareUserDetails($userUid);

        return $this->loadManageView('user.manage.details', $processReaction['data']);
    }

    /**
     * fetch Fake User Generator Options
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function fetchFakeUserOptions()
    {
        $processReaction = $this->manageUserEngine->prepareFakeUserOptions();

        return $this->loadManageView('fake-data-generator.fake-users', $processReaction['data']);
    }

    /**
     * Generate fake users.
     *
     * @param object GenerateFakeUsers $request
     * 
     * @return json object
     *---------------------------------------------------------------- */
    public function generateFakeUser(GenerateFakeUsers $request)
    {
        $processReaction = $this->manageUserEngine->processGenerateFakeUser($request->all());

        return $this->responseAction($this->processResponse($processReaction, [], [], true));
    }

    /**
     * Process Verify user profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processVerifyUserProfile($userUid)
    {
        $processReaction = $this->manageUserEngine->processVerifyUserProfile($userUid);

        return $this->responseAction($this->processResponse($processReaction, [], [], true));
    }

    /**
     * Process Unverify user profile.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function processUnverifyUserProfile($userUid)
    {
        $processReaction = $this->manageUserEngine->processUnverifyUserProfile($userUid);

        return $this->responseAction($this->processResponse($processReaction, [], [], true));
    }

    /**
     * Show user Transaction List.
     *
     *-----------------------------------------------------------------------*/
    public function manageUserTransactionList($userUid)
    {
        return $this->manageUserEngine->getUserTransactionList($userUid);
    }

    /**
     * Export User List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function userExport($status)
    {

        $processReaction = $this->manageUserEngine->prepareUsersExportList($status);
        
        $fileName = 'users.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        return response()->stream($processReaction, 200, $headers);
    }
}
