<?php
/**
* ManageForumCategoriesRepository.php - Repository file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumCategories\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\ForumCategories\Models\ForumCategoryModel;
use App\Yantrana\Components\ForumCategories\Blueprints\ManageForumCategoriesRepositoryBlueprint;
use File;

class ManageForumCategoriesRepository extends BaseRepository implements ManageForumCategoriesRepositoryBlueprint
{
    /**
     * Constructor.
     *
     * @param ForumCategory $forumCategory - forumCategory Model
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchListData()
    {
        $dataTableConfig = [
            'searchable' => [
                'forum_categories.title',
                'parent_categories.title',
            ]
        ];

        //return ForumCategoryModel::dataTables($dataTableConfig)->toArray();


        $query = ForumCategoryModel::leftJoin('forum_categories as parent_categories', 'parent_categories._id', '=', 'forum_categories.parent_category')
            ->select(
                __nestedKeyValues([
                    'forum_categories' => [
                        '_id',
                        '_uid',
                        'created_at',
                        'updated_at',
                        'status',
                        'title',
                        'parent_category',
                    ],
                    'parent_categories' => [
                        'title as parent',
                    ]
                ])
            )
            ->dataTables($dataTableConfig);

        return $query->toArray();
    }

    /**
     * fetch forumCategory data.
     *
     * @param int $idOrUid
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        //check is numeric
        if (is_numeric($idOrUid)) {
            return ForumCategoryModel::where('_id', $idOrUid)->first();
        } else {
            return ForumCategoryModel::where('_uid', $idOrUid)->first();
        }
    }

    /**
     * store new forumCategory.
     *
     * @param array $input
     *
     * @return array
     *---------------------------------------------------------------- */
    public function store($input)
    {
        $forumCategory = new ForumCategoryModel;

        $keyValues = [
            'title',
            'parent_category',
            'value',
            'status',
            'users__id'
        ];

        // Store New ForumCategory
        if ($forumCategory->assignInputsAndSave($input, $keyValues)) {
            activityLog($forumCategory->title . ' forumCategory created. ');
            return true;
        }
        return false;
    }

    /**
     * Update ForumCategory Data
     *
     * @param object $forumCategory
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function update($forumCategory, $updateData)
    {
        // Check if information updated
        if ($forumCategory->modelUpdate($updateData)) {
            activityLog($forumCategory->title . ' forumCategory updated. ');
            return true;
        }

        return false;
    }

    /**
     * Delete forumCategory.
     *
     * @param object $forumCategory
     *
     * @return bool
     *---------------------------------------------------------------- */
    public function delete($forumCategory)
    {
        // Check if forumCategory deleted
        if ($forumCategory->delete()) {
            activityLog($forumCategory->title . ' forumCategory deleted. ');
            return  true;
        }

        return false;
    }

    /**
     * fetch all forum-categories list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchList()
    {
        return ForumCategoryModel::all()->toArray();
    }
}
