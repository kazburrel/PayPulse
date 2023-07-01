<?php

namespace App\Http\Controllers;

use App\Models\TransferInitiation;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function getTransactionHistory($userId)
    {
        $history =  TransferInitiation::where('user_id', $userId)->get();
        if (count($history) > 0) {
            return $history;
        } else {
            return response()->json([
                'error' => 'No records found'
            ], 404);
        }
        
    }
}
