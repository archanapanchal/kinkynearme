<?php
/**
* UserUpdateRequest.php - Request file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Requests;

use App\Yantrana\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends BaseRequest
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
            'first_name'       => 'required|min:3|max:45',
            /*'last_name'        => 'required|min:3|max:45',*/
            'username'         => 'required|min:5|max:45|' . Rule::unique('users')->ignore($userUid, '_uid'),
            /*'mobile_number'    => 'required|max:15|' . Rule::unique('users')->ignore($userUid, '_uid'),*/
            'email'            => 'required|email|' . Rule::unique('users')->ignore($userUid, '_uid')
        ];
    }
}
