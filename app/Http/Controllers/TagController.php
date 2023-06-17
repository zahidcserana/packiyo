<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TagController extends Controller
{
    public function filterInputTags(Request $request)
    {
        return app()->tag->filterInputTags($request);
    }
}
