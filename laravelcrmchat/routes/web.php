<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('chat');
    }
    return view('auth.login');
})->name('home');

// Auth routes
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');

// Chat routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/room', [ChatController::class, 'createRoom'])->name('chat.room.create');
    Route::get('/chat/room/{room}', [ChatController::class, 'showRoom'])->name('chat.room');
    Route::post('/chat/room/{room}/message', [ChatController::class, 'sendMessage'])->name('chat.message.send');
    Route::post('/chat/room/{room}/read', [ChatController::class, 'markAsRead'])->name('chat.room.read');
});
