<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserPin extends Model
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'main_pin',
        // 'old_pin',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
