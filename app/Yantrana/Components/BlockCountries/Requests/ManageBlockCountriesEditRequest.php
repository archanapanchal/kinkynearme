<?php
/**
* ManageBlockCountriesEditRequest.php - Request file
*
* This file is part of the BlockCountries component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BlockCountries\Requests;

use Illuminate\Http\Request;
use App\Yantrana\Base\BaseRequest;

class ManageBlockCountriesEditRequest extends BaseRequest
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
            'name'         => 'required|min:3|max:255|unique_block_country_name',
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
            'name.unique_block_country_name' => 'The Country has already been taken'
        ];
    }
}
