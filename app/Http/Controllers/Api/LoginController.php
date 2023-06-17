<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\NewAccessToken;

/**
 * Class LoginController
 * @package App\Http\Controllers\Api
 * @group Users
 */
class LoginController extends ApiController
{
    public function __construct()
    {
//        $this->middleware('auth:api')->except('authenticate');
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'source' => 'required|string',
            'exclusive' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $credentials = $request->only('email', 'password');

        if(Auth::guard('web')->attempt($credentials)){
            $user = auth()->user();
            if ($request->exclusive) {
                $tokens = $user->tokens()->where('source', $request->source);
                if ($tokens->count()) {
                    if ($request['continue']) {
                        $tokens->delete();
                    } else {
                        return response()->json(['message' => 'Someone else is already logged in.', 'logged_in' => true ], 401);
                    }
                }
            }

            $newToken = auth()->user()->createToken(rand());

            $tokenArr = explode('|', $newToken->plainTextToken);
            $tokenId = $tokenArr[0];
            $token = $user->tokens()->where('id', $tokenId)->first();
            $token->source = $request->source;
            $token->save();

            if ($newToken instanceof NewAccessToken) {
                $user->access_token = $newToken->plainTextToken;
                $user->image = $user->picture ? url(Storage::url($user->picture)) : '';
                return response()->json(['message' => 'Logged in.', 'user' => $user]);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        }
        else{
            return response()->json(['message' => 'Please check if tenant and credentials are entered correctly.'], 401);
        }
    }
}
