<?php

use App\Http\Controllers as Controllers;
use Illuminate\Support\Facades\Route;
$url='variety-configuration/selection';
$controllerClass= Controllers\variety_configuration\VarietySelectionController::class;
/** @noinspection DuplicatedCode */
Route::middleware('logged-user')->group(function()use ($url,$controllerClass){
    Route::match(['GET','POST'],$url.'/{cropId}/{year}/initialize', [$controllerClass, 'initialize']);
    Route::match(['GET','POST'],$url.'/{cropId}/{year}/get-items', [$controllerClass, 'getItems']);
    Route::match(['GET','POST'],$url.'/{cropId}/{year}/get-item/{itemId}', [$controllerClass, 'getItem']);
});