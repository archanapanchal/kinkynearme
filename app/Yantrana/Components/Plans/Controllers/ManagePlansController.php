<?php
/**
* ManagePlansController.php - Controller file
*
* This file is part of the Plans component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Plans\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Plans\ManagePlansEngine;
use App\Yantrana\Components\Plans\Requests\{ManagePlansAddRequest, ManagePlansEditRequest};
use Carbon\Carbon;

class ManagePlansController extends BaseController
{
    /**
     * @var ManagePlansEngine - ManagePlans Engine
     */
    protected $managePlansEngine;

    /**
     * Constructor.
     *
     * @param ManagePlansEngine $managePlansEngine - ManagePlans Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManagePlansEngine $managePlansEngine)
    {
        $this->managePlansEngine = $managePlansEngine;
    }

    /**
     * Show Plan List View.
     *
     *-----------------------------------------------------------------------*/
    public function planListView()
    {
        return $this->loadManageView('plans.manage.list');
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getDatatableData()
    {
        return $this->managePlansEngine->preparePlanList();
    }

    /**
     * Show Plan Add View.
     *
     *-----------------------------------------------------------------------*/
    public function planAddView()
    {
        return $this->loadManageView('plans.manage.add', [
                    'types' => configItem('plan_settings.type'),
                    'features' => configItem('plan_settings.feature')
                ]);
    }

    /**
     * Handle add new plan request.
     *
     * @param ManagePlansAddRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processAddPlan(ManagePlansAddRequest $request)
    {
        $processReaction = $this->managePlansEngine
            ->prepareForAddNewPlan($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.plan.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show Plan Edit View.
     *
     *-----------------------------------------------------------------------*/
    public function planEditView($planUId)
    {
        $processReaction = $this->managePlansEngine->prepareUpdateData($planUId);

        $processReaction['data']['types'] = configItem('plan_settings.type');
        $processReaction['data']['features'] = configItem('plan_settings.feature');
        return $this->loadManageView('plans.manage.edit', $processReaction['data']);
    }

    /**
     * Handle edit new plan request.
     *
     * @param ManagePlansEditRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processEditPlan(ManagePlansEditRequest $request, $planUId)
    {
        $processReaction = $this->managePlansEngine
            ->prepareForEditNewPlan($request->all(), $planUId);

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.plan.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Handle delete plan data request.
     *
     * @param int $planUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function delete($planUId)
    {
        $processReaction = $this->managePlansEngine->processDelete($planUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
