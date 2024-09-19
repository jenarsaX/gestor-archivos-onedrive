<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MicrosoftGraphController;
use App\Http\Controllers\FileController;


Route::get('/auth/redirect', [MicrosoftGraphController::class, 'redirectToAuth'])->name('auth.redirect');
Route::get('/auth/callback', [MicrosoftGraphController::class, 'handleCallback']);
Route::get('/files', [MicrosoftGraphController::class, 'listFiles'])->name('list.files');
Route::get('/shared-files', [MicrosoftGraphController::class, 'listSharedFiles'])->name('list.shared.files');
Route::get('/download', [FileController::class, 'download'])->name('file.download');