<?php

/* 
 * ## CUSTOM-CHANGE ##
 * Temporary disable front-end access
 */

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;

class AdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        //return $next($request);

        /*if (url('/') == 'http://knm.local') {
            return $next($request);
        }*/

        if (!Auth::guest()) {
            // check if normal user try to access admin section
            if (isAdmin()) {
                $allow = [
                            'change-theme/dark',
                            'change-theme/light',
                            //'user/change-password',
                            //'user/change-email',
                            'user/settings/process-profile-setting',
                            'user/settings/search-static-cities',
                            'user/logout',
                            ];

                if (!in_array($request->path(), $allow)) {
                    return redirect()->route('manage.dashboard');
                }            
            } else {
                //Auth::logout();
                //return redirect('user/login');
            }
        } else {
            return redirect()->route('admin.user.login');
        }

        return $next($request);
    }
}
