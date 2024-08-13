<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CharacterSheetController;
Route::get('/', function () {
    return view('welcome');
});

Route::get("/character/{encoded}", CharacterSheetController::class );