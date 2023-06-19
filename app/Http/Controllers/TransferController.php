<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExternalTransferRequest;
use App\Http\Requests\StoreSameBankTransferConfirmRequest;
use App\Http\Requests\StoreSameBankTransferRequest;
use App\Models\TransferInitiation;
use App\Models\User;
use App\Models\UserPin;
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
        $fromUser = User::where('id', $tranId->user_id)->first();
        $toUser = User::where('id', $tranId->user_id_to)->first();
        $pin = UserPin::where('user_id', $tranId->user_id)->first();
        if ($tranId) {
            return response()->json([
                'error' => 'Transaction has already been confirmed'
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
                'user_id_to' => $userTo->id
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
                'user_id_to' => $userTo->id
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
}
