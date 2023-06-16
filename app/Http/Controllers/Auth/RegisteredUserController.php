<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRegistrationRequest;
use App\Models\User;
use App\Notifications\NotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRegistrationRequest $request, NotificationService $notificationService)
    {
        // dd($request->all());
        // $request->validate([

        // ]);
        $file = $request->hasFile('id_card') ? $request->file('id_card')->store('user_id_card', 'public') : '';
        $file2 = $request->hasFile('selfie') ? $request->file('selfie')->store('user_selfie', 'public') : '';
        $user = User::create($request->safe()->merge([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user',
            'id_card' => asset('storage/' . $file),
            'selfie' => asset('storage/' . $file2),
        ])->all());

        event(new Registered($user));

        Auth::login($user);
        $notificationService->subject('Welcome Notification')
            ->text('Welcome to ' . config('app.name'))
            ->send($user, ['mail']);

        return response()->json([
            'message' => 'User created successfully',
        ]);
    }
}
