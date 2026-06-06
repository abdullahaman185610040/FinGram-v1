<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\Http;
use App\Models\LoginToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $update = $request->all();

        Log::info($update);

        if (!isset($update['message'])) {
            return response()->json([
                'status' => 'ignored'
            ]);
        }

        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'] ?? '';

       if (str_starts_with($text, '/pemasukan')) {
            $this->saveTransaction($chatId, $text, 'income');
        }

        if (str_starts_with($text, '/pengeluaran')) {
            $this->saveTransaction($chatId, $text, 'expense');
        }

        if ($text == '/saldo') {
            $this->showSaldo($chatId);
        }

        if ($text == '/start' || $text == '/help') {
            $this->showHelp($chatId);
            $this->sendMainMenu($chatId);
        }

        if ($text == '/laporan') {
            $this->showReport($chatId);
        }

        if ($text == '/mutasi') {
            $this->showMutation($chatId);
        }

        if(str_starts_with($text,'/target')){
            $this->saveTarget($chatId,$text);
        }

        if($text == '/targetku'){
            $this->showTarget($chatId);
        }

        if(str_starts_with($text,'/nabung')){
            $this->saveSavingDeposit(
                $chatId,
                $text
            );
        }

        if ($text == '/web') {

    try {

        Log::info('STEP 1');

        $token = \Illuminate\Support\Str::random(40);

        Log::info('STEP 2');

        \App\Models\LoginToken::create([
            'telegram_id' => $chatId,
            'token' => $token,
            'expires_at' => now()->addMinutes(10)
        ]);

        Log::info('STEP 3');

        $link = url("/login/telegram/$token");

        Log::info('STEP 4');

        $this->sendTelegramMessage(
            $chatId,
            "🔐 Login Web\n\n$link"
        );

        Log::info('STEP 5');

    } catch (\Exception $e) {

        Log::error($e->getMessage());

        $this->sendTelegramMessage(
            $chatId,
            "ERROR:\n".$e->getMessage()
        );
    }
}


        return response()->json([
            'status' => 'ok'
        ]);
    }

    private function saveTransaction($chatId, $text, $type)
    {
        $parts = explode(' ', $text, 3);

        if(count($parts) < 3){
            return false;
        }

        if (!is_numeric($parts[1])) {
            $this->sendTelegramMessage(
                $chatId,
                "❌ Nominal harus berupa angka.\n\nContoh:\n/pemasukan 500000 Gaji"
            );

            return false;
        }

        $amount = (float)$parts[1];
        $description = $parts[2];

        $category = $this->detectCategory(
            $description,
            $type
        );

        Transaction::create([
            'telegram_id' => $chatId,
            'type' => $type,
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now()->toDateString()
        ]);

        if ($type == 'expense') {
            $this->checkDailyExpense($chatId);
            $this->checkFinancialHealth($chatId);
        }

        $jenis = match($type){
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            'saving' => 'Tabungan'
        };

        $pesan =
            "✅ {$jenis} berhasil dicatat\n\n".
            "💰 Nominal : Rp".number_format($amount,0,',','.')."\n".
            "📝 Keterangan : {$description}";

        $this->sendTelegramMessage(
            $chatId,
            $pesan
        );

        return true;
    }

    private function sendTelegramMessage($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        Http::post(
            "https://api.telegram.org/bot{$token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]
        );
    }

    private function showSaldo($chatId)
    {
        $income = Transaction::where(
            'telegram_id',
            $chatId
        )->where(
            'type',
            'income'
        )->sum('amount');

        $expense = Transaction::where(
            'telegram_id',
            $chatId
        )->where(
            'type',
            'expense'
        )->sum('amount');

        $saving = Transaction::where(
            'telegram_id',
            $chatId
        )->where(
            'type',
            'saving'
        )->sum('amount');

        $saldo =
            $income -
            $expense -
            $saving;

        $message =
            "📊 RINGKASAN KEUANGAN\n\n".
            "💵 Pemasukan : Rp".number_format($income,0,',','.')."\n".
            "💸 Pengeluaran : Rp".number_format($expense,0,',','.')."\n".
            "🏦 Tabungan : Rp".number_format($saving,0,',','.')."\n\n".
            "💰 Saldo : Rp".number_format($saldo,0,',','.');

        $this->sendTelegramMessage(
            $chatId,
            $message
        );
    }

    private function showHelp($chatId)
    {
        $message =
            "🤖 BOT KEUANGAN PRIBADI\n\n".

            "📌 PENCATATAN TRANSAKSI\n".
            "➕ /pemasukan nominal keterangan\n".
            "Contoh:\n".
            "/pemasukan 5000000 Gaji\n\n".

            "➖ /pengeluaran nominal keterangan\n".
            "Contoh:\n".
            "/pengeluaran 25000 Kopi\n\n".

            "📊 INFORMASI KEUANGAN\n".
            "💰 /saldo\n".
            "Melihat saldo saat ini\n\n".

            "📈 /laporan\n".
            "Laporan bulan berjalan\n\n".

            "📋 /mutasi\n".
            "Menampilkan 5 transaksi terakhir\n\n".

            "🎯 TARGET TABUNGAN\n".
            "🎯 /target nominal nama_target\n".
            "Contoh:\n".
            "/target 10000000 Laptop Baru\n\n".

            "🏦 /nabung id_target nominal\n".
            "Contoh:\n".
            "/nabung 1 500000\n".
            "Gunakan /targetku untuk melihat ID target\n\n".

            "🏁 /targetku\n".
            "Melihat progres target tabungan\n\n".

            "ℹ️ BANTUAN\n".
            "/help - Menampilkan bantuan\n".
            "/start - Menampilkan menu utama";

        $this->sendTelegramMessage($chatId, $message);
    }

    private function showReport($chatId)
    {
        $income = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where('type','income')
        ->whereMonth('transaction_date', now()->month)
        ->sum('amount');

        $expense = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where('type','expense')
        ->whereMonth('transaction_date', now()->month)
        ->sum('amount');

        $saving = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where('type','saving')
        ->whereMonth('transaction_date', now()->month)
        ->sum('amount');

        $saldo = $income - $expense - $saving;

        $message =
            "📊 LAPORAN BULAN INI\n\n".
            "💵 Pemasukan\n".
            "Rp".number_format($income,0,',','.')."\n\n".
            "💸 Pengeluaran\n".
            "Rp".number_format($expense,0,',','.')."\n\n".
            "🏦 Tabungan\n".
            "Rp".number_format($saving,0,',','.')."\n\n".
            "💰 Saldo\n".
            "Rp".number_format($saldo,0,',','.');

        $this->sendTelegramMessage(
            $chatId,
            $message
        );
    }

    private function showMutation($chatId)
    {
        $transactions = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->latest()
        ->limit(5)
        ->get();

        if($transactions->isEmpty()){

            $this->sendTelegramMessage(
                $chatId,
                "📭 Belum ada transaksi."
            );

            return;
        }

        $message = "📋 5 TRANSAKSI TERAKHIR\n\n";

        foreach($transactions as $trx){

            $icon = match($trx->type){
                'income' => '➕',
                'expense' => '➖',
                'saving' => '🏦'
            };

            $message .=
                $trx->transaction_date."\n".
                $icon." Rp".
                number_format(
                    $trx->amount,
                    0,
                    ',',
                    '.'
                )."\n".
                $trx->description."\n\n";
        }

        $this->sendTelegramMessage(
            $chatId,
            $message
        );
    }

    private function detectCategory($description, $type)
    {
        $description = strtolower($description);

        if ($type == 'income') {

            if (
                str_contains($description,'gaji') ||
                str_contains($description,'bonus') ||
                str_contains($description,'honor')
            ) {
                return 'Pendapatan';
            }

            return 'Lainnya';
        }

        if ($type == 'expense') {

            if (
                str_contains($description,'makan') ||
                str_contains($description,'minum')||
                str_contains($description,'jajan')
            ) {
                return 'Makanan & Minuman';
            }

            if (
                str_contains($description,'bengkel') ||
                str_contains($description,'servis') ||
                str_contains($description,'pertalite') ||
                str_contains($description,'bensin')
            ) {
                return 'Transportasi';
            }

            if (
                str_contains($description,'internet') ||
                str_contains($description,'wifi') ||
                str_contains($description,'listrik')
            ) {
                return 'Tagihan';
            }

            return 'Lainnya';
        }

        if ($type == 'saving') {
            return 'Tabungan';
        }

        return 'Lainnya';
    }

    private function saveTarget($chatId,$text)
    {
        $parts = explode(' ',$text,3);

        if(count($parts) < 3){

            $this->sendTelegramMessage(
                $chatId,
                "Contoh:\n/target 10000000 Laptop Baru"
            );

            return;
        }

        if(!is_numeric($parts[1])){

            $this->sendTelegramMessage(
                $chatId,
                "Nominal target harus angka."
            );

            return;
        }

        $goal = SavingGoal::create([
            'telegram_id' => $chatId,
            'goal_name' => $parts[2],
            'target_amount' => $parts[1]
        ]);

        $this->sendTelegramMessage(
            $chatId,
            "🎯 Target berhasil dibuat\n\n".
            "ID : {$goal->id}\n".
            "Nama : {$goal->goal_name}\n".
            "Target : Rp".number_format(
                $goal->target_amount,
                0,
                ',',
                '.'
            )
        );
    }

    private function showTarget($chatId)
    {
        $goals = SavingGoal::where(
            'telegram_id',
            $chatId
        )->get();

        if($goals->isEmpty()){

            $this->sendTelegramMessage(
                $chatId,
                "Belum ada target tabungan."
            );

            return;
        }

        $message = "🎯 DAFTAR TARGET TABUNGAN\n\n";

        foreach($goals as $goal){

            $saved = Transaction::where(
                'goal_id',
                $goal->id
            )->sum('amount');

            $progress = 0;

            if($goal->target_amount > 0){
                $progress =
                    ($saved / $goal->target_amount) * 100;
            }

            $message .=
                "ID : {$goal->id}\n".
                "🎯 {$goal->goal_name}\n".
                "Target : Rp".
                number_format(
                    $goal->target_amount,
                    0,
                    ',',
                    '.'
                )."\n".
                "Terkumpul : Rp".
                number_format(
                    $saved,
                    0,
                    ',',
                    '.'
                )."\n".
                "Progress : ".
                round($progress,1).
                "%\n\n";
        }

        $this->sendTelegramMessage(
            $chatId,
            $message
        );
    }

    private function sendMainMenu($chatId)
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        Http::post(
            "https://api.telegram.org/bot{$token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => 'Pilih menu:',
                'reply_markup' => [
                    'keyboard' => [
                        [
                            '/start',
                            '/saldo',
                            '/laporan'
                        ],
                        [
                            '/help',
                            '/mutasi',
                            '/targetku'
                        ]
                    ],
                    'resize_keyboard' => true
                ]
            ]
        );
    }

    private function checkDailyExpense($chatId)
    {
        $dailyExpense = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where(
            'type',
            'expense'
        )
        ->whereDate(
            'transaction_date',
            today()
        )
        ->sum('amount');

        $limit = 500000;

        if ($dailyExpense >= $limit) {

            $this->sendTelegramMessage(
                $chatId,
                "⚠️ Peringatan Pengeluaran\n\n".
                "Pengeluaran hari ini:\n".
                "Rp".number_format($dailyExpense,0,',','.')."\n\n".
                "Sudah melewati batas harian Rp".
                number_format($limit,0,',','.')
            );
        }
    }

    private function checkFinancialHealth($chatId)
    {
        $income = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where(
            'type',
            'income'
        )
        ->whereMonth(
            'transaction_date',
            now()->month
        )
        ->sum('amount');

        $expense = Transaction::where(
            'telegram_id',
            $chatId
        )
        ->where(
            'type',
            'expense'
        )
        ->whereMonth(
            'transaction_date',
            now()->month
        )
        ->sum('amount');

        if ($income <= 0) {
            return;
        }

        $percentage = ($expense / $income) * 100;

        if ($percentage >= 80) {

            $this->sendTelegramMessage(
                $chatId,
                "⚠️ Kondisi Keuangan\n\n".
                "Pengeluaran bulan ini sudah ".
                round($percentage,0).
                "% dari pemasukan.\n\n".
                "Pertimbangkan mengurangi pengeluaran."
            );
        }
    }

    private function saveSavingDeposit($chatId,$text)
    {
        $parts = explode(' ',$text,3);

        if(count($parts) < 3){

            $this->sendTelegramMessage(
                $chatId,
                "Contoh:\n/nabung 1 500000"
            );

            return;
        }

        $goalId = $parts[1];
        $amount = $parts[2];

        $goal = SavingGoal::find($goalId);

        if(!$goal){

            $this->sendTelegramMessage(
                $chatId,
                "Target tidak ditemukan."
            );

            return;
        }

        Transaction::create([
            'telegram_id' => $chatId,
            'type' => 'saving',
            'category' => 'Tabungan',
            'goal_id' => $goal->id,
            'amount' => $amount,
            'description' => 'Setoran '.$goal->goal_name,
            'transaction_date' => now()->toDateString()
        ]);

        $this->sendTelegramMessage(
            $chatId,
            "🏦 Tabungan berhasil ditambahkan\n\n".
            "Target : {$goal->goal_name}\n".
            "Nominal : Rp".
            number_format(
                $amount,
                0,
                ',',
                '.'
            )
        );
    }
}
