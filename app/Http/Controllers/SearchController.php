<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {

    }

    public function index($keyword)
    {
        return view('search.index', ['keyword'=>$keyword, 'page' => 'search']);
    }

    public function getSearch(Request $request)
    {
        return redirect()->route('search', ['keyword'=>$request->keyword]);
    }
}
