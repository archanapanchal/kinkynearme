<?php
/**
* ManageBlockCountriesController.php - Controller file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\BlockCountries\ManageBlockCountriesEngine;
use App\Yantrana\Components\BlockCountries\Requests\{ManageBlockCountriesAddRequest, ManageBlockCountriesEditRequest};
use Carbon\Carbon;

class ManageBlockCountriesController extends BaseController
{
    /**
     * @var ManageBlockCountriesEngine - ManageBlockCountries Engine
     */
    protected $manageBlockCountriesEngine;

    /**
     * Constructor.
     *
     * @param ManageBlockCountriesEngine $manageBlockCountriesEngine - ManageBlockCountries Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageBlockCountriesEngine $manageBlockCountriesEngine)
    {
        $this->manageBlockCountriesEngine = $manageBlockCountriesEngine;
    }

    /**
     * Show BlockCountries List View.
     *
     *-----------------------------------------------------------------------*/
    public function blockCountryListView()
    {
        return $this->loadManageView('blockCountries.manage.list');
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getDatatableData()
    {
        return $this->manageBlockCountriesEngine->prepareBlockCountryList();
    }

    /**
     * Show Block Country Add View.
     *
     *-----------------------------------------------------------------------*/
    public function blockCountryAddView()
    {
        return $this->loadManageView('blockCountries.manage.add');
    }

    /**
     * Handle add new Block Country request.
     *
     * @param ManageBlockCountriesAddRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processAddBlockCountry(ManageBlockCountriesAddRequest $request)
    {
        $processReaction = $this->manageBlockCountriesEngine
            ->prepareForAddNewBlockCountry($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.blockCountry.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show BlockCountry Edit View.
     *
     *-----------------------------------------------------------------------*/
    public function blockCountryEditView($blockCountryUId)
    {
        $processReaction = $this->manageBlockCountriesEngine->prepareUpdateData($blockCountryUId);

        return $this->loadManageView('blockCountries.manage.edit', $processReaction['data']);
    }

    /**
     * Handle edit new blockCountry request.
     *
     * @param ManageBlockCountriesEditRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processEditBlockCountry(ManageBlockCountriesEditRequest $request, $blockCountryUId)
    {
        $processReaction = $this->manageBlockCountriesEngine
            ->prepareForEditNewBlockCountry($request->all(), $blockCountryUId);

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.blockCountry.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Handle delete blockCountry data request.
     *
     * @param int $blockCountryUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function delete($blockCountryUId)
    {
        $processReaction = $this->manageBlockCountriesEngine->processDelete($blockCountryUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
