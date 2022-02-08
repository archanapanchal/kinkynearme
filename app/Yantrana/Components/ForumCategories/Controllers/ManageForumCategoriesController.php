<?php
/**
* ManageForumCategoriesController.php - Controller file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumCategories\Controllers;

use Illuminate\Http\Request;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\ForumCategories\ManageForumCategoriesEngine;
use App\Yantrana\Components\ForumCategories\Requests\{ManageForumCategoriesAddRequest, ManageForumCategoriesEditRequest};
use Carbon\Carbon;

class ManageForumCategoriesController extends BaseController
{
    /**
     * @var ManageForumCategoriesEngine - ManageForumCategories Engine
     */
    protected $manageForumCategoriesEngine;

    /**
     * Constructor.
     *
     * @param ManageForumCategoriesEngine $manageForumCategoriesEngine - ManageForumCategories Engine
     *-----------------------------------------------------------------------*/
    public function __construct(ManageForumCategoriesEngine $manageForumCategoriesEngine)
    {
        $this->manageForumCategoriesEngine = $manageForumCategoriesEngine;
    }

    /**
     * Show ForumCategory List View.
     *
     *-----------------------------------------------------------------------*/
    public function forumCategoryListView()
    {
        return $this->loadManageView('forum-categories.manage.list');
    }

    /**
     * Get Datatable data.
     *
     *-----------------------------------------------------------------------*/
    public function getDatatableData()
    {
        return $this->manageForumCategoriesEngine->prepareForumCategoryList();
    }

    /**
     * Show ForumCategory Add View.
     *
     *-----------------------------------------------------------------------*/
    public function forumCategoryAddView()
    {
        return $this->loadManageView('forum-categories.manage.add', [
                    'parentCategories' => $this->manageForumCategoriesEngine->prepareParentForumCategoryList()
                ]);
    }

    /**
     * Handle add new forumCategory request.
     *
     * @param ManageForumCategoriesAddRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processAddForumCategory(ManageForumCategoriesAddRequest $request)
    {
        $processReaction = $this->manageForumCategoriesEngine
            ->prepareForAddNewForumCategory($request->all());

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.forum-category.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Show ForumCategory Edit View.
     *
     *-----------------------------------------------------------------------*/
    public function forumCategoryEditView($forumCategoryUId)
    {
        $processReaction = $this->manageForumCategoriesEngine->prepareUpdateData($forumCategoryUId);

        $processReaction['data']['parentCategories'] = $this->manageForumCategoriesEngine->prepareParentForumCategoryList($forumCategoryUId);
        return $this->loadManageView('forum-categories.manage.edit', $processReaction['data']);
    }

    /**
     * Handle edit new forumCategory request.
     *
     * @param ManageForumCategoriesEditRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processEditForumCategory(ManageForumCategoriesEditRequest $request, $forumCategoryUId)
    {
        $processReaction = $this->manageForumCategoriesEngine
            ->prepareForEditNewForumCategory($request->all(), $forumCategoryUId);

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.forum-category.view')
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Handle delete forumCategory data request.
     *
     * @param int $forumCategoryUId
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function delete($forumCategoryUId)
    {
        $processReaction = $this->manageForumCategoriesEngine->processDelete($forumCategoryUId);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
