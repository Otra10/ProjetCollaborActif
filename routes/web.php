<?php
use App\Http\Controllers\ListeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListeController::class, 'index'])->name('listes.index');
Route::get('/listes/{liste}', [ListeController::class, 'show'])->name('listes.show');
Route::get('/listes/{liste}/membres', [ListeController::class, 'membres'])->name('listes.membres');
