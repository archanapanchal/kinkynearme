<?php

/**
 * Core Helper - 1.9.26 - 14 JUL 2021.
 *
 * Common helper functions for Laravel applications
 *
 *
 * Dependencies:
 *
 * Laravel     5.0 +     - http://laravel.com
 *-------------------------------------------------------- */

/**
 * State route for StateViaRoute function
 *
 *-------------------------------------------------------- */
Route::get('/state-via-route/{stateRouteInfo}', [
    'as'    => '__laraware.state_via_route',
    'uses'  => 'App\Yantrana\__Laraware\Support\CommonSupport@stateViaRoute'
]);

/**
 * State route for StateViaRoute function
 *
 *-------------------------------------------------------- */
Route::get('/redirect-via-post/{redirectPostData}', [
    'as'    => '__laraware.redirect_via_post',
    'uses'  => 'App\Yantrana\__Laraware\Support\CommonSupport@redirectViaPost'
]);

/**
 * Enabling Debug modes for the specific ips
 * @since 1.4.3 - 19 SEP 2018
 *-------------------------------------------------------- */
if (config('app.debug') == false) {
    if ($debugIps = env('APP_DEBUG_IPS', false)) {
        if ($debugIps) {
            $debugIps = array_map('trim', explode(',', $debugIps));
            if (in_array(request()->getClientIp(), $debugIps)) {
                config([
                    'app.debug' => true
                ]);
                unset($debugIps);
            }
        }
    }
}

if (!function_exists('redirectViaPost')) {
    /**
     * Redirect using post
     *
     * @param  string routeData url or route name
     * @param  array postData data to post

     *-------------------------------------------------------- */
    function redirectViaPost($routeData, $postData = [], $tempRedirectData = [])
    {
        if (is_string($routeData) === false) {
            throw new Exception('route id should be string');
        }

        if (is_array($postData) === false) {
            throw new Exception('post data should be array');
        }

        if (starts_with($routeData, ['http://', 'https://'])) {
            $redirectRoute = $routeData;
        } else {
            $redirectRoute = route($routeData);
        }

        $postFieldString = '';

        foreach ($postData as $key => $value) {
            if (is_numeric($value) or is_string($value)) {
                $postFieldString .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
            } else {
                throw new Exception('value should be numeric or string');
            }
        }

        $tempRedirectData = json_encode($tempRedirectData);

        return <<<EOL
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting ...</title>
</head>
    <body>
        Redirecting... please wait
        <form id="redirectViaPostFormElement" action="$redirectRoute" method="post">
        $postFieldString;
        </form>
        <script type="text/javascript">
            var tempRedirectData = `$tempRedirectData`;
            if(tempRedirectData) {
                window.localStorage.setItem('temp_redirect_data', tempRedirectData);
            }
            function redirectPostForm() {
                document.getElementById('redirectViaPostFormElement').submit();
            }
            window.onload = redirectPostForm;
        </script>
    </body>
</html>
EOL;
    }
}

if (!function_exists('stateViaRoute')) {
    /**
     * State via route function
     * Mostly get used for AngularJS state
     *
     * @param  routeData
     * @param  stateData

     *-------------------------------------------------------- */
    function stateViaRoute($routeData, $stateData)
    {
        $routeId = $routeData;
        $routeParams = [];

        if (is_array($routeData) and isset($routeData[0]) and is_string($routeData[0])) {
            $routeId = $routeData[0];

            if (isset($routeData[1])) {
                if (is_array($routeData[1])) {
                    $routeParams = $routeData[1];
                } else {
                    $routeParams[] = $routeData[1];
                }
            }
        }

        $stateId = $stateData;
        $stateParams = [];

        if (is_array($stateData) and isset($stateData[0]) and is_string($stateData[0])) {
            $stateId = $stateData[0];

            if (isset($stateData[1]) and is_array($stateData[1])) {
                $stateParams = array_only($stateData[1], array_filter(array_keys($stateData[1]), 'is_string'));
            }
        }

        if (is_string($routeId) === false) {
            throw new Exception('route id should be string');
        }

        if (is_string($stateId) === false) {
            throw new Exception('route id should be string');
        }

        $stateViaRouteInfo = [
            'routeId' => $routeId,
            'routeParams' => $routeParams,
            'stateName' => $stateId,
            'stateParams' => $stateParams
        ];

        return route('__laraware.state_via_route', base64_encode(json_encode($stateViaRouteInfo)));
    }
}

