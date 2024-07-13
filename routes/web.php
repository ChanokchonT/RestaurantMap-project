<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;

Route::resource('restaurant', RestaurantController::class);

Route::get('/', [RestaurantController::class, 'index']);

