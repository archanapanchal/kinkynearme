<?php

use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Custom validation rules for check unique title for account
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('unique_title', function ($attribute, $value, $parameters) {
    $title = Str::slug($value, '-');
    $account = App\Yantrana\Services\ServiceControl\Models\AccountModel::where('title', $title)->first();

    if (!__isEmpty($account)) {
        return false;
    }

    return true;
});

/**
 * Custom validation rules for check disposable emails
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('check_disposable_email', function ($attribute, $value, $parameters) {
    $disposableEmailData = [];
    try {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://disposable.debounce.io/?email=" . $value,
            // CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_TIMEOUT => 10
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpcode !== 200) {
            throw new Exception("Something went wrong on server.", 1);
        }
        // Close request to clear up some resources
        curl_close($curl);

        $disposableEmailData = json_decode($resp, true);
    } catch (Exception $e) {
        $disposableEmailData = [
            'disposable' => true
        ];
    }

    if ($disposableEmailData['disposable'] == 'false') {
        return true;
    }

    return false;
});

/**
 * Custom validation rules for check unique email address -
 * for author users
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('unique_email', function ($attribute, $value, $parameters) {
    $email      = strtolower($value);

    $user = App\Yantrana\Components\User\Models\User::where('email', $email)->first();
    $accountUser = null;

    // Check if user is exist
    if (!__isEmpty($user)) {
        $userId = $user->_id;

        $accountUser = App\Yantrana\Components\User\Models\UserAuthorityModel::where([
            'users__id' => $userId
        ])->first();
    }

    // Check if user exist in account
    if (!__isEmpty($accountUser)) {
        return false;
    }

    return true;
});

/**
 * Custom validation rules for domain
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('domain', function ($attribute, $domain, $parameters) {
    if (stripos($domain, 'http://') === 0) {
        $domain = substr($domain, 7);
    }

    if (stripos($domain, 'https://') === 0) {
        $domain = substr($domain, 8);
    }

    // Not even a single . this will eliminate things like abcd,
    // since http://abcd is reported valid
    if (!substr_count($domain, '.')) {
        return false;
    }

    if (stripos($domain, 'www.') === 0) {
        $domain = substr($domain, 4);
    }

    $again = 'http://' . $domain;
    return filter_var($again, FILTER_VALIDATE_URL);
});

/**
 * Custom validation rules for domains
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('domains', function ($attribute, $domains, $parameters) {
    $domains = explode(',', $domains);

    foreach ($domains as $domain) {

        if (stripos($domain, '*.') === 0) {
            $domain = substr($domain, 2);
        }

        if (stripos($domain, 'http://') === 0) {
            $domain = substr($domain, 7);
        }

        if (stripos($domain, 'https://') === 0) {
            $domain = substr($domain, 8);
        }

        // Not even a single . this will eliminate things like abcd,
        // since http://abcd is reported valid
        if (!substr_count($domain, '.')) {
            return false;
        }

        if (stripos($domain, 'www.') === 0) {
            $domain = substr($domain, 4);
        }

        $again = 'http://' . $domain;
        if (!filter_var($again, FILTER_VALIDATE_URL)) {
            return false;
        }
    }
    return true;
});

/**
 * Custom validation rules for check given subdomain is unique
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('unique_subdomain', function ($attribute, $value, $parameters) {
    if (in_array($value, configItem('reserved_subdomains'))) {
        return false;
    }
    return true;
});

/**
 * Custom validation rules for check amount length
 *
 * @return bool
 *---------------------------------------------------------------- */
Validator::extend('amount_validation', function ($attribute, $value, $parameters) {
    $condition = true;

    if (!__isEmpty($value)) {
        $afterDecimalAmount = null;

        $amount = number_format($value, 0, '.', '');

        $isDotContain = strpos($amount, '.');

        // Check if amount contains dot if yes then get before . amount value
        // and after dot amount.
        if ($isDotContain !== false) {
            $nonDecimalAmount = strchr($amount, '.', true);
        } else {
            $nonDecimalAmount = $amount;
        }

        // Get length of original amount
        $priceLength = strlen($nonDecimalAmount);

        // Check if amount length is greater than 10
        if ($priceLength > 9) {
            $condition = false;
        }

        if ($value > 999999999) {
            $condition = false;
        }
    }

    return $condition;
});

/**
 * Custom validation rules for check amount length
 *
 * @return bool
 *---------------------------------------------------------------- */
Validator::extend('decimal_validation', function ($attribute, $value, $parameters) {
    $condition = true;

    if (!__isEmpty($value)) {
        $afterDecimalAmount = null;

        $isDotContain = strpos($value, '.');

        // Check if amount contains dot if yes then get before . amount value
        // and after dot amount.
        if ($isDotContain !== false) {
            $afterDecimalAmount = strchr($value, '.');

            // Get length of decimal amount
            $afterDecimalAmountLength = strlen($afterDecimalAmount) - 1;

            $decimalValidation = $afterDecimalAmountLength;

            // Check if after decimal amount is greater than 4
            if ($afterDecimalAmountLength > 4) {

                // Get configuration setting decimal round number
                $configAfterDecimalAmountLength = 2; //getConfigurationSettings('currency_decimal_round');

                $decimalValidation = ($configAfterDecimalAmountLength > 4)
                    ? 4 : $configAfterDecimalAmountLength;
            }

            // Check if decimal amount length is greater than
            if ($afterDecimalAmountLength > $decimalValidation) {
                $condition = false;
            }
        }
    }

    return $condition;
});

