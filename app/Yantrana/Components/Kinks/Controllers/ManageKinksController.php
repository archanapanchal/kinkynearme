<?php
/**
* ManageKinksController.php - Controller file
*
* This file is part of the Kinks component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Kinks\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Kinks\ManageKinksEngine;
use App\Yantrana\Components\Kinks\Requests\{ManageKinksAddRequest, ManageKinksEditRequest};
use Carbon\Carbon;

class ManageKinksController extends BaseController
{
    /**
     * @var ManageKinksEngine - ManageKinks Engine
     */
    protected $manageKinksEngine;

    /**
     * Constructor.
     *
     * @param ManageKinksEngine $manageKinksEngine - ManageKinks Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageKinksEngine $manageKinksEngine)
    {
        $this->manageKinksEngine = $manageKinksEngine;
    }

    /**
     * Show Kink List View.
     *
     *-----------------------------------------------------------------------*/
    public function kinkListView()
    {
        return $this->loadManageView('kinks.manage.list');
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getDatatableData()
    {
        return $this->manageKinksEngine->prepareKinkList();
    }

    /**
     * Show Kink Add View.
     *
     *-----------------------------------------------------------------------*/
    public function kinkAddView()
    {
        return $this->loadManageView('kinks.manage.add');
    }

    /**
     * Handle add new kink request.
     *
     * @param ManageKinksAddRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processAddKink(ManageKinksAddRequest $request)
    {
        $processReaction = $this->manageKinksEngine
            ->prepareForAddNewKink($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.kink.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show Kink Edit View.
     *
     *-----------------------------------------------------------------------*/
    public function kinkEditView($kinkUId)
    {
        $processReaction = $this->manageKinksEngine->prepareUpdateData($kinkUId);

        return $this->loadManageView('kinks.manage.edit', $processReaction['data']);
    }

    /**
     * Handle edit new kink request.
     *
     * @param ManageKinksEditRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processEditKink(ManageKinksEditRequest $request, $kinkUId)
    {
        $processReaction = $this->manageKinksEngine
            ->prepareForEditNewKink($request->all(), $kinkUId);

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.kink.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Handle delete kink data request.
     *
     * @param int $kinkUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function delete($kinkUId)
    {
        $processReaction = $this->manageKinksEngine->processDelete($kinkUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
