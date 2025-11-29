<?php 

namespace App;

use App\Controllers\AuthController;
use App\Controllers\UsersController;

class Routes {
    public function define (): void 
    {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',[AuthController::class, 'login']);
        Route::get('/users', [UsersController::class, 'index']);
        Route::put('/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'delete']);
    }
}