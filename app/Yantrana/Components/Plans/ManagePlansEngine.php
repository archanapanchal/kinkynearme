<?php
/**
* ManagePlansEngine.php - Main component file
*
* This file is part of the Plans component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Plans;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Plans\Repositories\ManagePlansRepository;

class ManagePlansEngine extends BaseEngine
{
    /**
     * @var ManagePlansRepository - ManagePlans Repository
     */
    protected $managePlansRepository;

    /**
     * Constructor.
     *
     * @param ManagePlansRepository $managePlansRepository - ManagePlans Repository
     *-----------------------------------------------------------------------*/
    public function __construct(ManagePlansRepository $managePlansRepository)
    {
        $this->managePlansRepository = $managePlansRepository;
    }

    /**
     * get plan list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function preparePlanList()
    {
        $planCollection = $this->managePlansRepository->fetchListData();

        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'price',
            'plan_type',
            'formattedPlanType' => function ($key) {
                //check is not empty
                if (!__isEmpty($key['plan_type'])) {
                    return configItem('plan_settings.type', $key['plan_type']);
                }

                return '-';
            },
            'created_at' => function ($planData) {
                return formatDate($planData['created_at']);
            },
            'updated_at' => function ($planData) {
                return formatDate($planData['updated_at']);
            },
            'status' => function ($planData) {
                return configItem('status_codes', $planData['status']);
            }
        ];

        return $this->dataTableResponse($planCollection, $requireColumns);
    }

    /**
     * Process add new plan.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddNewPlan($inputData)
    {
        $storeData = [
            'title'         => $inputData['title'],
            'price'         => $inputData['price'],
            'plan_type'         => $inputData['plan_type'],
            //'feature'         => json_encode($inputData['feature']),
            'content'         => $inputData['description'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id()
        ];

        //Check if plan added
        if ($this->managePlansRepository->store($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Plan added successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Plan not added.'));
    }

    /**
     * get plan edit data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUpdateData($planUId)
    {
        $planCollection = $this->managePlansRepository->fetch($planUId);

        //if is empty
        if (__isEmpty($planCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Plan does not exist'));
        }

        $planEditData = [];
        if (!__isEmpty($planCollection)) {
            $planEditData = [
                '_id'             => $planCollection['_id'],
                '_uid'             => $planCollection['_uid'],
                'title'         => $planCollection['title'],
                'price'         => $planCollection['price'],
                'plan_type'         => $planCollection['plan_type'],
                //'feature'         => json_decode($planCollection['feature']),
                'description'     => $planCollection['content'],
                'created_at'     => formatDate($planCollection['created_at']),
                'updated_at'     => formatDate($planCollection['updated_at']),
                'status'         => $planCollection['status'],
            ];
        }

        return $this->engineReaction(1, [
            'planEditData' => $planEditData
        ]);
    }

    /**
     * Process add new plan.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForEditNewPlan($inputData, $planUId)
    {
        $planCollection = $this->managePlansRepository->fetch($planUId);

        //if is empty
        if (__isEmpty($planCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Plan does not exist'));
        }

        //update data
        $updateData = [
            'title'         => $inputData['title'],
            'price'         => $inputData['price'],
            'plan_type'         => $inputData['plan_type'],
            //'feature'         => json_encode($inputData['feature']),
            'content'         => $inputData['description'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2
        ];

        //Check if plan updated
        if ($this->managePlansRepository->update($planCollection, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Plan updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Plan not updated.'));
    }

    /**
     * Process delete.
     *
     * @param int planUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDelete($planUId)
    {
        $planCollection = $this->managePlansRepository->fetch($planUId);

        //if is empty
        if (__isEmpty($planCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Plan does not exist.'));
        }

        //Check if plan deleted
        if ($this->managePlansRepository->delete($planCollection)) {
            return $this->engineReaction(1, [
                'planUId' => $planCollection->_uid
            ], __tr('Plan deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('Plan not deleted.'));
    }

    /**
     * fetch all plans list by type.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListByType()
    {
        $plans = $this->managePlansRepository->fetchList();

        $list = [];

        foreach ($plans as $plan) {
            if (!isset($list[$plan['plan_type']])) {
                $list[$plan['plan_type']] = [];
            }
            $list[$plan['plan_type']][] = $plan; 
        }

        return $this->engineReaction(1, [
            'plans' => $list
        ]);
    }
}
