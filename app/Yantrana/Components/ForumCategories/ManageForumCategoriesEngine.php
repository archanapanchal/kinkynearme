<?php
/**
* ManageForumCategoriesEngine.php - Main component file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumCategories;

use Auth;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\ForumCategories\Repositories\ManageForumCategoriesRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ManageForumCategoriesEngine extends BaseEngine
{
    /**
     * @var ManageForumCategoriesRepository - ManageForumCategories Repository
     */
    protected $manageForumCategoriesRepository;

    /**
     * Constructor.
     *
     * @param ManageForumCategoriesRepository $manageForumCategoriesRepository - ManageForumCategories Repository
     *-----------------------------------------------------------------------*/
    public function __construct(ManageForumCategoriesRepository $manageForumCategoriesRepository)
    {
        $this->manageForumCategoriesRepository = $manageForumCategoriesRepository;
    }

    /**
     * get forumCategory list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareForumCategoryList()
    {
        $forumCategoryCollection = $this->manageForumCategoriesRepository->fetchListData();

        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'parent_category',
            'parent',
            'created_at' => function ($forumCategoryData) {
                return formatDate($forumCategoryData['created_at']);
            },
            'updated_at' => function ($forumCategoryData) {
                return formatDate($forumCategoryData['updated_at']);
            },
        ];

        return $this->dataTableResponse($forumCategoryCollection, $requireColumns);
    }

    /**
     * Process add new forumCategory.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForAddNewForumCategory($inputData)
    {
        $storeData = [
            'title'         => $inputData['title'],
            // 'parent_category'         => $inputData['parent_category'],
            'value'         => Str::slug($inputData['title'], '-'),
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2,
            'users__id'     => Auth::id()
        ];

        //Check if forumCategory added
        if ($this->manageForumCategoriesRepository->store($storeData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumCategory added successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('ForumCategory not added.'));
    }

    /**
     * get forumCategory edit data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUpdateData($forumCategoryUId)
    {
        $forumCategoryCollection = $this->manageForumCategoriesRepository->fetch($forumCategoryUId);

        //if is empty
        if (__isEmpty($forumCategoryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumCategory does not exist'));
        }

        $forumCategoryEditData = [];
        if (!__isEmpty($forumCategoryCollection)) {
            $forumCategoryEditData = [
                '_id'             => $forumCategoryCollection['_id'],
                '_uid'             => $forumCategoryCollection['_uid'],
                'title'         => $forumCategoryCollection['title'],
                'parent_category'         => $forumCategoryCollection['parent_category'],
                'created_at'     => formatDate($forumCategoryCollection['created_at']),
                'updated_at'     => formatDate($forumCategoryCollection['updated_at']),
                'status'         => $forumCategoryCollection['status'],
            ];
        }

        return $this->engineReaction(1, [
            'forumCategoryEditData' => $forumCategoryEditData
        ]);
    }

    /**
     * Process add new forumCategory.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareForEditNewForumCategory($inputData, $forumCategoryUId)
    {
        $forumCategoryCollection = $this->manageForumCategoriesRepository->fetch($forumCategoryUId);

        //if is empty
        if (__isEmpty($forumCategoryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumCategory does not exist'));
        }

        //update data
        $updateData = [
            'title'         => $inputData['title'],
            // 'parent_category'         => $inputData['parent_category'],
            'status'        => (isset($inputData['status']) and $inputData['status'] == 'on') ? 1 : 2
        ];

        //Check if forumCategory updated
        if ($this->manageForumCategoriesRepository->update($forumCategoryCollection, $updateData)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumCategory updated successfully'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('ForumCategory not updated.'));
    }

    /**
     * Process delete.
     *
     * @param int forumCategoryUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processDelete($forumCategoryUId)
    {
        $forumCategoryCollection = $this->manageForumCategoriesRepository->fetch($forumCategoryUId);

        //if is empty
        if (__isEmpty($forumCategoryCollection)) {
            return $this->engineReaction(1, ['show_message' => true], __tr('ForumCategory does not exist.'));
        }

        //Check if forumCategory deleted
        if ($this->manageForumCategoriesRepository->delete($forumCategoryCollection)) {
            return $this->engineReaction(1, [
                'forumCategoryUId' => $forumCategoryCollection->_uid
            ], __tr('ForumCategory deleted successfully.'));
        }

        return $this->engineReaction(18, ['show_message' => true], __tr('ForumCategory not deleted.'));
    }


    /**
     * Get parent forum categories
     *
     * @param int forumCategoryUId
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareParentForumCategoryList($forumCategoryUId = null)
    {
        $forumCategoryCollection = $this->manageForumCategoriesRepository->fetchList();

        $list = [];

        foreach ($forumCategoryCollection as $key => $forumCategory) {
            if (!$forumCategory['parent_category'] && $forumCategoryUId != $forumCategory['_uid']) {
                $list[$forumCategory['_id']] = $forumCategory['title'];
            }
        }

        return $list;
    }
}
