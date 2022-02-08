<?php

namespace App\Yantrana\Base;

use Illuminate\Support\Facades\Route;
use YesSecurity;
use JavaScript;
use YesAuthority;
use Auth;
use Request;
use View;
use Illuminate\Support\Str;

use App\Yantrana\__Laraware\Core\CoreController;

abstract class BaseController extends CoreController
{
    /**
     * Load Public View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadManageView($viewName, $data = [])
    {
        return $this->renderView('manage-master', $viewName, $data);
    }

    /**
     * Load Public View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadPublicView($viewName, $data = [])
    {
        return $this->renderView('public-master', $viewName, $data);
    }

    /**
     * Load Profile View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadProfileView($viewName, $data = [])
    {
        return $this->renderView('profile-master', $viewName, $data);
    }

    /**
     * Load Page View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadPageView($viewName, $data = [])
    {
        return $this->renderView('page-master', $viewName, $data);
    }

    /**
     * Load Search View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadSearchView($viewName, $data = [])
    {
        return $this->renderView('search-master', $viewName, $data);
    }


    /**
     * Load Public View
     *
     * @param string $viewName - View Name
     * @param array  $data     - Array of data if needed
     *
     * @return array
     *-------------------------------------------------------------------------- */
    protected function renderView($mainView, $viewName, $data = [])
    {
        $output = view($mainView, $data)->nest('pageRequested', $viewName, $data)->render();

        if (!env('APP_DEBUG', false)) {
            $filters = array(
                '/<!--([^\[|(<!)].*)/' => '',  // Remove HTML Comments (breaks with HTML5 Boilerplate)
                '/(?<!\S)\/\/\s*[^\r\n]*/' => '',  // Remove comments in the form /* */
                '/\s{2,}/' => ' ', // Shorten multiple white spaces
                '/(\r?\n)/' => '',  // Collapse new lines
            );

            return preg_replace(
                array_keys($filters),
                array_values($filters),
                $output
            );
        } else { // for clog

            $clogSessItemName = '__clog';
            if (!empty(config('app.' . $clogSessItemName, []))) {
                $responseData = [
                    '__dd'              => true,
                    '__clogType'        => 'NonAjax',
                    $clogSessItemName   => config('app.' . $clogSessItemName),
                ];

                //reset the __clog items in session
                config(['app.' . $clogSessItemName => []]);
                $output = $output . '<script type="text/javascript">__globals.clog(' . json_encode($responseData) . ');</script>';
            }
        }

        return $output;
    }

    /**
     * Send response to client
     *
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function responseAction($processResponse, $typeResponse = [])
    {
        $originalData = $processResponse->getData();
        $originalData->response_action = array_merge([
            'type' => null, // redirect, replace, append, prepend
            'target' => null, // replacement element identifier or redirect url
            'content' => null,
            'url' => null
        ], $typeResponse);

        $processResponse->setData($originalData);

        return $processResponse;
    }

    /**
     * Replace view preparation
     *
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function replaceView($viewName, $data = [], $targetElement = '#pageContent')
    {
        return [
            'type' => 'replace', // redirect, replace, append, prepend
            'target' => $targetElement, // replacement element identifier or redirect url
            'content' => view($viewName, $data)->render()
        ];
    }

    /**
     * Replace view preparation
     *
     *
     * @return array
     *-------------------------------------------------------------------------- */
    public function redirectTo($routeOrUrl, $parameters = [])
    {
        return [
            'type' => 'redirect', // redirect, replace, append, prepend
            'url' => Str::startsWith($routeOrUrl, 'http') ? $routeOrUrl : route($routeOrUrl, $parameters)
        ];
    }

    /**
     * Prepare data for clideside.
     *
     * @return array
     */
    protected function prepareForBrowser()
    {
        // get all application routes.
        $routeCollection = Route::getRoutes();
        $routes = [];
        $availableRoutes = [];
        $index = 1;

        // if routes in application
        if (!empty($routeCollection)) {
            foreach ($routeCollection as $route) {
                if ($route->getName()) {
                    $routes[$route->getName()] = $route->uri();
                    $availableRoutes[] = $route->getName();
                } else {
                    $routes['unnamed_' . $index] = $route->uri();
                }

                ++$index;
            }
        }


        return [
            '__appImmutables' => [
                'auth_info'         => getUserAuthInfo(),
                'availableRoutes'   => $availableRoutes,
                'routes'            => $routes,
                'agora_app_id'      => getStoreSettings('agora_app_id'),
                'pusher_key'        => getStoreSettings('pusher_app_key'),
                'pusher_cluster_key' => getStoreSettings('pusher_app_cluster_key'),
                'messages' => [
                    'validation' => trans('validation'),
                    'js_string' => trans('js-string'),
                ],
            ],
            'appConfig' => [
                'debug'         => env('APP_DEBUG', false),
                'appBaseURL'    => asset(''),
            ]
        ];
    }
}
