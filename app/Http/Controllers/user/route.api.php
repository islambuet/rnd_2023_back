<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

$url=str_replace('\\','/',explode('Http\\Controllers\\', dirname(__FILE__))[1]);
$controllerClass=Controllers\user\UserController::class;

Route::match(array('GET','POST'),$url.'/initialize', [$controllerClass, 'initialize']);
Route::post($url.'/login', [$controllerClass, 'login']);
Route::match(array('GET','POST'),$url.'/logout', [$controllerClass, 'logout']);

Route::middleware('logged-user')->group(function()use ($url,$controllerClass){
    Route::post($url.'/profile-picture', [$controllerClass, 'profilePicture']);
    Route::post($url.'/change-password', [$controllerClass, 'ChangePassword']);
});

