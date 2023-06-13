<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function store(Request $request, NotificationService $notificationService): response
    {
        // dd($request->all());
        $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'id_card' => ['required'],
            'selfie' => ['required'],
            'account_type' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:255'],
            't_c' => ['accepted'],
            'security_question' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required'],
        ]);
        $file = $request->hasFile('id_card') ? $request->file('id_card')->store('user_id_card', 'public') : '';
        $file2 = $request->hasFile('selfie') ? $request->file('selfie')->store('user_selfie', 'public') : '';
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user',
            'id_card' => asset('storage/' . $file),
            'selfie' => asset('storage/' . $file2),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $notificationService->subject('Welcome Notification')
            ->text('Welcome to ' . config('app.name'))
            ->send($user, ['mail']);

        return response()->noContent()->json([
            'message' => 'Vendor created successfully',
        ]);
    }
}
