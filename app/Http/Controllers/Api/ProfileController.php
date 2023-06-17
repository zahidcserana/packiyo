<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function edit(Request $request)
    {
        return response()->json((app()->profile->edit($request)));
    }

    public function password(Request $request)
    {
        return response()->json((app()->profile->password($request)));
    }

    public function update(Request $request)
    {
        return response()->json((app()->profile->update($request)));
    }

    public function delete(Request $request)
    {
        return response()->json((app()->profile->delete($request)));
    }

    public function upload(Request $request)
    {
        return response()->json((app()->profile->upload($request)));
    }

    public function logout(Request $request)
    {
        return response()->json((app()->profile->logout($request)));
    }
}
