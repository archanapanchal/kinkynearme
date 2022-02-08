<?php
/**
* ManagePlansRepository.php - Repository file
*
* This file is part of the Plans component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Plans\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Plans\Models\PlanModel;
use App\Yantrana\Components\Plans\Blueprints\ManagePlansRepositoryBlueprint;
use File;

class ManagePlansRepository extends BaseRepository implements ManagePlansRepositoryBlueprint
{
    /**
     * Constructor.
     *
     * @param Plan $plan - plan Model
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
    }

    /**
     * fetch all plans list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListData()
    {
        $dataTableConfig = [
            'searchable' => [
                'title',
                'price',
                'plan_type',
                'content'
            ]
        ];

        return PlanModel::dataTables($dataTableConfig)->toArray();
    }

    /**
     * fetch plan data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return PlanModel::where('_id', $idOrUid)->first();
        } else {
            return PlanModel::where('_uid', $idOrUid)->first();
        }
    }

    /**
     * store new plan.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function store($input)
    {
        $plan = new PlanModel;

        $keyValues = [
            'title',
            'price',
            'plan_type',
            //'feature',
            'content',
            'status',
            'users__id'
        ];

        // Store New Plan
        if ($plan->assignInputsAndSave($input, $keyValues)) {
            activityLog($plan->title . ' plan created. ');
            return true;
        }
        return false;
    }

    /**
     * Update Plan Data
     *
     * @param object $plan
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function update($plan, $updateData)
    {
        // Check if information updated
        if ($plan->modelUpdate($updateData)) {
            activityLog($plan->title . ' plan updated. ');
            return true;
        }

        return false;
    }

    /**
     * Delete plan.
     *
     * @param object $plan
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function delete($plan)
    {
        // Check if plan deleted
        if ($plan->delete()) {
            activityLog($plan->title . ' plan deleted. ');
            return  true;
        }

        return false;
    }

    /**
     * Delete plan.
     *
     * @param object $plan
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function fetchList()
    {
        return PlanModel::where('status', 1)->get()->toArray();
    }
}
