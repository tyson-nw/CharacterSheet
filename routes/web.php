<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CharacterSheetController;
use App\Http\Controllers\ExampleController;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/character/{encoded}", CharacterSheetController::class );

Route::get("/example/{shortcut}", ExampleController::class);