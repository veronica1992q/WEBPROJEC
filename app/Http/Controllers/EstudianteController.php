<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Log;

class EstudianteController extends Controller
{
   
        /**
         * Display a listing of the resource.
         */
        /**
         * @OA\Get(
         *      path="/api/estudiantes"
         *      sumary="Lista de estudiante",
         *      @OA\Response(response=200, description="Lista obtenida correctamente)
         * )
         */
        // Listar todos los estudiantes 
        public function index()
        {
            return response()->json(Estudiante::with('paralelo')->get(), 200);   
        }
      
    // Crear un nuevo estudiante
    /**
     * @OA\Post(
     *     path="/api/estudiantes",
     *     summary="Crear un nuevo estudiantes",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"nombre","cedula","correo","paralelo_id"},
     *              @OA\Property(property="nombre", type="string", example="Juan pérez"),
     *              @OA\Property(property="cedula", type="string", exaple="1101234567")
     *              @OA\Property(property="correo", type="string", format="email", example=juan@example.com),
     *              @OA\Property(property="paralelo_id", type="integer", example=1)
     *          )
     *       ),
     *       @OA\Parameter(
     *           name="Accept",
     *           in="header",
     *           required=true,
     *           @OA\Schema(type="string", default="application/json"),
     *           description="Indica que se espara una repuesta en formato JSON"
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|unique:estudiantes,cedula',
            'correo' => 'required|email|unique:estudiantes,correo',
            'paralelo_id' => 'required|exists:paralelos,id',
        ]);

        $estudiante = Estudiante::create($validated);

        return response()->json([
            'mensaje' => 'Estudiante creado correctamente',
            'estudiante' => $estudiante
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/estudiantes/{id}",
     *     summary="Mostrar un estudiante especifico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         decription="ID del estudiante",
     *         required=true,
     *         @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Estudiante encontrado"),
     *   @OA\Response(response=404, description="Estudiante no encontrado"),
     * )
     */
    // Mostrar un estudiante específico
    public function show($id)
    {
        $estudiante = Estudiante::with('paralelo')->find($id);

        if (!$estudiante) {
            return response()->json(['mensaje' => 'Estudiante no encontrado'], 404);
        }

        return response()->json($estudiante);
    }

    // Actualizar un estudiante
    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::find($id);

        if (!$estudiante) {
            return response()->json(['mensaje' => 'Estudiante no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'cedula' => 'sometimes|required|unique:estudiantes,cedula,' . $id,
            'correo' => 'sometimes|required|email|unique:estudiantes,correo,' . $id,
            'paralelo_id' => 'sometimes|required|exists:paralelos,id',
        ]);
        /**
         * @OA\Put(
         *      path="/api/estudiantes/{id}",
     *          summary="MostrarActualizar un estudiante Existente",
     *          @OA\Parameter(
     *          name="id",
     *          in="path",
     *          decription="ID del estudiante a actualizar",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *           @OA\JsonContent(  
     *               @OA\Property(property="nombre", type="string", example="Pedro Gómez"),
     *               @OA\Property(property="cedula", type="string", exaple="1101234569")
     *               @OA\Property(property="correo", type="string", format="email", example=pedro@example.com),
     *               @OA\Property(property="paralelo_id", type="integer", example=2)
     *            )
     *         ),
     *         @OA\Response(response=200, description="Estudiante actualizado correctamente"),
     *         @OA\Response(response=404, description="Estudiante no encontrado"),
     *     )
         */
        $estudiante->update($validated);

        return response()->json([
            'mensaje' => 'Estudiante actualizado correctamente',
            'estudiante' => $estudiante
        ]);
    }

    // Eliminar un estudiante
    public function destroy($id)
    {
        $estudiante = Estudiante::find($id);

        if (!$estudiante) {
            return response()->json(['mensaje' => 'Estudiante no encontrado'], 404);
        }

        $estudiante->delete();

        return response()->json(['mensaje' => 'Estudiante eliminado correctamente']);
    }
}