if (!function_exists('__dd')) {
    /**
     * Debugging function for debugging javascript side.
     * Alias of Laravel dd function but works well with  Ajax request by showing
     * it in browser console as well with file path and line no.
     *
     * @param  N numbers of params can be sent
     *-------------------------------------------------------- */
    function __dd()
    {
        if (config('app.debug', false) == false) {
            throw new Exception('Something went wrong!!');
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__dd() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();
        // Editors Supported: "phpstorm", "vscode", "vscode-insiders","sublime", "atom"
        // vscode as default editor if not set from env
        if (!env('IGNITION_EDITOR')) {
            config(['ignition.editor' => 'vscode']);
        }
        $editor = config('ignition.editor', 'vscode');

        if (isset($backtrace[0])) {
            // $args['debug_backtrace'] = str_replace(base_path(), '', $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
            // if the using Homestead
            $backtrace[0]['file'] =  $editor . '://file' . str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
            $args['debug_backtrace'] = $backtrace[0]['file'] . '#';
        }

        if (Request::ajax() === false) {
            echo '<a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="' . $backtrace[0]['file'] . '">Open in Editor (' . $editor . ')</a>';
            // call for dd
            call_user_func_array('dd', $args);
            exit();
        }

        exit(json_encode(array_merge(__response([], 23), [ // debug reaction
            '__dd' => '__dd',
            'data' => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args),
        ])));
    }
}

if (!function_exists('__pr')) {
    /**
     * Debugging function for debugging javascript as well as PHP side, work as likely print_r but accepts unlimited parameters
     * Works well with  Ajax request by showing
     * it in browser console as well with file path and line no.
     *
     * @param  N numbers of params can be sent
     * @return void
     *-------------------------------------------------------- */
    function __pr()
    {
        if (config('app.debug', false) == false) {
            return false;
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__pr() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        // vscode as default editor if not set from env
        if (!env('IGNITION_EDITOR')) {
            config(['ignition.editor' => 'vscode']);
        }
        $editor = config('ignition.editor', 'vscode');

        if (isset($backtrace[0])) {
            // $args['debug_backtrace'] = str_replace(base_path(), '', $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
            // if the using Homestead
            $backtrace[0]['file'] =  $editor . '://file' . str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
            $args['debug_backtrace'] = $backtrace[0]['file'] . '#';
        }

        if (Request::ajax() === false) {
            echo '<a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="' .  $backtrace[0]['file'] . '">Open in Editor (' . $editor . ')</a>';

            if (class_exists('\Illuminate\Support\Debug\Dumper')) {
                return array_map(function ($argument) {
                    (new \Illuminate\Support\Debug\Dumper())->dump($argument, false);
                }, $args);
            } elseif (function_exists('dump')) {
                return dump($args);
            } else {
                return array_map(function ($argument) {
                    print_r($argument, false);
                }, $args);
            }
        }

        return config([
            'app.__pr.' . count(config('app.__pr', [])) => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args)
        ]);
    }
}

if (!function_exists('__logDebug')) {
    /**
     * Log helper
     * Writes data in Laravel log file with File Path and Line Number
     *
     * @param  N numbers of params can be sent
     * @return void
     * @since - 1.5.3 - 20 SEP 2018
     *-------------------------------------------------------- */
    function __logDebug()
    {
        if (config('app.debug', false) == false) {
            return false;
        }
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__logDebug() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();
        if (isset($backtrace[0])) {
            $args['debug_backtrace'] = " logged @ file --------------->  " .  $backtrace[0]['file'] = str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
        }

        return array_map(function ($argument) {
            if (is_object($argument)) {
                Log::info('Following array is converted from Object for log.');
                Log::debug((array) $argument);
            } else {
                Log::debug($argument);
            }
        }, $args);

        return Log::debug($args);
    }
}

