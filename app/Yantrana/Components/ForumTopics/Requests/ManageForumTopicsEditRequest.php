<?php
/**
* ManageForumTopicsEditRequest.php - Request file
*
* This file is part of the ForumTopics component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\ForumTopics\Requests;

use Illuminate\Http\Request;
use App\Yantrana\Base\BaseRequest;

class ManageForumTopicsEditRequest extends BaseRequest
{
    /**
     * Loosely sanitize fields.
     *------------------------------------------------------------------------ */

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
            'title'         => 'required|min:3|max:255',
            'category_id'         => 'required',
            'description'         => 'required',
            'status'        =>    'sometimes|required'
        ];

        return $rules;
    }
}
