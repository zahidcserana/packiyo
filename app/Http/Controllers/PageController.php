<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Display all the static pages when authenticated
     *
     * @param string $page
     * @return Application|Factory|View
     */
    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }

        return abort(404);
    }

    /**
     * Display the pricing page
     *
     * @return Application|Factory|View
     */
    public function pricing()
    {
        return view('pages.pricing');
    }

    /**
     * Display the lock page
     *
     * @return Application|Factory|View
     */
    public function lock()
    {
        return view('pages.lock');
    }
}
