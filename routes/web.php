<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionWebController;
use App\Models\LoginToken;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Webhook OK';
});

Route::post('/telegram/webhook', 
    [TelegramController::class, 'webhook'] 
);

Route::get(
    '/dashboard',
    [DashboardController::class,'index']
);

Route::get('/transactions', 
    [TransactionWebController::class, 'index']
);
Route::delete('/transactions/{id}', 
    [TransactionWebController::class, 'destroy']
);
Route::get('/transactions/{id}/edit', 
    [TransactionWebController::class, 'edit']
);
Route::put('/transactions/{id}', 
    [TransactionWebController::class, 'update']
);

Route::get('/transactions', 
    [TransactionWebController::class, 'index']
);

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', function (Illuminate\Http\Request $request) {
    session(['telegram_id' => $request->telegram_id]);
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('telegram.auth');

Route::get('/transactions', [TransactionWebController::class, 'index'])
    ->middleware('telegram.auth');


Route::get('/login/telegram/{token}', function ($token) {

    $data = LoginToken::where('token', $token)
        ->where('expires_at', '>', now())
        ->first();

    if (!$data) {
        return "Token tidak valid atau sudah expired";
    }

    session(['telegram_id' => $data->telegram_id]);

    return redirect('/dashboard');
});

use App\Http\Controllers\SavingGoalController;

Route::get(
    '/saving-goals',
    [SavingGoalController::class,'index']
);

Route::get(
    '/saving-goals/{id}/edit',
    [SavingGoalController::class,'edit']
);

Route::put(
    '/saving-goals/{id}',
    [SavingGoalController::class,'update']
);

Route::delete(
    '/saving-goals/{id}',
    [SavingGoalController::class,'destroy']
);