/**
 * SSH public validation key
 *
 * @return bool
 *---------------------------------------------------------------- */
Validator::extend('ssh_public_key', function ($attribute, $value, $parameters, $validator) {
    return App\Yantrana\Support\Utils::validatePublicKey($value);
});

Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
    return Hash::check($value, current($parameters));
});

Validator::extend('verfy_authenticator_code', function ($attribute, $value, $parameters) {
    $email = \Request::input('email');

    $twoFactorAuthKey = App\Yantrana\Components\User\Models\UserModel::where('email', $email)->pluck('2fa_enabled')->toArray();

    if ($twoFactorAuthKey) {

        $google2fa = new PragmaRX\Google2FA\Google2FA();

        // Verify Code
        if (!$google2fa->verifyKey(array_first($twoFactorAuthKey), $value)) {

            return false;
        }
    }

    return true;
});

/**
 * Custom validation rules to check item in string
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('string_contains', function ($attribute, $value, $parameters) {
    return str_contains($value, array_get($parameters, '0'));
});

/**
 * Custom validation rules for checking age
 *
 * @return boolean
 *---------------------------------------------------------------- */

Validator::extend('validate_age', function ($attribute, $value, $parameters) {
    $diff = (Carbon::parse($value)->diffInDays(Carbon::now()) / 365);
    $ageRestriction = configItem('age_restriction');

    if (($diff > $ageRestriction['minimum']) and ($diff <= $ageRestriction['maximum'])) {
        return true;
    }

    return false;
});

/**
 * Custom validation rules for check unique title for pages
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('unique_page_title', function ($attribute, $value, $parameters) {
    $inputData = Request::all();
    $title = Str::slug($value, '-');
    $page = App\Yantrana\Components\Pages\Models\PageModel::where('title', $inputData['title'])->where('_uid', '!=', $inputData['pageUid'])->first();
    // check is empty
    if (!__isEmpty($page)) {
        return false;
    }
    return true;
});

/**
 * Custom validation rules for check unique title for kinks
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('unique_kink_title', function ($attribute, $value, $parameters) {
    $inputData = Request::all();
    $title = Str::slug($value, '-');
    $kink = App\Yantrana\Components\Kinks\Models\KinkModel::where('title', $inputData['title'])->where('_uid', '!=', $inputData['kinkUid'])->first();
    // check is empty
    if (!__isEmpty($kink)) {
        return false;
    }
    return true;
});

/**
 * Custom validation rules for check unique title for plans
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('unique_plan_title', function ($attribute, $value, $parameters) {
    $inputData = Request::all();
    $title = Str::slug($value, '-');
    $plan = App\Yantrana\Components\Plans\Models\PlanModel::where('title', $inputData['title'])->where('_uid', '!=', $inputData['planUid'])->first();
    // check is empty
    if (!__isEmpty($plan)) {
        return false;
    }
    return true;
});

/**
 * Custom validation rules for check unique title for forum categories
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('unique_forum_category_title', function ($attribute, $value, $parameters) {
    $inputData = Request::all();
    $title = Str::slug($value, '-');
    $forumCategory = App\Yantrana\Components\ForumCategories\Models\ForumCategoryModel::where('title', $inputData['title'])->where('_uid', '!=', $inputData['forumCategoryUid'])->first();
    // check is empty
    if (!__isEmpty($forumCategory)) {
        return false;
    }
    return true;
});

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}

/**
 * Custom validation rules for check unique title for forum categories
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('g_recaptcha_verify', function ($attribute, $value, $parameters) {

    $inputData = Request::all();
    $captcha = Str::slug($value, '-');
    $captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : null;

    $secretKey = env('RECAPTCHA_SECRET_KEY');
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
    //$response = file_get_contents($url);



    $response = curl_get_file_contents($url);
    $responseKeys = json_decode($response,true);

    if ( $responseKeys["success"] ) {
        return true;
    } else {
        return false;
    }
});


/**
 * Custom validation rules for check unique name for Block Country
 *
 * @return boolean
 *---------------------------------------------------------------- */
Validator::extend('unique_block_country_name', function ($attribute, $value, $parameters) {
    $inputData = Request::all();
    $title = Str::slug($value, '-');
    $kink = App\Yantrana\Components\BlockCountries\Models\BlockCountryModel::where('name', $inputData['name'])->where('_uid', '!=', $inputData['blockCountryUid'])->first();
    // check is empty
    if (!__isEmpty($kink)) {
        return false;
    }
    return true;
});