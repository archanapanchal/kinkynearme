<?php
/**
* ManageBlockCountriesRepository.php - Repository file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\BlockCountries\Models\BlockCountryModel;
use App\Yantrana\Components\BlockCountries\Blueprints\ManageBlockCountriesRepositoryBlueprint;
use File;

class ManageBlockCountriesRepository extends BaseRepository implements ManageBlockCountriesRepositoryBlueprint
{
    /**
     * Constructor.
     *
     * @param BlockCountry $blockCountry - BlockCountry Model
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
    }

    /**
     * fetch all BlockCountries list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListData()
    {
        $dataTableConfig = [
            'searchable' => [
                'name',
            ]
        ];

        return BlockCountryModel::dataTables($dataTableConfig)->toArray();
    }

    /**
     * fetch Block Country data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return BlockCountryModel::where('_id', $idOrUid)->first();
        } else {
            return BlockCountryModel::where('_uid', $idOrUid)->first();
        }
    }

    /**
     * store new BlockCountry.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function store($input)
    {
        $blockCountry = new BlockCountryModel;

        $keyValues = [
            'name',
            'value',
            'status',
            'users__id'
        ];

        // Store New Block Country
        if ($blockCountry->assignInputsAndSave($input, $keyValues)) {
            activityLog($blockCountry->name . ' Block Country created. ');
            return true;
        }
        return false;
    }

    /**
     * Update Block Country Data
     *
     * @param object $blockCountry
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function update($blockCountry, $updateData)
    {
        // Check if information updated
        if ($blockCountry->modelUpdate($updateData)) {
            activityLog($blockCountry->name . ' Block Country updated. ');
            return true;
        }

        return false;
    }

    /**
     * Delete BlockCountry.
     *
     * @param object $blockCountry
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function delete($blockCountry)
    {
        // Check if BlockCountry deleted
        if ($blockCountry->delete()) {
            activityLog($blockCountry->name . ' Block Country deleted. ');
            return  true;
        }

        return false;
    }
    /**
     * fetch all BlockCountries list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchList()
    {
        return BlockCountryModel::all()->toArray();
    }
}
