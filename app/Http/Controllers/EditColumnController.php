<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EditColumnController extends Controller
{
    public function update(Request $request) {
        app()->editColumn->update($request);
    }
}
