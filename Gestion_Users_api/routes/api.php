<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\registrosController;

//llamando para mostrar todos los registros
Route::get('/registros', [registrosController::class, 'index']);


//llamando para guardar un registro
Route::post('/registros', [registrosController::class, 'store']);

//llamando para mostrar un solo registro
Route::get('/registros/{id}', [registrosController::class, 'show']);


//Eliminar registro 
Route::delete('/registros/{id}', [registrosController::class, 'destroy']);

//Actualizar registro completo
Route::put('/registros/{id}', [registrosController::class, 'update']);

//Actualizar un campo de un registro
Route::patch('/registros/{id}', [registrosController::class, 'updatePartial']);