if (!function_exists('__clog')) {
    /**
     * Debugging function for debugging javascript
     * Prints data in browser console with file path and line number
     *
     * @param  N numbers of params can be sent
     * @return void
     *-------------------------------------------------------- */
    function __clog()
    {
        if (config('app.debug', false) == false) {
            return false;
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__clog() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        if (isset($backtrace[0])) {
            if (!env('IGNITION_EDITOR')) {
                config(['ignition.editor' => 'vscode']);
            }
            $editor = config('ignition.editor', 'vscode');
            // if the using Homestead
            $backtrace[0]['file'] = str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']);
            $args['debug_backtrace'] =  $editor . '://file' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '#';
        }

        return config([
            'app.__clog.' . count(config('app.__clog', [])) => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args)
        ]);
    }
}

if (!function_exists('__nestedKeyValues')) {
    /**
     * Utility function to create array of nested array items strings (concatenate parent key in to child key) & assign values to it.
     *
     * @param  $inputArray raw nested array
     * @param  $requestedJoiner joiner or word for string concat
     * @param  $prepend prepend string
     * @param  $allStages if you want to create an array item for every stage
     *
     * @return array
     *-------------------------------------------------------- */
    function __nestedKeyValues(array $inputArray, $requestedJoiner = '.', $prepend = null, $allStages = false)
    {
        $formattedArray = [];

        foreach ($inputArray as $key => $value) {
            $joiner = ($prepend == null) ? '' : $requestedJoiner;

            // if array run this again to grab the child items to process
            if (is_array($value)) {
                if ($allStages === true) {
                    array_push($formattedArray, $prepend);
                }

                $formattedArray = array_merge($formattedArray, __nestedKeyValues($value, $requestedJoiner, $prepend . $joiner . $key, $allStages));
            } else {
                // if key is not string push item in to array with required
                if (is_string($key) === false) {
                    if (is_string($value) === true) {
                        array_push($formattedArray, $prepend . $joiner . $value);
                    } else {
                        array_push($formattedArray, $value);
                    }
                } else {
                    // if want to have specific key
                    if (is_string($value) and substr($value, 0, 4) === 'key@') {
                        $formattedArray[substr($value, 4)] = $prepend . $joiner . $key;
                    } else {
                        $formattedArray[$prepend . $joiner . $key] = $value;
                    }
                }
            }
        }

        unset($prepend, $joiner, $requestedJoiner, $prepend, $allStages, $inputArray);

        return $formattedArray;
    }
}

if (!function_exists('__secureApiResponse')) {
    /**
     * Create JSON object for all HTTP request with Masked/Encrypted data
     *
     * @param array $data
     * @param integer|array $reactionCode
     * @param integer $httpCode http response code @since 1.8.25 - 07 MAY 2021
     *-------------------------------------------------------- */
    function __secureApiResponse($data, $reactionCode = 1, $httpCode = null)
    {
        $data['__secureOutput'] = true;
        return __apiResponse($data, $reactionCode, $httpCode);
    }
}
// non encrypted
if (!function_exists('__apiResponse')) {
    /**
     * Prepare JSON Response
     *
     * @param array $data
     * @param integer|array $reactionCode
     * @param integer $httpCode http response code @since 1.8.23 - 22 APR 2021
     * @return response
     */
    function __apiResponse($data, $reactionCode = 1, $httpCode = null)
    {
        if (($httpCode === null) and isset($reactionCode['http_code']) and $reactionCode['http_code']) {
            $httpCode = $reactionCode['http_code'];
        }

        if ($reactionCode === 21 and isset($data['redirect_to'])) {
            // if not ajax redirect from here
            if (!request()->ajax()) {
                if ($httpCode and is_integer($httpCode)) {
                    return redirect($data['redirect_to'], $httpCode);
                } else {
                    return redirect($data['redirect_to']);
                }
            }
        }

        if (
            isset($data['__useNativeJsonEncode'])
            and $data['__useNativeJsonEncode'] === true
        ) {
            if ($httpCode and is_integer($httpCode)) {
                http_response_code($httpCode);
            }
            return json_encode(__response($data, $reactionCode));
        }
        // ask to encrypt data to secure output
        if (
            isset($data['__secureOutput'])
            and $data['__secureOutput'] === true and !config('app.debug')
        ) {
            array_pull($data, '__secureOutput');

            $data = [
                '__maskedData' => YesSecurity::encryptLongRSA(
                    __response($data, $reactionCode)
                )
            ];

            unset($encryptedString, $reactionCode, $jsonStringsCollection);
        } else {
            $data = __response($data, $reactionCode);
        }

        if ($httpCode and is_integer($httpCode)) {
            return Response::json($data, $httpCode);
        }
        return Response::json($data);
    }
}

