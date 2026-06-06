<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;


class MonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = Transaction::select(
            'telegram_id'
        )
        ->distinct()
        ->get();

        foreach ($users as $user) {

            $chatId = $user->telegram_id;

            $income = Transaction::where(
                'telegram_id',
                $chatId
            )
            ->where('type','income')
            ->whereMonth(
                'transaction_date',
                now()->subMonth()->month
            )
            ->sum('amount');

            $expense = Transaction::where(
                'telegram_id',
                $chatId
            )
            ->where('type','expense')
            ->whereMonth(
                'transaction_date',
                now()->subMonth()->month
            )
            ->sum('amount');

            $saving = Transaction::where(
                'telegram_id',
                $chatId
            )
            ->where('type','saving')
            ->whereMonth(
                'transaction_date',
                now()->subMonth()->month
            )
            ->sum('amount');

            $saldo =
                $income -
                $expense -
                $saving;

            $message =
                "📊 LAPORAN BULANAN\n\n".
                "💵 Pemasukan : Rp".number_format($income,0,',','.')."\n".
                "💸 Pengeluaran : Rp".number_format($expense,0,',','.')."\n".
                "🏦 Tabungan : Rp".number_format($saving,0,',','.')."\n".
                "💰 Saldo : Rp".number_format($saldo,0,',','.');

            Http::post(
                "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            );
        }
    }
}
