<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScoreController;

//Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// Score Routes
Route::post('/save-score', [ScoreController::class, 'store']);
Route::get('/scores', [ScoreController::class, 'index']);
Route::get('/scores/user/{id}', [ScoreController::class, 'userScores']);
