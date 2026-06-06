<?php

namespace App\Http\Controllers;

use App\Models\SavingGoal;
use App\Models\Transaction;
use Illuminate\Http\Request;


class SavingGoalController extends Controller
{
    public function index()
    {
        $telegramId = session('telegram_id');

        $goals = SavingGoal::where(
            'telegram_id',
            $telegramId
        )->get();

        foreach($goals as $goal){

            $saved = Transaction::where(
                'goal_id',
                $goal->id
            )->sum('amount');

            $goal->saved = $saved;

            $goal->progress =
                $goal->target_amount > 0
                ? ($saved / $goal->target_amount) * 100
                : 0;
        }

        return view(
            'saving-goals.index',
            compact('goals')
        );
    }

    public function edit($id)
    {
        $goal = SavingGoal::findOrFail($id);

        return view(
            'saving-goals.edit',
            compact('goal')
        );
    }

    public function update(Request $request,$id)
    {
        $goal = SavingGoal::findOrFail($id);

        $goal->update([
            'goal_name'=>$request->goal_name,
            'target_amount'=>$request->target_amount
        ]);

        return redirect('/saving-goals');
    }

    public function destroy($id)
    {
        Transaction::where(
            'goal_id',
            $id
        )->delete();
    
        SavingGoal::destroy($id);
    
        return redirect('/dashboard')
            ->with(
                'success',
                'Target dan seluruh riwayat tabungan berhasil dihapus'
            );
    }
}