/**
 * Echo JSON API response.
 *
 * @param  array $data
 * @return JSON Object.
 *-------------------------------------------------------- */

if (!function_exists('__response')) {
    function __response($data, $reactionCode = 1)
    {
        // update client models as per response
        $updateClientModels = config('__update_client_models', []);
        $clientModels = [];
        if (!empty($updateClientModels)) {
            $clientModels = $updateClientModels;
            config([
                '__update_client_models' => []
            ]);
            unset($updateClientModels);
        }

        if (Session::has('additional')) {
            $data['additional'] = Session::get('additional');
        }

        if (config('app.additional')) {
            $data['additional'] = config('app.additional');
        }

        $responseData = [
            //  'data' => $data,
            'response_token' => (int) Request::get('fresh'),
            'reaction' => $reactionCode,
            'incident' => isset($data['incident']) ? $data['incident'] : null,
            // update client models accordingly
            'client_models' => $clientModels
        ];

        if (array_has($data, 'dataTableResponse')) {
            $responseData = array_merge($responseData, $data);
        } else {
            $responseData['data'] = $data;
        }

        if (Session::has('additional')) {
            $responseData['additional'] = Session::get('additional');
        }

        if (config('app.additional')) {
            $responseData['additional'] = config('app.additional');
            config(['app.additional' => null]);
        }

        // __pr() to print in console
        if (config('app.debug', false) == true) {
            $prSessItemName = '__pr';
            if (config('app.' . $prSessItemName)) {
                $responseData['__dd'] = true;
                // set for response
                $responseData[$prSessItemName] = config('app.' . $prSessItemName, []);
                config(['app.' . $prSessItemName => []]);
            }

            $clogSessItemName = '__clog';

            if (config('app.' . $clogSessItemName)) {
                $responseData['__dd'] = true;
                // set for response
                $responseData[$clogSessItemName] = config('app.' . $clogSessItemName, []);
                //reset the __clog items in config
                config(['app.' . $clogSessItemName => []]);
            }

            // email view debugging
            if (env('MAIL_VIEW_DEBUG', false) == true) {
                $testEmailViewSessName = '__emailDebugView';
                if (config('app.' . $testEmailViewSessName)) {
                    $responseData[$testEmailViewSessName] = config('app.' . $testEmailViewSessName, []);
                    //reset the testEmailViewSessName items in config
                    config(['app.' . $testEmailViewSessName => []]);
                }
            }
        }

        return $responseData;
    }
}

if (!function_exists('__') and !config('__tech.gettext_fallback')) {
    /**
     * Customized GetText string
     *
     * @param string $string
     * @param array $replaceValues
     *
     * @return string.
     *-------------------------------------------------------- */
    function __($string, $replaceValues = [])
    {
        if (function_exists('gettext') and getenv('LC_ALL') !== false) {
            $string = gettext($string);
        }

        // Check if replaceValues exist
        if (!empty($replaceValues) and is_array($replaceValues)) {
            $string = strtr($string, $replaceValues);
        }

        return $string;
    }
}

