<?php
/**
* ManageBlockCountriesEngine.php - Main component file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\BlockCountries\Repositories\ManageBlockCountriesRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ManageBlockCountriesEngine extends BaseEngine
{
    /**
     * @var ManageBlockCountriesRepository - ManageBlockCountries Repository
     */
    protected $manageBlockCountriesRepository;

    /**
     * Constructor.
     *
     * @param ManageBlockCountriesRepository $manageBlockCountriesRepository - ManageBlockCountries Repository
     *-----------------------------------------------------------------------*/
    public function __construct(ManageBlockCountriesRepository $manageBlockCountriesRepository)
    {
        $this->manageBlockCountriesRepository = $manageBlockCountriesRepository;
    }

    /**
     * get BlockCountry list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareBlockCountryList()
    {
        $blockCountryCollection = $this->manageBlockCountriesRepository->fetchListData();

        $requireColumns = [
            '_id',
            '_uid',
            'name',
            'created_at' => function ($blockCountryData) {
                return formatDate($blockCountryData['created_at']);
            },
            'updated_at' => function ($blockCountryData) {
                return formatDate($blockCountryData['updated_at']);
            },
        ];

        return $this->dataTableResponse($blockCountryCollection, $requireColumns);
    }

    /**
     * Process add new BlockCountry.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddNewBlockCountry($inputData)
    {
        $storeData = [
            'name'         => Str::ucfirst($inputData['name']),
            'value'         => Str::slug($inputData['name'], '-'),
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id()
        ];

        //Check if BlockCountry added
        if ($this->manageBlockCountriesRepository->store($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Country Blocked successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('This Country is not blocked.'));
    }

    /**
     * get Block Country edit data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUpdateData($blockCountryUId)
    {
        $blockCountryCollection = $this->manageBlockCountriesRepository->fetch($blockCountryUId);

        //if is empty
        if (__isEmpty($blockCountryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Block Country does not exist'));
        }

        $blockCountryEditData = [];
        if (!__isEmpty($blockCountryCollection)) {
            $blockCountryEditData = [
                '_id'             => $blockCountryCollection['_id'],
                '_uid'             => $blockCountryCollection['_uid'],
                'name'         => Str::ucfirst($blockCountryCollection['name']),
                'created_at'     => formatDate($blockCountryCollection['created_at']),
                'updated_at'     => formatDate($blockCountryCollection['updated_at']),
                'status'         => $blockCountryCollection['status'],
            ];
        }

        return $this->engineReaction(1, [
            'blockCountryEditData' => $blockCountryEditData
        ]);
    }

    /**
     * Process add new Block Country.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForEditNewBlockCountry($inputData, $blockCountryUId)
    {
        $blockCountryCollection = $this->manageBlockCountriesRepository->fetch($blockCountryUId);

        //if is empty
        if (__isEmpty($blockCountryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Block Country does not exist'));
        }

        //update data
        $updateData = [
            'name'         => Str::ucfirst($inputData['name']),
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2
        ];

        //Check if block country updated
        if ($this->manageBlockCountriesRepository->update($blockCountryCollection, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Blocked country updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Blocked Country not updated.'));
    }

    /**
     * Process delete.
     *
     * @param int blockCountryUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDelete($blockCountryUId)
    {
        $blockCountryCollection = $this->manageBlockCountriesRepository->fetch($blockCountryUId);

        //if is empty
        if (__isEmpty($blockCountryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('Block Country does not exist.'));
        }

        //Check if Block Country deleted
        if ($this->manageBlockCountriesRepository->delete($blockCountryCollection)) {
            return $this->engineReaction(1, [
                'blockCountryUId' => $blockCountryCollection->_uid
            ], __tr('Blocked Country deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('Blocked Country not deleted.'));
    }

    /**
     * Generate Json.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareJson()
    {
        $blockCountryCollection = $this->manageBlockCountriesRepository->fetchList();

        $list = [];
        foreach ($blockCountryCollection as $key => $blockCountry) {
            $list[$blockCountry['value']] = $blockCountry['name'];
        }

        Storage::disk('local')->put('blockCountries.json', json_encode($list));
    }
}
