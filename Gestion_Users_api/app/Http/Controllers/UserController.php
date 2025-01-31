<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
//dependecia para validar mis datos 
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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



    //mostrar datos de usuario segun nombre, correo o fecha de creacion 


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


    public function shownef(Request $request)
    {
        //$respuesta = User::find($id);
        //Obtener los filtros desde la consulta
        $nombre = $request->input('nombre');
        $email = $request->input('email');
        $fechaRegistro = $request->input('created_at');

        //consulta

        $usuarios = User::query()
            ->when($nombre, fn($query) => $query->where('name', 'LIKE', "%$nombre%"))
            ->when($email, fn($query) => $query->where('email', $email))
            ->when($fechaRegistro, fn($query) => $query->whereDate('created_at', $fechaRegistro))
            ->get();


        if ($usuarios->isEmpty()) {
            $data = [
                'mensaje' => 'Registro no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        return response()->json($usuarios, 200);
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

    public function estadisticaDiaria($dia)
    {

        $usuarios_dia = User::whereDate('created_at', $dia)->count();
        return response()->json(['Usuarios Registrados este dia: ' => $usuarios_dia]);
    }


    //funcion para obtener la cantidad de usuarios registrados en la semana
    public function estadisticaSemana(Request $request)
    {
        // Validación de la fecha en la petición
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date', // La fecha es obligatoria y debe ser válida
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener la fecha de referencia desde la solicitud
        $fechaReferencia = Carbon::parse($request->input('fecha'));

        // Aseguramos que la semana empieza el lunes
        $fechaInicio = $fechaReferencia->copy()->startOfWeek(Carbon::MONDAY); // Lunes de la semana
        $fechaFin = $fechaReferencia->copy()->endOfWeek(Carbon::SUNDAY); // Domingo de la semana



        // Obtener el número de usuarios registrados en esa semana
        $usuarios_semana = User::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();


        return response()->json([
            'semana' => $fechaReferencia->week, // Número de semana del año
            'anio' => $fechaReferencia->year, // Año correspondiente
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_fin' => $fechaFin->toDateString(),
            'cantidad_usuarios' => $usuarios_semana
        ]);
    }

    //funcion muestra usuarios registrados por mes 

    public function estadisticaMes(Request $request)
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'mes' => 'nullable|integer|min:1|max:12',  // Opcional, debe ser un número entre 1 y 12
            'anio' => 'nullable|integer|min:2000|max:' . Carbon::now()->year // Opcional, desde el año 2000 hasta el actual
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener mes y año desde el request, o usar el actual si no se envían
        $mes = $request->input('mes', Carbon::now()->month);
        $anio = $request->input('anio', Carbon::now()->year);

        // Fechas de inicio y fin del mes seleccionado
        $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth();
        $fechaFin = Carbon::create($anio, $mes, 1)->endOfMonth();

        // Contar usuarios registrados en ese mes
        $usuarios_mes = User::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();

        return response()->json([
            'mes' => $mes,
            'anio' => $anio,
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_fin' => $fechaFin->toDateString(),
            'cantidad_usuarios' => $usuarios_mes
        ]);
    }
}
