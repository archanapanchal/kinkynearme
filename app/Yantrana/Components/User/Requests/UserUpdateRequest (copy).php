<?php
/**
* UserUpdateRequest.php - Request file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Requests;

use App\Yantrana\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UserProfileBuildRequest extends BaseRequest
{
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
     * Get the validation rules that apply to the user register request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function rules()
    {
        $userUid = $this->route('userUid');

        return [
            'gender'              => [
                'required',
                Rule::in(array_keys(configItem('user_settings.gender'))),
            'dob'                => 'sometimes|validate_age',
        ];
    }
}
