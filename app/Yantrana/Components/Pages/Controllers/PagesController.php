<?php
/**
* ManagePagesController.php - Controller file
*
* This file is part of the Pages component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Pages\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Pages\ManagePagesEngine;
use App\Yantrana\Components\Pages\Requests\{ManagePagesAddRequest, ManagePagesEditRequest};
use Carbon\Carbon;
use App\Yantrana\Components\User\UserEngine;
class PagesController extends BaseController
{
    /**
     * @var ManagePagesEngine - ManagePages Engine
     */
    protected $managePagesEngine;
    protected $userEngine;
    /**
     * Constructor.
     *
     * @param ManagePagesEngine $managePagesEngine - ManagePages Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManagePagesEngine $managePagesEngine,UserEngine $userEngine)
    {
        $this->managePagesEngine = $managePagesEngine;
        $this->userEngine = $userEngine;
    }

    /**
     * Show Page View.
     *
     *-----------------------------------------------------------------------*/
    public function pageView($pageId=1,Request $request)
    {   
        /*search*/
        $search = $request['search'] ??"";
        $search_data = "";
        $search_5_data = "";
        if ($search != "" ) {
            $search_data = $this->userEngine->searchRequestFromHome($search);
            $search_5_data =array_slice($search_data, 0,5);
        }/*end search*/
        $processReaction = $this->managePagesEngine->prepareData($pageId);
        /*search*/
        $processReaction['data']['search_data'] = $search_data;
        $processReaction['data']['search_5_data'] = $search_5_data;
        /*end search*/
        return $this->loadPageView('pages.view', $processReaction['data']);
    }
}
