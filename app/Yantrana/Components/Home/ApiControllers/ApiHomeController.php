<?php
/**
* HomeController.php - Controller file
*
* This file is part of the Home component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Home\ApiControllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Home\HomeEngine;
use App\Yantrana\Components\User\UserEncounterEngine;
use App\Yantrana\Components\Filter\FilterEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Pages\ManagePagesEngine;

class ApiHomeController extends BaseController
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
        ManagePagesEngine $managePageEngine
    ) {
        $this->homeEngine           = $homeEngine;
        $this->userEncounterEngine  = $userEncounterEngine;
        $this->filterEngine         = $filterEngine;
        $this->managePageEngine         = $managePageEngine;
    }

    /**
     * View Home Page
     *---------------------------------------------------------------- */
    public function getHomePageData()
    {
        // get encounter data
        $processReaction = $this->userEncounterEngine->getEncounterUserData();


        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * View Home Page
     *---------------------------------------------------------------- */
    public function getRandomUsers()
    {
        // For Random search use following function
        $processReaction = $this->filterEngine->prepareRandomUserData([], 12);


        return $this->processResponse($processReaction, [], [], true);
    }
}
