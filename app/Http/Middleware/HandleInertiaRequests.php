<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        if(Auth::check()) {
            $user = Auth::user();
            $userRoles = $user->userRoles;
            $roles = array();
        
            foreach($userRoles as $userRole) {
                array_push($roles, $userRole->role->slug);
            }
        }

        return array_merge(parent::share($request), [
            'auth' => Auth::check() ? [
                'user' => [
                    'name' => $user->name,
                    'roles' => $roles,
                ]
            ] : null,
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
        ]);
    }
}