if (!function_exists('__yesset')) {
    /**
     * Generating public js/css links
     * @version 1.8.25 - 07 MAY 2021 - It also adds integrity attribute based on the file hash
     * @
     * @param string|array $file - file path from public path
     * @param bool $generateTag - if you want to generate script/link tags
     *
     * @return string
     *-------------------------------------------------------- */
    function __yesset($file, $generateTag = false, $options = [])
    {
        $options = array_merge([
            'random' => false
        ], $options);

        $filesString = '';
        $files = [];

        // if file is not array add it to array
        if (is_array($file) === false) {
            $files = [$file];
        } else {
            $files = $file;
        }


        foreach ($files as $keyFile) {
            $keyFile = strip_tags(trim($keyFile));

            // find actual files on the system based on the file path/name
            $globFiles = glob($keyFile);
            $fileHash = null;
            $fileExt = null;

            if (empty($globFiles)) {
                // if debug mode on throw an exception
                if (config('app.debug', false) === true) {
                    throw new Exception('Yesset file not found - ' . $keyFile . '
                        Check * in file name.');
                } else {
                    // if not just create file name;
                    $getFileName = $keyFile;

                    // generate url based on file name & path
                    $fileString = asset($getFileName);
                }
            } else {
                // we need to get first item out of it.
                $getFileName = $globFiles[0];
                // if randomly any one if required
                if ($options['random'] === true) {
                    $getFileName = $globFiles[rand(0, count($globFiles) - 1)];
                }

                $fileinfo = pathinfo($getFileName);
                $fileExt = $fileinfo['extension'];
                // generate url based on file name & path
                // also append file hash to know the file has been changed.
                $fileHash = base64_encode(hash_file('sha512', $getFileName, true));
                $fileString = asset($getFileName) . '?sign=' . hash('sha512', $fileHash);
            }

            // generate tags based on file extension
            // if file is array or generateTag is true
            if ((is_array($file) === true)
                or ($generateTag === true)
            ) {
                // get last 3 character from file name mostly file extension
                $jsItemTOMatch = 'js';
                if (!$fileExt) {
                    $fileExt = strtolower(substr($getFileName, -3));
                    $jsItemTOMatch = '.js';
                }

                switch ($fileExt) {
                        // script tag generation for JS file
                    case $jsItemTOMatch:
                        if ($fileHash) {
                            $filesString .= '<script type="text/javascript" integrity="sha512-' . $fileHash . '" src="' . $fileString . '"></script>' . PHP_EOL;
                        } else {
                            $filesString .= '<script type="text/javascript" src="' . $fileString . '"></script>' . PHP_EOL;
                        }
                        break;
                        // link tag generation for CSS file
                    case 'css':
                        if ($fileHash) {
                            $filesString .= '<link rel="stylesheet" type="text/css" integrity="sha512-' . $fileHash . '" href="' . $fileString . '"/>' . PHP_EOL;
                        } else {
                            $filesString .= '<link rel="stylesheet" type="text/css" href="' . $fileString . '"/>' . PHP_EOL;
                        }
                        break;

                    default:
                        $filesString .=  $fileString;
                }

                continue;
            }
            // if its string just return it.
            $filesString =  $fileString;
        }

        unset($files, $file, $generateTag);

        return $filesString;
    }
}

if (!function_exists('__secureProcessResponse')) {
    /**
     * Process response & send API response
     *
     * @param Integer  $engineReaction - Engine reaction
     * @param Array    $responses      - Response Messages as per reaction code
     * @param Array    $data           - Additional Data for success
     * @param int $httpCode - @since 1.8.23 - 22 APR 2021
     *
     * @return array
     *---------------------------------------------------------------- */
    function __secureProcessResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        $data['__secureOutput'] = true;

        return __processResponse(
            $engineReaction,
            $messageResponses,
            $data,
            $appendEngineData,
            $httpCode
        );
    }
}

