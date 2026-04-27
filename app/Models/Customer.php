<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'document_id',
        'email',
        'phone',
        'address',
        'avatar',
        'user_id',
        'notes',
        'credit_limit',
        'points',
        'birthday',
        'tags',
    ];


    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addPoints($amount)
    {
        // 1 punto por cada 10 unidades de moneda gastadas
        $pointsToAdd = floor($amount / 10);
        $this->increment('points', $pointsToAdd);
    }
}
