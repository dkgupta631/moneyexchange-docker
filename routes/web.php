<?php
use Illuminate\Support\Facades\Route;
use Inertia\inertia;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MoneyExchangeController;
use App\Http\Controllers\Web\MoneyTransferController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Auth\LoginController;

// Route::get('/', function () {
//     return inertia('Home');
// });

Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en','th-TH','km'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return back();
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ExchangeRate', [HomeController::class, 'ShowExchangeRate'])->name('showExchangeRate');
Route::get('/moneyexchange', [MoneyExchangeController::class, 'openForm'])->name('open.moneyexchange.form');
Route::get('/moneyexchange-invoices/{invoice_number}', [MoneyExchangeController::class, 'showMoneyExchangeInvoices'])->name('MoneyExchangeInvoices.show');
Route::post('/get-exchange-rate', [MoneyExchangeController::class, 'getRate']);
Route::post('/calculateMoney', [MoneyExchangeController::class, 'SaveCalculatedMoney']);

Route::get('/money-transfer-in', [MoneyTransferController::class, 'moneyTransferINForm'])->name('moneytransfer.in.form');
Route::post('/money-transfer-in/store', [MoneyTransferController::class, 'storeTransferIN']);
Route::get('/money-transfer/invoice/{encodedInvoice}', [MoneyTransferController::class, 'showTransferINInvoice'])->name('money.transfer.in.invoice');

Route::get('/money-transfer-OUT', [MoneyTransferController::class, 'moneyTransferOUTForm'])->name('moneytransfer.out.form');
Route::post('/money-transfer-OUT/store', [MoneyTransferController::class, 'storeTransferOUT']);



// Registration
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
 
// Login / Logout
Route::get('/login', [LoginController::class, 'create'])->name('teller.login');
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');



