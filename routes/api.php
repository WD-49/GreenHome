<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;

Route::post('/chat', [ChatController::class, 'geminiChat'])->name('api.chat.gemini');