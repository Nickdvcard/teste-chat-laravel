<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Chat\Index;
use App\Livewire\Chat\ChatMain;
use App\Livewire\Users;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/chat', Index::class)->name('chat.index');
    Route::get('/chat/{query}', ChatMain::class)->name('chat'); //query é o id da conversa, não o deu um dos seus participantes
    Route::get('/users', Users::class)->name('users');
});


