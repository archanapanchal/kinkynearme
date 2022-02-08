<?php
/**
* ManageKinksRepository.php - Repository file
*
* This file is part of the Kinks component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Kinks\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Kinks\Models\KinkModel;
use App\Yantrana\Components\Kinks\Blueprints\ManageKinksRepositoryBlueprint;
use File;

class ManageKinksRepository extends BaseRepository implements ManageKinksRepositoryBlueprint
{
    /**
     * Constructor.
     *
     * @param Kink $kink - kink Model
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
    }

    /**
     * fetch all kinks list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListData()
    {
        $dataTableConfig = [
            'searchable' => [
                'title',
            ]
        ];

        return KinkModel::dataTables($dataTableConfig)->toArray();
    }

    /**
     * fetch kink data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return KinkModel::where('_id', $idOrUid)->first();
        } else {
            return KinkModel::where('_uid', $idOrUid)->first();
        }
    }

    /**
     * store new kink.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function store($input)
    {
        $kink = new KinkModel;

        $keyValues = [
            'title',
            'value',
            'status',
            'users__id'
        ];

        // Store New Kink
        if ($kink->assignInputsAndSave($input, $keyValues)) {
            activityLog($kink->title . ' kink created. ');
            return true;
        }
        return false;
    }

    /**
     * Update Kink Data
     *
     * @param object $kink
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function update($kink, $updateData)
    {
        // Check if information updated
        if ($kink->modelUpdate($updateData)) {
            activityLog($kink->title . ' kink updated. ');
            return true;
        }

        return false;
    }

    /**
     * Delete kink.
     *
     * @param object $kink
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function delete($kink)
    {
        // Check if kink deleted
        if ($kink->delete()) {
            activityLog($kink->title . ' kink deleted. ');
            return  true;
        }

        return false;
    }
    /**
     * fetch all kinks list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchList()
    {
        return KinkModel::all()->toArray();
    }
}
