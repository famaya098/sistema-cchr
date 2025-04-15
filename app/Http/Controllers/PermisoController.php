<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PermisoController extends Controller
{
    public function index()
    {
        return Inertia::render('Permiso/Form', [
            'sedes' => [
                'Juayua-Sonsonate 1',
                'Juayua-Sonsonate 2',
                'San Salvador - Central',
                'Santa Ana'
            ],
            'cargos' => [
                'Técnico Agropecuario y Pesca',
                'Supervisor de Campo',
                'Asistente Administrativo',
                'Técnico de Sistemas'
            ]
        ]);
    }

    public function generarPDF(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dui' => 'required|regex:/^\d{8}-\d{1}$/',
                'nombre' => 'required|string',
                'fechaPermiso' => 'required|date',
                'horaInicio' => 'required',
                'horaFin' => 'required',
                'motivo' => 'required|string',
                'conDescuento' => 'required',
                'sede' => 'required|string',
                'cargo' => 'required|string',
                'diasReposicion' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            
            // Manejo específico para conDescuento (convertirlo a booleano)
            $conDescuento = filter_var($data['conDescuento'], FILTER_VALIDATE_BOOLEAN);
            
            // Manejo específico para diasReposicion (convertir de JSON si es necesario)
            if (is_string($data['diasReposicion'])) {
                $diasReposicion = json_decode($data['diasReposicion'], true);
            } else {
                $diasReposicion = $data['diasReposicion'];
            }
            
            $pdf = PDF::loadView('pdf.permiso', [
                'dui' => $data['dui'],
                'nombre' => $data['nombre'],
                'fecha' => now()->format('d/m/Y'),
                'fechaPermiso' => date('d/m/Y', strtotime($data['fechaPermiso'])),
                'horaInicio' => $data['horaInicio'],
                'horaFin' => $data['horaFin'],
                'tiempoSolicitado' => $this->calcularHoras($data['horaInicio'], $data['horaFin']) . ' horas',
                'motivo' => $data['motivo'],
                'conDescuento' => $conDescuento,
                'sede' => $data['sede'],
                'cargo' => $data['cargo'],
                'diasReposicion' => $diasReposicion,
            ]);

            return $pdf->download('solicitud_permiso.pdf');
        } catch (\Exception $e) {
            // Registrar el error para debugging
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar el PDF: ' . $e->getMessage()], 500);
        }
    }

    private function calcularHoras($inicio, $fin)
    {
        try {
            $horaInicio = strtotime($inicio);
            $horaFin = strtotime($fin);
            $diff = $horaFin - $horaInicio;
            if ($diff < 0) {
                $diff += 86400; // Agregar 24 horas si el fin es al día siguiente
            }
            return number_format($diff / 3600, 1);
        } catch (\Exception $e) {
            Log::error('Error al calcular horas: ' . $e->getMessage());
            return 0;
        }
    }
}