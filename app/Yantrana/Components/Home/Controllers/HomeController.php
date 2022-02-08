<?php
/**
* HomeController.php - Controller file
*
* This file is part of the Home component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Home\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Home\HomeEngine;
use App\Yantrana\Components\User\UserEncounterEngine;
use App\Yantrana\Components\Filter\FilterEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Pages\ManagePagesEngine;
use App\Yantrana\Components\UserSetting\UserSettingEngine;
use App\Yantrana\Components\User\UserEngine;

class HomeController extends BaseController
{
    /**
     * @var  HomeEngine $homeEngine - Home Engine
     */
    protected $homeEngine;

    /**
     * @var  UserEncounterEngine $userEncounterEngine - UserEncounter Engine
     */
    protected $userEncounterEngine;

    /**
     * @var  FilterEngine $filterEngine - Filter Engine
     */
    protected $filterEngine;

    /**
     * @var  ManagePagesEngine $managePageEngine - Manage Pages Engine
     */
    protected $managePageEngine;

    /**
     * @var  UserEngine $userEngine - User Engine
     */
    protected $userEngine;

    /**
     * @var  UserSettingEngine $userSettingEngine - UserSetting Engine
     */
    protected $userSettingEngine;

    /**
     * Constructor
     *
     * @param  HomeEngine $homeEngine - Home Engine
     * @param  ManagePagesEngine $managePageEngine - Manage Pages Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(
        HomeEngine $homeEngine,
        UserEncounterEngine $userEncounterEngine,
        FilterEngine $filterEngine,
        ManagePagesEngine $managePageEngine,
        UserEngine $userEngine,
        UserSettingEngine $userSettingEngine
    ) {
        $this->homeEngine           = $homeEngine;
        $this->userEncounterEngine     = $userEncounterEngine;
        $this->filterEngine         = $filterEngine;
        $this->managePageEngine         = $managePageEngine;
        $this->userEngine         = $userEngine;
        $this->userSettingEngine = $userSettingEngine;
    }

    /**
     * View Home Page
     *---------------------------------------------------------------- */
    public function homePage()
    {
        // get encounter data
        $encounterData = $this->userEncounterEngine->getEncounterUserData();
        // For Random search use following function
        $basicSearchData = $this->filterEngine->prepareRandomUserData([], 12);
        // merge encounter data and basic data
        $processReaction = array_merge($encounterData['data'], $basicSearchData['data']);

        return $this->loadPublicView('home', $processReaction);
    }

    /**
     * ChangeLocale - It also managed from index.php.
     *---------------------------------------------------------------- */
    protected function changeLocale(CommonUnsecuredPostRequest $request, $localeId = null)
    {
        if (is_string($localeId)) {
            changeAppLocale($localeId);
            if (isLoggedIn()) {
                $this->userSettingEngine->processUserSettingStore('site_defaults', [
                    'selected_locale' => $localeId
                ]);
            }
        }
        if ($request->has('redirectTo')) {
            header('Location: ' . base64_decode($request->get('redirectTo')));
            exit();
        }

        return __tr('Invalid Request');
    }

    /**
     * Change Theme
     *---------------------------------------------------------------- */
    protected function changeTheme(CommonUnsecuredPostRequest $request, $themeName = null)
    {
        if (is_string($themeName) and in_array($themeName, [
            'light',
            'dark'
        ])) {
            $this->userSettingEngine->processUserSettingStore('site_defaults', [
                'selected_theme' => $themeName
            ]);
        }
        if ($request->has('redirectTo')) {
            header('Location: ' . base64_decode($request->get('redirectTo')));
            exit();
        }

        return __tr('Invalid Request');
    }

    /**
     * preview page
     *---------------------------------------------------------------- */
    public function previewPage($pageUid, $title)
    {
        $processReaction = $this->managePageEngine->previewPage($pageUid);

        return $this->loadView('pages.preview', $processReaction['data'], [
            'compress_page' => false
        ]);
    }

    /**
     * preview landing page
     *---------------------------------------------------------------- */
    public function landingPage()
    {
        $processReaction = $this->userEngine->fetchPlanByType();

        return $this->loadView('outer-home', $processReaction['data']);
    }

    /**
     * Search Matches
     *---------------------------------------------------------------- */
    public function searchMatches(CommonUnsecuredPostRequest $request)
    {
        $inputData = $request->all();
        // Set user search data into session
        session()->put('userSearchData', [
            "looking_for"   => $inputData['looking_for'],
            "min_age"       => $inputData['min_age'],
            "max_age"       => $inputData['max_age']
        ]);

        return redirect()->route('user.sign_up');
    }
}
