<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Гостевые маршруты
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Маршруты для авторизованных пользователей
Route::middleware('auth')->group(function () {
    // Выход
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Дашборд
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Чаты
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats/{chat}/message', [ChatController::class, 'sendMessage'])->name('chats.message');
    Route::put('/chats/{chat}/name', [ChatController::class, 'updateName'])->name('chats.update-name');
    
    // Личный чат с пользователем
    Route::get('/chat/personal/{user}', [ChatController::class, 'createPersonal'])->name('chats.personal');
    
    // Групповые чаты
    Route::get('/chats/group/create', [ChatController::class, 'createGroup'])->name('chats.group.create');
    Route::post('/chats/group', [ChatController::class, 'storeGroup'])->name('chats.group.store');

    // Администрирование (только для администраторов)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [AdminController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/employees/{user}/edit', [AdminController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/employees/{user}', [AdminController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{user}', [AdminController::class, 'destroyEmployee'])->name('employees.destroy');
    });
});

// Главная страница
Route::redirect('/', '/dashboard');
