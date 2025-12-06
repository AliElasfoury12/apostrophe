<?php 

namespace App;

use App\Controllers\AuthController;
use App\Controllers\UsersController;

class Routes {
    public function define (): void 
    {
        Route::get('/test/{id}', fn($r,int $id) => "hello$id");
        $this->AuthRoutes();
        $this->UsersRoutes();      
    }

    private function AuthRoutes () 
    {
        Route::post('/register', [AuthController::class,'register']);
        Route::post('/login',[AuthController::class,'login']);
        Route::patch('/change_password/{user_id}',[AuthController::class,'changePassword'])->middleware(['auth:jwt']);
    }

    private function UsersRoutes () 
    {
        Route::get('/users',[UsersController::class, 'index'])->middleware(['auth:jwt']);
        Route::patch('/users/{id}',[UsersController::class, 'update'])->middleware(['auth:jwt']);
        Route::delete('/users/{id}',[UsersController::class, 'delete'])->middleware(['auth:jwt']);
    }
}