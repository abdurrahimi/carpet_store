<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Menu;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'menu' => $menus,
            ],
            'flash' => [
                'success'=> session('success'),
                'error'=> session('error')
            ]
        ];
    }
}
