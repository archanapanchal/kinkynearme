<?php
/**
* UserSignUpRequest.php - Request file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Requests;

use App\Yantrana\Base\BaseRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UserSignUpRequest extends BaseRequest
{
    /**
     * Secure form.
     *------------------------------------------------------------------------ */
    protected $securedForm = true;

    /**
     * Unsecured/Un encrypted form fields.
     *------------------------------------------------------------------------ */
    protected $unsecuredFields = ['first_name', 'last_name'];

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
        return [
            'first_name'        => 'required|min:3|max:45',
            'last_name'         => 'required|min:3|max:45',
            'username'          => 'required|min:5|max:45|unique:users,username',
            //'mobile_number'     => 'required|max:15|unique:users,mobile_number',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:6|max:30',
            /*'gender'              => [
                'required',
                Rule::in(array_keys(configItem('user_settings.gender'))),
            ],*/
            'repeat_password'   => 'required|min:6|max:30|same:password',
            /*'dob'                => 'sometimes|validate_age',*/
            'accepted_terms'     => 'required',
            /*'g-recaptcha-response'     => 'g_recaptcha_verify',*/
        ];
    }

    /**
     * Get the validation rules that apply to the user register request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function messages()
    {
        $ageRestriction = configItem('age_restriction');
        
        //echo "<pre>";print_r($ageRestriction);exit();
        return [
            'dob.validate_age'    => __tr('Age must be between __min__ and __max__ years', [
                '__min__' => $ageRestriction['minimum'],
                '__max__' => $ageRestriction['maximum'],
            ]),
            'accepted_terms.required' => __tr("Please accept all terms and conditions.")
        ];
    }
}
