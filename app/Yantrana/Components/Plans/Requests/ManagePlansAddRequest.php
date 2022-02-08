<?php
/**
* ManagePlansAddRequest.php - Request file
*
* This file is part of the Plans component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Plans\Requests;

use App\Yantrana\Base\BaseRequest;

class ManagePlansAddRequest extends BaseRequest
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
            //'title'         => 'required|min:3|max:255|unique:plans',
            'title'         => 'required|min:3|max:255',
            'price'         => 'required|numeric',
            'plan_type'     => 'required',
            //'feature'     => 'required',
            'description'     => 'required',
            'status'        =>    'sometimes|required'
        ];

        return $rules;
    }
}
