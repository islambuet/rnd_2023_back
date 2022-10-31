<?php

use App\Http\Controllers as Controllers;
use Illuminate\Support\Facades\Route;
$url='trial/forms';
$controllerClass= Controllers\trial\TrialFormsController::class;
/** @noinspection DuplicatedCode */
Route::middleware('logged-user')->group(function()use ($url,$controllerClass){
    Route::match(['GET','POST'],$url.'/{cropId}/initialize', [$controllerClass, 'initialize']);
    Route::match(['GET','POST'],$url.'/{cropId}/get-items', [$controllerClass, 'getItemsForm']);
    Route::match(['GET','POST'],$url.'/{cropId}/get-item/{formId}', [$controllerClass, 'getItemForm']);
    Route::post($url.'/{cropId}/save-item', [$controllerClass, 'saveItemForm']);

    Route::match(['GET','POST'],$url.'/{cropId}/inputs/{formId}/get-items', [$controllerClass, 'getItemsInput']);
    Route::match(['GET','POST'],$url.'/{cropId}/inputs/{formId}/get-item/{inputId}', [$controllerClass, 'getItemInput']);
    Route::post($url.'/{cropId}/inputs/{formId}/save-item', [$controllerClass, 'saveItemInput']);
});

$url='trial/data';
$controllerClass= Controllers\trial\TrialDataController::class;
/** @noinspection DuplicatedCode */
Route::middleware('logged-user')->group(function()use ($url,$controllerClass){
    Route::match(['GET','POST'],$url.'/{cropId}/{formId}/initialize', [$controllerClass, 'initialize']);
});

