<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPinRequest;
use App\Http\Requests\StoreUserPinUpdateRequest;
use App\Models\UserPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function storePin(StoreUserPinRequest $request)
    {
        $user = Auth::user();
        // dd($user);
        $userPin = UserPin::where('user_id', $user->id)->first();
        if ($userPin) {
            return response()->json([
                'message' => 'You have a pin already, please go and update'
            ]);
            if ($request->main_pin !== $request->main_pin_confirmation) {
                return response()->json([
                    'message' => 'Pin confirmation does not match'
                ]);
                if ($userPin && Hash::check($request->main_pin, $userPin->main_pin)) {
                    return response()->json([
                        'message' => 'You cannot use your old pin as new pin'
                    ]);
                }
            }
        }
        UserPin::create($request->safe()->merge([
            'user_id' => $user->id,
            'main_pin' => Hash::make($request->main_pin),
        ])->all());
        return response()->json([
            'message' => 'Transaction pin created successfully'
        ]);
    }

    public function updatePin(StoreUserPinUpdateRequest $request, UserPin $pinId)
    {

        if ($pinId && Hash::check($request->old_pin, $pinId->main_pin)) {
            if ($request->main_pin !== $request->main_pin_confirmation) {
                return response()->json([
                    'message' => 'Pin confirmation does not match'
                ]);
            }
            $pinId->update($request->safe()->merge([
                'main_pin' => Hash::make($request->main_pin),
            ])->all());
            return response()->json([
                'message' => 'Pin updated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'You have entered an incorrect old pin'
            ]);
        }
    }
}
