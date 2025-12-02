<?php 

namespace App;

use App\Controllers\AuthController;
use App\Controllers\UsersController;

class Routes {
    public function define (): void 
    {
        Route::get('/test/{id}', fn($r,int $id) => "hello$id");
        Route::post('/register', [AuthController::class,'register']);
        Route::post('/login',[AuthController::class,'login']);
        Route::patch('/change_password',[AuthController::class,'changePassword']);

        Route::get('/users', [UsersController::class, 'index']);
        Route::patch('/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'delete']);
    }
}