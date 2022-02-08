<?php
/**
* ManageBlockCountriesAddRequest.php - Request file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries\Requests;

use App\Yantrana\Base\BaseRequest;

class ManageBlockCountriesAddRequest extends BaseRequest
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
            'name'         => 'required|min:3|max:255|unique:block_countries',
            'status'        =>    'sometimes|required'
        ];

        return $rules;
    }
}
