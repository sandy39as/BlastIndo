<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

Route::get('/webhook/meta', [WebhookController::class, 'verify']);
Route::post('/webhook/meta', [WebhookController::class, 'handle']);
