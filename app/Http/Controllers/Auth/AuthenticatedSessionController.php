<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticatedSessionController extends Controller
{

    public function username()
    {
        return 'username';
    }
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
       
        $input = $request->all();
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password']))) {
            $token = $request->user()->createToken('user_token')->plainTextToken;
            $personalAccessToken = PersonalAccessToken::findToken($token);
            $type = $personalAccessToken->name;
            return  response()->json([
                'message' => 'You are now logged in',
                'token' => $token,
                'type' => $type
            ]);
        } else {
            return  response()->json([
                'message' => 'Invalid credentials',
            ]);
        }
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();
        return  response()->json([
            'message' => 'You are now logged out',
        ]);

       
    }
}
