<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class TransferInitiation extends Model
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_id_to',
        'account_name',
        'account_number',
        'swift_code',
        'bank_name',
        'bank_branch',
        'country',
        'amount',
        'beneficial_phone',
        'email',
        'otp',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to', 'id');
    }
}
