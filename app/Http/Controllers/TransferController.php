<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExternalTransferRequest;
use App\Http\Requests\StoreInternationalBankTransferConfirmRequest;
use App\Http\Requests\StoreInternationalBankTransferOTPConfirmRequest;
use App\Http\Requests\StoreInternationalTransferRequest;
use App\Http\Requests\StoreSameBankTransferConfirmRequest;
use App\Http\Requests\StoreSameBankTransferRequest;
use App\Models\TransferInitiation;
use App\Models\User;
use App\Models\UserPin;
use App\Notifications\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TransferController extends Controller
{

    // GENERAL FUNCTIONS

    public function getUserName($accountNum)
    {
        $userName = User::where('account_number', $accountNum)->first();
        if (!$userName) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json([
            'First name' => $userName->fname,
            'Last name' => $userName->lname
        ]);
    }
    public function sameBankTransferConfirm(StoreSameBankTransferConfirmRequest $request, TransferInitiation $tranId)
    {
        // dd($tranId);
        $user = Auth::user();
        $fromUser = User::where('id', $tranId->user_id)->first();
        $toUser = User::where('id', $tranId->user_id_to)->first();
        $pin = UserPin::where('user_id', $tranId->user_id)->first();
        if ($tranId->status) {
            return response()->json([
                'error' => 'Transaction has already been confirmed'
            ], 500);
        }
        if ($tranId->account_number === $user->account_number) {
            return response()->json([
                'error' => 'You cannot transfer to yourself'
            ], 500);
        }
        if ($pin && Hash::check($request->pin, $pin->main_pin)) {
            $tranId->status =  1;
            $tranId->save();
            $fromUser->update($request->safe()->merge([
                'balance' => $fromUser->balance - $tranId->amount
            ])->all());
            $toUser->update($request->safe()->merge([
                'balance' => $toUser->balance + $tranId->amount
            ])->all());

            return response()->json([
                'message' => 'Transfer successful'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Wrong pin'
            ], 500);
        }
    }

    // INTERNAL TRANSFERS

    public function store(StoreSameBankTransferRequest $request)
    {
        $user = Auth::user();
        $userTo = User::where('account_number', $request->account_number)->first();
        // dd($userTo->id);

        if ($user && $user->balance >= $request->amount) {
            TransferInitiation::create($request->safe()->merge([
                'user_id' => $user->id,
                'user_id_to' => $userTo->id,
                'type' => 'internal'
            ])->all());
            return response()->json([
                'message' => 'Transfer initiated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Insuffecient funds'
            ], 404);
        }
    }

    public function getTranDetails(TransferInitiation $tranId)
    {
        if (!$tranId) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        return response()->json([
            'First name' => $tranId->userTo->fname,
            'Middle name' => $tranId->userTo->other_name,
            'Last name' => $tranId->userTo->lname,
            'amount' => $tranId->amount,
            'To account' => $tranId->account_number,
            'From account' => $tranId->user->account_number,
            'description' => $tranId->description
        ]);
    }

    // EXTERNAL TRANSFERS

    public function storeExternalTransfer(StoreExternalTransferRequest $request)
    {
        $user = Auth::user();
        $userTo = User::where('account_number', $request->account_number)->first();
        if ($user && $user->balance >= $request->amount) {
            TransferInitiation::create($request->safe()->merge([
                'user_id' => $user->id,
                'user_id_to' => $userTo->id,
                'type' => 'external'
            ])->all());
            return response()->json([
                'message' => 'Transfer initiated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Insuffecient funds'
            ], 404);
        }
    }
    public function getExtTranDetails(TransferInitiation $tranId)
    {
        if (!$tranId) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        return response()->json([
            'First name' => $tranId->userTo->fname,
            'Middle name' => $tranId->userTo->other_name,
            'Last name' => $tranId->userTo->lname,
            'Bank name' => $tranId->bank_name,
            'amount' => $tranId->amount,
            'To account' => $tranId->account_number,
            'From account' => $tranId->user->account_number,
            'description' => $tranId->description
        ]);
    }

    // INTERNATIONAL TRANSFER FUNCTIONS 
    public function storeInternationalTransfer(StoreInternationalTransferRequest $request, NotificationService $notificationService)
    {
        // dd($request->all());
        $user = Auth::user();
        $userTo = User::where('account_number', $request->account_number)->first();
        if (!$userTo) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $otp = mt_rand(100000, 999999);
        if ($user && $user->balance >= $request->amount) {
            TransferInitiation::create($request->safe()->merge([
                'user_id' => $user->id,
                'user_id_to' => $userTo->id,
                'otp' => $otp,
                'type' => 'international'
            ])->all());
            $notificationService->subject('OTP NOTIFICATION')
                ->text('To authenticate your transaction, please use the following One Time Password (OTP):')
                ->text($otp)
                ->text("Don't share this OTP with anyone. Our customer service team will never ask you for your password, OTP, credit card, or banking info.")
                ->send($user, ['mail']);
            return response()->json([
                'message' => 'Transfer initiated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Insuffecient funds'
            ], 404);
        }
    }

    public function getInternationalTranDetails(TransferInitiation $tranId)
    {
        if (!$tranId) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        return response()->json([
            'Recepient Account Name' => $tranId->account_name,
            'Recepient Account Number' => $tranId->account_number,
            'Swift Code' => $tranId->swift_code,
            'Bank Branch' => $tranId->bank_name,
            'Bank Branch' => $tranId->bank_branch,
            'Country' => $tranId->country,
            'amount' => $tranId->amount,
            'email' => $tranId->email,
            'Recepient Phone Number' => $tranId->phone,
            'From Account Number' => $tranId->user->account_number,
            'From Account First Name' => $tranId->user->fname,
            'From Account Last Name' => $tranId->user->lname,
            'description' => $tranId->description
        ]);
    }
    public function internationalTransferConfirm(StoreInternationalBankTransferConfirmRequest $request, TransferInitiation $tranId)
    {
        // dd($tranId);
        $user = Auth::user();
        $pin = UserPin::where('user_id', $tranId->user_id)->first();
        if ($tranId->status) {
            return response()->json([
                'error' => 'Transaction has already been confirmed'
            ], 500);
        }
        if ($tranId->account_number === $user->account_number) {
            return response()->json([
                'error' => 'You cannot transfer to yourself'
            ], 500);
        }
        if ($pin && Hash::check($request->pin, $pin->main_pin)) {
            return response()->json([
                'message' => 'Pin correct'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Wrong pin'
            ], 500);
        }
    }
    public function internationalTransferOTPConfirm(StoreInternationalBankTransferOTPConfirmRequest $request, TransferInitiation $tranId)
    {
        // dd($tranId);
        $fromUser = User::where('id', $tranId->user_id)->first();
        $toUser = User::where('id', $tranId->user_id_to)->first();
        if ($tranId->status) {
            return response()->json([
                'error' => 'Transaction has already been confirmed'
            ], 500);
        }
        if ($request->otp === $tranId->otp) {
            $tranId->status =  1;
            $tranId->save();
            $fromUser->update($request->safe()->merge([
                'balance' => $fromUser->balance - $tranId->amount
            ])->all());
            $toUser->update($request->safe()->merge([
                'balance' => $toUser->balance + $tranId->amount
            ])->all());

            return response()->json([
                'message' => 'Transfer successful'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Wrong otp'
            ], 500);
        }
    }
}
