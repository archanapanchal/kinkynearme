<?php
/**
* ManageForumCategoriesEditRequest.php - Request file
*
* This file is part of the ForumCategories component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumCategories\Requests;

use Illuminate\Http\Request;
use App\Yantrana\Base\BaseRequest;

class ManageForumCategoriesEditRequest extends BaseRequest
{
    /**
     * Loosely sanitize fields.
     *------------------------------------------------------------------------ */
    protected $looseSanitizationFields = ['description' => true];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the add author client post request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function rules()
    {
        $rules = [
            'title'         => 'required|min:3|max:255|unique_forum_category_title',
            'status'        =>    'sometimes|required'
        ];

        return $rules;
    }

    /**
     * Get the validation rules that apply to the user login request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function messages()
    {
        return [
            'title.unique_forum_category_title' => 'The title has already been taken'
        ];
    }
}
