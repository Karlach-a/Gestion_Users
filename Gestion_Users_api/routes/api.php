<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\registrosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

//llamando para mostrar todos los registros de usuario
Route::get('/usuarios', [UserController::class, 'index']);


//llamando para guardar un registro
//Route::post('/registros', [registrosController::class, 'store']);

//llamando para mostrar un solo registro de usuario
Route::get('/usuarios/{id}', [UserController::class, 'show']);


//Eliminar registro de usuario 
Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

//Actualizar registro completo de usuario
Route::put('/usuarios/{id}', [UserController::class, 'update']);

//Actualizar un campo de un registro de usuario
Route::patch('/usuarios/{id}', [UserController::class, 'updatePartial']);


//Autenticacion 
//registrar al usuario 
Route::post('/register', [AuthController::class,'register']);

//Realizar el login 
Route::post('/login', [AuthController::class,'login']);

//
Route::middleware(['auth:sanctum','refresh.token'])->group(function(){ //refresh.token para que expire en 5min el token
    
    Route::get('/logout', [AuthController::class,'logout']);
    Route::get('/historial/{id_user}', [registrosController::class, 'showbyuserid']);
    Route::get('/estadistica/dias/{dia}', [UserController::class, 'estadisticaDiaria']);
    Route::get('/estadistica/semana/{semana}', [UserController::class, 'estadisticaSemana']);
    Route::get('/estadistica/mes/{mes}', [UserController::class, 'estadisticaMes']);
    
});
