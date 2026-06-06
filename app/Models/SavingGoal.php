<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class SavingGoal extends Model
{
    protected $fillable = [
    'telegram_id',
    'goal_name',
    'target_amount'
    ];

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class,
            'goal_id'
        );
    }
}
