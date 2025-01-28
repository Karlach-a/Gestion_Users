<?php

namespace App\Http\Controllers;

use App\Models\Registros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Corregido el namespace
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400); // Respuesta con errores de validación
        }

        // Creación del usuario
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hashear contraseña
        ]);

        // Generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Respuesta exitosa
        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        //creamos el token
        $token = $user->createToken('auth_token')->plainTextToken;

         //guardamos los Login en la tabla registros
        $registro = Registros::create([
            'id_user' => $user->id,
            'fecha_hora' => Carbon::now(),
            'accion' => 'Login',

        ]);


        return response()->json([
            'message' => 'Hi ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        //guardamos los Logout en la tabla registros 
        $registro = Registros::create([
            'id_user' => $request->user()->id,
            'fecha_hora' => Carbon::now(),
            'accion' => 'Logout',

        ]);
        return [
            'message' => 'You hace successfully logged out and the token was successfully deleted',
            'datos' => $request->user()
        ];
    }
}
