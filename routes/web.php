<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('payouts.index');
});

Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
Route::put('/payouts/{payout}', [PayoutController::class, 'update'])->name('payouts.update');
Route::patch('/payouts/{payout}/status', [PayoutController::class, 'updateStatus'])->name('payouts.update-status');
Route::delete('/payouts/{payout}', [PayoutController::class, 'destroy'])->name('payouts.destroy');
