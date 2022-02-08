<?php
/**
* ManageKinksEngine.php - Main component file
*
* This file is part of the Kinks component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Kinks;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Kinks\Repositories\ManageKinksRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ManageKinksEngine extends BaseEngine
{
    /**
     * @var ManageKinksRepository - ManageKinks Repository
     */
    protected $manageKinksRepository;

    /**
     * Constructor.
     *
     * @param ManageKinksRepository $manageKinksRepository - ManageKinks Repository
     *-----------------------------------------------------------------------*/
    public function __construct(ManageKinksRepository $manageKinksRepository)
    {
        $this->manageKinksRepository = $manageKinksRepository;
    }

    /**
     * get kink list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareKinkList()
    {
        $kinkCollection = $this->manageKinksRepository->fetchListData();

        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'created_at' => function ($kinkData) {
                return formatDate($kinkData['created_at']);
            },
            'updated_at' => function ($kinkData) {
                return formatDate($kinkData['updated_at']);
            },
        ];

        return $this->dataTableResponse($kinkCollection, $requireColumns);
    }

    /**
     * Process add new kink.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddNewKink($inputData)
    {
        $storeData = [
            'title'         => $inputData['title'],
            'value'         => Str::slug($inputData['title'], '-'),
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id()
        ];

        //Check if kink added
        if ($this->manageKinksRepository->store($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Kink added successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Kink not added.'));
    }

    /**
     * get kink edit data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUpdateData($kinkUId)
    {
        $kinkCollection = $this->manageKinksRepository->fetch($kinkUId);

        //if is empty
        if (__isEmpty($kinkCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Kink does not exist'));
        }

        $kinkEditData = [];
        if (!__isEmpty($kinkCollection)) {
            $kinkEditData = [
                '_id'             => $kinkCollection['_id'],
                '_uid'             => $kinkCollection['_uid'],
                'title'         => $kinkCollection['title'],
                'created_at'     => formatDate($kinkCollection['created_at']),
                'updated_at'     => formatDate($kinkCollection['updated_at']),
                'status'         => $kinkCollection['status'],
            ];
        }

        return $this->engineReaction(1, [
            'kinkEditData' => $kinkEditData
        ]);
    }

    /**
     * Process add new kink.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForEditNewKink($inputData, $kinkUId)
    {
        $kinkCollection = $this->manageKinksRepository->fetch($kinkUId);

        //if is empty
        if (__isEmpty($kinkCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Kink does not exist'));
        }

        //update data
        $updateData = [
            'title'         => $inputData['title'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2
        ];

        //Check if kink updated
        if ($this->manageKinksRepository->update($kinkCollection, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Kink updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Kink not updated.'));
    }

    /**
     * Process delete.
     *
     * @param int kinkUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDelete($kinkUId)
    {
        $kinkCollection = $this->manageKinksRepository->fetch($kinkUId);

        //if is empty
        if (__isEmpty($kinkCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Kink does not exist.'));
        }

        //Check if kink deleted
        if ($this->manageKinksRepository->delete($kinkCollection)) {
            return $this->engineReaction(1, [
                'kinkUId' => $kinkCollection->_uid
            ], __tr('Kink deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('Kink not deleted.'));
    }

    /**
     * Generate Json.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareJson()
    {
        $kinkCollection = $this->manageKinksRepository->fetchList();

        $list = [];
        foreach ($kinkCollection as $key => $kink) {
            $list[$kink['value']] = $kink['title'];
        }

        Storage::disk('local')->put('kinks.json', json_encode($list));
    }
}