if (!function_exists('__processResponse')) {
    /**
     * Process the response to send
     *
     * @param int|array $engineReaction
     * @param array $messageResponses
     * @param array $data
     * @param boolean $appendEngineData
     * @param int $httpCode - @since 1.8.23 - 22 APR 2021
     * @return response
     */
    function __processResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        if (__isValidReactionCode($engineReaction) === true) {
            return __apiResponse($data, $engineReaction, $httpCode);
        }

        if (($httpCode === null) and isset($engineReaction['http_code']) and $engineReaction['http_code']) {
            $httpCode = $engineReaction['http_code'];
        }

        if (is_array($engineReaction) === false or (array_key_exists(
            'reaction_code',
            $engineReaction
        ) === false
            and array_key_exists(
                'data',
                $engineReaction
            ) === false
            and array_key_exists(
                'message',
                $engineReaction
            ) === false)) {
            throw new Exception('__processResponse:: Invalid Engine Reaction');
        }

        $reactionCode = $engineReaction['reaction_code'];
        $reactionMessage = $engineReaction['message'];

        // Use message if sent from EngineReaction
        if (__isEmpty($reactionMessage) === false) {
            $data['message'] = $reactionMessage;
        // else use process response messages
        } elseif ($messageResponses and array_key_exists($reactionCode, $messageResponses)) {
            $data['message'] = $messageResponses[$reactionCode];
        }

        $dataFromReaction = isset($engineReaction['data']) ? $engineReaction['data'] : [];

        if ($data === true or $appendEngineData === true) {
            if (is_array($data) === false or empty($data) === true) {
                $data = [];
            }

            if (__isEmpty($dataFromReaction) === false) {
                if (
                    is_array($dataFromReaction)
                    or is_object($dataFromReaction)
                ) {
                    $data = array_merge($data, (array) $dataFromReaction);
                }
            }
        }

        $data['incident'] = isset($dataFromReaction['incident']) ? $dataFromReaction['incident'] : null;

        return __apiResponse($data, $reactionCode, $httpCode);
    }
}

if (!function_exists('__ifIsset')) {
    /**
     * Check isset & __isEmpty & return the result based on values sent
     *
     * @param Mixed  $data  - Mixed data - Note: Should no used direct function etc
     * @param Mixed  $ifSetValue  - Value if result is true
     * @param Mixed  $ifNotSetValue  - Value if result is false
     *
     * @return mixed
     *---------------------------------------------------------------- */
    function __ifIsset(&$data, $ifSetValue = '', $ifNotSetValue = '')
    {
        // check if value isset & not empty
        if ((isset($data) === true) and (__isEmpty($data) === false)) {
            if (!is_string($ifSetValue) and is_callable($ifSetValue) === true) {
                return call_user_func($ifSetValue, $data);
            } elseif ($ifSetValue === true) {
                return $data;
            } elseif ($ifSetValue !== '') {
                return $ifSetValue;
            }

            return true;
        } else {
            if (!is_string($ifNotSetValue) and is_callable($ifNotSetValue) === true) {
                return call_user_func($ifNotSetValue);
            } elseif ($ifNotSetValue !== '') {
                return $ifNotSetValue;
            }

            return false;
        }
    }
}

if (!function_exists('__isEmpty')) {
    /**
     * Customized isEmpty
     *
     * @param Mixed  $data  - Mixed data
     *
     * @return array
     *---------------------------------------------------------------- */
    function __isEmpty($data)
    {
        if (empty($data) === false) {
            if (($data instanceof Illuminate\Database\Eloquent\Collection
                    or $data instanceof Illuminate\Pagination\Paginator
                    or $data instanceof Illuminate\Pagination\LengthAwarePaginator
                    or $data instanceof Illuminate\Support\Collection)
                and ($data->count() <= 0)
            ) {
                return true;
            } elseif (is_object($data)) {
                $data = (array) $data;

                return empty($data);
            }

            return false;
        }

        return true;
    }
}

