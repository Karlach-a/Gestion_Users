<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registros;
//dependecia para validar mis datos 
use Illuminate\Support\Facades\Validator;

class registrosController extends Controller
{
    //funcion para mostrar todos los registros
    public function index()
    {
       $respuesta =  Registros::all();
       
       if($respuesta->isEmpty()){
           $data= ['mensaje' => 'No hay registros',
                        'status' => 200];   

            return response()->json($data, 404);
       }
       

       return response()->json($respuesta, 200);
    }

    //funcion para guardar un registro
    public function store(Request $request)
    {

        $validador = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email',
            'Usuario' => 'required',
            'Contrasena' => 'required'
        ]);

        //vdalidar si los datos son correctos
        if($validador->fails())
        {
            $data = ['mensaje' => 'Datos incorrectos',
                        'status' => 400];

                return response()->json($data, 400);
        }
        
        //creando un nuevo registro
        $NuevoRegistro = Registros::create($request->all());

        //si hay un error al guardar el registro
        if(!$NuevoRegistro){
            $data = ['mensaje' => 'Error al guardar el registro',
                        'status' => 500];

                return response()->json($data, 500);
        }

        //si el registro se guardo correctamente
        $data = ['mensaje' => 'Registro guardado',
                        'status' => 200];

    }

    public function show($id)
    {
        $respuesta = Registros::find($id);

        if(!$respuesta){
            $data = ['mensaje' => 'Registro no encontrado',
                        'status' => 404];

                return response()->json($data, 404);
        }

        return response()->json($respuesta, 200);
    }

    //Eliminar registro
    public function destroy($id)
    {
        $respuesta = Registros::find($id);

        if(!$respuesta){
            $data = ['mensaje' => 'Registro no encontrado',
                        'status' => 404];

                return response()->json($data, 404);
        }

        $respuesta->delete();

        $data = ['mensaje' => 'Registro eliminado',
                        'status' => 200];

        return response()->json($data, 200);
    }


    //Actualizar registro
    public function update(Request $request, $id)
    {
        $respuesta = Registros::find($id);

        if(!$respuesta){
            $data = ['mensaje' => 'Registro no encontrado',
                        'status' => 404];

                return response()->json($data, 404);
        }

        $validador = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email',
            'Usuario' => 'required',
            'Contrasena' => 'required'
        ]);

        if($validador->fails())
        {
            $data = ['mensaje' => 'Error al actualizar el registro',
                        'status' => 400];

                return response()->json($data, 400);
        }

        $respuesta->update($request->all());

        $data = ['mensaje' => 'Registro actualizado',
                        'status' => 200];

                        return response()->json($data, 200);
    }

    //Actualizar un campo de un registro
    public function updatePartial(Request $request, $id)
    {
        $respuesta = Registros::find($id);

        if(!$respuesta){
            $data = ['mensaje' => 'Registro no encontrado',
                        'status' => 404];

                return response()->json($data, 404);
        }

        $validador = Validator::make($request->all(), [
            'nombre' => '',
            'apellido' => '',
            'correo' => 'email',
            'Usuario' => '',
            'Contrasena' => ''
        ]);

        if($validador->fails())
        {
            $data = ['mensaje' => 'Error al actualizar el registro',
                        'status' => 400];

                return response()->json($data, 400);
        }

        if($request->has('nombre')){
            $respuesta->nombre = $request->nombre;
        }

        if($request->has('apellido')){
            $respuesta->apellido = $request->apellido;
        }

        if($request->has('correo')){
            $respuesta->correo = $request->correo;
        }
        if($request->has('Usuario')){
            $respuesta->Usuario = $request->Usuario;
        }

        if($request->has('Contrasena')){
            $respuesta->Contrasena = $request->Contrasena;
        }

        $respuesta->save();

        $data = ['mensaje' => 'Registro actualizado',
                        'status' => 200];

                        return response()->json($data, 200);
    }

}
