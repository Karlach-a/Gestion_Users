<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
//dependecia para validar mis datos 
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class UserController extends Controller
{
    //funcion para mostrar todos los registros
    public function index()
    {
        $respuesta = User::paginate(10); // Usar paginacion para limitar los datos

        if ($respuesta->isEmpty()) {
            $data = [
                'mensaje' => 'No hay registros disponibles',
                'status' => 404
            ];

            return response()->json($data, 404); // Mantener el código HTTP consistente
        }

        return response()->json([
            'mensaje' => 'Registros encontrados',
            'status' => 200,
            'data' => $respuesta,
        ], 200);
    }

    //funcion para guardar un registro
    public function store(Request $request)
    {

        $validador = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required'

        ]);

        //vdalidar si los datos son correctos
        if ($validador->fails()) {
            $data = [
                'mensaje' => 'Datos incorrectos',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        //creando un nuevo Usuario
        $NuevoRegistro = User::create($request->all());

        //si hay un error al guardar el registro
        if (!$NuevoRegistro) {
            $data = [
                'mensaje' => 'Error al guardar el registro',
                'status' => 500
            ];

            return response()->json($data, 500);
        }

        //si el registro se guardo correctamente
        $data = [
            'mensaje' => 'Usuario guardado',
            'status' => 200
        ];
    }

    //mostrar datos de usuario segun id

    public function show($id)
    {
        $respuesta = User::find($id);

        if (!$respuesta) {
            $data = [
                'mensaje' => 'Registro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        return response()->json($respuesta, 200);
    }

    //Eliminar registro
    public function destroy($id)
    {
        $respuesta = User::find($id);

        if (!$respuesta) {
            $data = [
                'mensaje' => 'Registro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $respuesta->delete();

        $data = [
            'mensaje' => 'Registro eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    //Actualizar registro
    public function update(Request $request, $id)
    {
        $respuesta = User::find($id);

        if (!$respuesta) {
            $data = [
                'mensaje' => 'Registro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validador = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validador->fails()) {
            $data = [
                'mensaje' => 'Error al actualizar el registro',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $respuesta->update($request->all());

        $data = [
            'mensaje' => 'Registro actualizado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    //Actualizar un campo de un registro
    public function updatePartial(Request $request, $id)
    {
        $respuesta = User::find($id);

        if (!$respuesta) {
            $data = [
                'mensaje' => 'Registro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validador = Validator::make($request->all(), [
            'name' => '',
            'last_name' => '',
            'email' => 'email',
            'password' => ''

        ]);

        if ($validador->fails()) {
            $data = [
                'mensaje' => 'Error al actualizar el registro',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        if ($request->has('name')) {
            $respuesta->name = $request->name;
        }

        if ($request->has('last_name')) {
            $respuesta->last_name = $request->last_name;
        }

        if ($request->has('email')) {
            $respuesta->email = $request->email;
        }
        if ($request->has('password')) {
            $respuesta->Usuario = $request->Usuario;
        }

        //if($request->has('Contrasena')){
        //   $respuesta->Contrasena = $request->Contrasena;
        // }

        $respuesta->save();

        $data = [
            'mensaje' => 'Registro actualizado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    //funcion para obtener la cantidad de usuarios registrados en el dia 

    public function estadisticaDiaria(Request $request, $dia)
    {

        $usuarios_dia = User::whereDate('created_at', $dia)->count();

        return response()->json(['Usuarios Registrados este dia: ' => $usuarios_dia]);
    }

    public function estadisticaSemana($semana)
    {
        //obtenemos fecha inicio y ficha  fina de semana actual
        $fechaInicio = Carbon::now()->startOfWeek();
        $fechaFin = Carbon::now()->endOfweek();

        //consulta en elocuent
        $usuarios_semana = User::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();
        //retornamos respuesta en formato JSON

        return response()->json([
            'semana' => Carbon::now()->week, //numero de semana actual 
            'Año' => Carbon::now()->year, //año actual 
            'fecha_inicio' => $fechaInicio->toDateString(),
            'Fecha_fin' => $fechaFin->toDateString(),
            'cantidad Usuarios por semana' => $usuarios_semana

        ]);
    }

    public function estadisticaMes(Request $request, $mes)
    {
       $anio=Carbon::now()->year;
       $mes=Carbon::now()->month;

       $fechaInicio=Carbon::create($anio,$mes,1)->startOfMonth();
       $fechafin=Carbon::create($anio,$mes,1)->endOfMonth();

       $usuarios_mes = User::whereBetween('created_at', [$fechaInicio, $fechafin])->count();

       return response()->json([
        'mes' => Carbon::now()->month, //numero de mes actual
        'Año' => Carbon::now()->year, //año actual 
        'fecha_inicio' => $fechaInicio->toDateString(),
        'Fecha_fin' => $fechafin->toDateString(),
        'cantidad Usuarios por mes' => $usuarios_mes

    ]);

    }
}