if (!function_exists('__isValidReactionCode')) {
    /**
     * Customized isEmpty
     *
     * @param Integer  $reactionCode  - Reaction Code
     *
     * @return bool
     *---------------------------------------------------------------- */
    function __isValidReactionCode($reactionCode)
    {
        if (
            is_integer($reactionCode) === true
            and array_key_exists(
                $reactionCode,
                config('__tech.reaction_codes')
            ) === true
        ) {
            return true;
        }

        return false;
    }
}

if (!function_exists('__reIndexArray')) {
    /**
     * Re Indexing using array value based on key
     *
     * @param array $array
     * @param string $valueKey
     * @param closure function $closure
     * @since - 29 JUN 2017
     * @example uses
            __reIndexArray([
                ['id' => '9e0fec39-dd53-4636-b628-f0123f05b318', name= 'xyz'],
                ['id' => '8e0fec39-ed53-5636-c628-f0123f05b618', name= 'abc']
            ], 'id', function($item, $valueKey) {
                $item['name'] =>  strtoupper($item['name']);
                return $item;
            });

            // Result
                [
                    '9e0fec39-dd53-4636-b628-f0123f05b318' => [
                        'id' => '9e0fec39-dd53-4636-b628-f0123f05b318',
                        'name' => 'Xyz'
                    ],
                    '8e0fec39-ed53-5636-c628-f0123f05b618' => [
                        'id' => '8e0fec39-ed53-5636-c628-f0123f05b618',
                        'name' => 'Abc'
                    ]
                ]

     * @return array
     *-------------------------------------------------------- */
    function __reIndexArray(array $array, $valueKey, $closure = null)
    {
        $newArray = [];
        if (!empty($array)) {
            foreach ($array as $item) {
                if (is_array($item)) {
                    $itemForKey = array_get($item, $valueKey);
                    if ($itemForKey and (is_string($itemForKey)
                        or is_numeric($itemForKey))) {
                        if ($closure and is_callable($closure)) {
                            $newArray[$itemForKey] = call_user_func($closure, $item, $valueKey);
                        } else {
                            $newArray[$itemForKey] = $item;
                        }
                    }
                }
            }
        }
        unset($array, $valueKey, $closure);
        return $newArray;
    }
}

if (!function_exists('__canAccess')) {
    /**
     * Check if access available
     *
     * @param string $accessId
     *
     * @return bool.
     *-------------------------------------------------------- */
    function __canAccess($accessId = null)
    {
        if (
            YesAuthority::check($accessId) === true
            or YesAuthority::isPublicAccess($accessId)
        ) {
            return true;
        }

        return false;
    }
}

if (!function_exists('__canPublicAccess')) {
    /**
     * Check if access available
     *
     * @param string $accessId
     *
     * @return bool.
     *-------------------------------------------------------- */
    function __canPublicAccess($accessId = null)
    {
        return YesAuthority::isPublicAccess($accessId);
    }
}

/**
 * listen Query events
 *---------------------------------------------------------------- */
if ((config('app.debug', false) == true)
    and env('APP_DB_LOG', false) == true
) {
    Event::listen('Illuminate\Database\Events\QueryExecuted', function ($event) {
        $bindings = $event->bindings;

        if (count($bindings) > 0) {
            // Format binding data for sql insertion
            foreach ($bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } elseif (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }

            $clogItems['SQL__Bindings'] = implode(', ', $bindings);
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $event->sql);
        $query = vsprintf($query, $bindings);

        $clogItems = ['SQL__Query' => $query];

        $clogItems['SQL__TimeTaken'] = $event->time;

        __clog($clogItems);
    });
}

