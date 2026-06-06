<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\SavingGoal;

class DashboardController extends Controller
{
    public function index()
{
    $telegramId = session('telegram_id');

    $income = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'income')
        ->sum('amount');

    $expense = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'expense')
        ->sum('amount');

    $saving = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'saving')
        ->sum('amount');

    $saldo = $income - $expense - $saving;

    $monthlyIncome = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'income')
        ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $monthlyExpense = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'expense')
        ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $monthlySaving = Transaction::where('telegram_id', $telegramId)
        ->where('type', 'saving')
        ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $incomeChart = [];
    $expenseChart = [];
    $savingChart = [];
    $saldoChart = [];

    for ($i = 1; $i <= 12; $i++) {

        $incomeValue = $monthlyIncome[$i] ?? 0;
        $expenseValue = $monthlyExpense[$i] ?? 0;
        $savingValue = $monthlySaving[$i] ?? 0;

        $incomeChart[] = $incomeValue;
        $expenseChart[] = $expenseValue;
        $savingChart[] = $savingValue;

        $saldoChart[] =
            $incomeValue -
            $expenseValue -
            $savingValue;
    }

    $expenseCategories = Transaction::where(
            'telegram_id',
            $telegramId
        )
        ->where('type', 'expense')
        ->selectRaw('category, SUM(amount) as total')
        ->groupBy('category')
        ->get();

    $topExpenses = Transaction::where(
            'telegram_id',
            $telegramId
        )
        ->where('type', 'expense')
        ->whereMonth(
            'transaction_date',
            now()->month
        )
        ->orderByDesc('amount')
        ->limit(5)
        ->get();

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

    return view('dashboard', compact(
        'income',
        'expense',
        'saving',
        'saldo',
        'incomeChart',
        'expenseChart',
        'savingChart',
        'saldoChart',
        'expenseCategories',
        'topExpenses',
        'goals'
    ));
}
}