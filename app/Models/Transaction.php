<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SavingGoal;

class Transaction extends Model
{
    protected $fillable = [
        'telegram_id',
        'type',
        'category',
        'amount',
        'description',
        'transaction_date',
        'goal_id',
    ];

}