if (!function_exists('updateCreateArrayFileItem')) {
    /**
     * Update config file
     *
     * @param string $configFile - without .php
     * @param mixed $itemName
     * @param mixed $itemValue
     *
     * @return mixed.
     *-------------------------------------------------------- */
    function updateCreateArrayFileItem($configFile, $itemName, $itemValue, $options = [])
    {
        $actualFileName = $configFile . '.php';
        if (!file_exists($actualFileName)) {
            $fh = fopen($actualFileName, 'a');
            fwrite($fh, "<?php
    return [];");
            fclose($fh);
        }

        $options = array_merge([
            'prepend_comment' => ''
        ], $options);

        // $configFile = str_replace('.php', '', $configFile);
        $configFileArray = require $actualFileName;
        $updatedArray = array_set($configFileArray, $itemName, $itemValue);
        $arrayString = '<?php
            ' . $options['prepend_comment'] . '
    return ';
        $arrayString .= var_export($configFileArray, true) . ";";

        /*  config([
                $configFile => $configFileArray
            ]); */

        file_put_contents($actualFileName, $arrayString);

        return $updatedArray;
    }
}

if (!function_exists('arraySetAndGet')) {
    /**
     * Set the array item using get with replaced values
     * default is set array value
     *
     * @param array $setArray
     * @param array $getArray
     * @param array $replaceValues
     * @return array
     */
    function arraySetAndGet(&$setArray = [], &$getArray = [], $replaceValues = [])
    {
        $replaceValues = __nestedKeyValues($replaceValues);
        foreach ($replaceValues as $key => $value) {
            array_set($setArray, $key, array_get($getArray, $value, array_get($setArray, $key)));
        }
        unset($replaceValues);
        return $setArray;
    }
}

if (!function_exists('arrayFilterRecursive')) {
    /**
     * Remove the blank & null value elements from array recursively
     * Note: Resulting array items won't be replaced by blank or null items
     *
     * @uses ['__key__' => 2]
     * @param array $array
     * @return array
     */
    function arrayFilterRecursive(array $array)
    {
        $newArray = [];
        foreach ($array as $arrayKey => $arrayValue) {
            // if it is array call again
            if (is_array($arrayValue)) {
                $getInternals = arrayFilterRecursive($arrayValue);
                // store if its not empty
                if (!empty($getInternals)) {
                    $newArray[$arrayKey] = $getInternals;
                }
                // check iff the item is not blank or null
            } elseif (($arrayValue !== null) and (trim($arrayValue) !== '')) {
                $newArray[$arrayKey] = $arrayValue;
            }
        }
        // unset non-required items
        unset($array);
        return $newArray;
    }
}
if (!function_exists('arrayExtend')) {
    /**
     * Extended array by other like jquery extends
     *
     * @param array $array
     * @param array $otherArray
     * @return array
     */
    function arrayExtend(array $array, array $otherArray)
    {
        return array_replace_recursive(
            $array,
            // remove blank value items
            arrayFilterRecursive($otherArray)
        );
    }
}

if (!function_exists('arrayStringReplace')) {
    /**
     * Replace te string based keys within the array eg. __xyz__ change abc
     *
     * @param array $array
     * @param array $otherArray
     * @return array
     */
    function arrayStringReplace(array $array, array $updates)
    {
        return json_decode(
            // replace the items in array
            strtr(
                // convert to string
                json_encode($array),
                // changes array
                $updates
            ),
            true
        );
    }
}

if (!function_exists('updateClientModels')) {
    /**
     * Add items to update client models to response
     * it add client_models array to response on which
     * client update their models works fine with alpineJS models
     *
     * @param array $items
     * @return void
     * @since 1.8.20 - 19 APR 2021
     */
    function updateClientModels(array $items)
    {
        config([
            '__update_client_models' => array_merge(config('__update_client_models', []), $items)
        ]);
        return true;
    }
}

if (!function_exists('abortIf')) {
    /**
     * Implemented to handle ajax request
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @since 1.9.25 - 04 JUN 2021.
     */
    function abortIf($boolean, $code = 404, $message = '', array $headers = [])
    {
        if ($boolean) {
            if (Request::ajax() === false) {
                abort($code, $message, $headers);
            }
            http_response_code($code);
            exit(json_encode(array_merge(__response([], 2), [ // debug reaction
                'message' => $message ? $message : (function_exists('__tr') ? __tr('Operation aborted, may invalid request') : __('Operation aborted, may invalid request')),
                'data' => [],
            ])));
        }
    }
}
