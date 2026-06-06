<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionWebController extends Controller
{
   public function index(Request $request)
    {
        $telegramId = session('telegram_id');
    
        $query = Transaction::where(
            'telegram_id',
            $telegramId
        );
    
        if ($request->filled('month')) {
            $query->whereMonth(
                'transaction_date',
                $request->month
            );
        }
    
        if ($request->filled('type')) {
            $query->where(
                'type',
                $request->type
            );
        }
    
        if ($request->filled('category')) {
            $query->where(
                'category',
                $request->category
            );
        }
    
        $perPage = $request->per_page ?? 10;
    
        $transactions = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    
        $totalNominal = $query->sum('amount');
    
        return view(
            'transactions.index',
            compact(
                'transactions',
                'totalNominal'
            )
        );
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('transactions.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        return redirect('/transactions');
    }

    public function destroy($id)
    {
        Transaction::destroy($id);

        return redirect('/transactions');
    }


}