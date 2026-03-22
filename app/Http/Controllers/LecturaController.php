<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lectura;
use App\Models\Componente; // Usamos solo el singular que es el que tienes en tu carpeta Models

class LecturaController extends Controller
{
    public function store(Request $request)
    {
        // Validamos que al menos llegue el ID del esclavo
        $data = $request->all();
        $esclavoId = $data['esclavo_id'] ?? null;

        if (!$esclavoId) {
            return response()->json(['error' => 'Falta esclavo_id'], 400);
        }

        // Mapeo de datos del ESP32 -> Nombres en tu base de datos
        $sensores = [
            'temperatura' => $data['temperatura'] ?? 0,
            'humedad'     => $data['humedad'] ?? 0,
            'suelo'       => $data['suelo'] ?? 0,
            'luz'         => $data['luz'] ?? 0,
            'wifi_rssi'   => $data['rssi'] ?? 0,
        ];

        foreach ($sensores as $nombre => $valor) {
            // Buscamos el componente que coincida con el nombre y pertenezca al esclavo
            $componente = Componente::where('nombre', 'like', "%$nombre%")
                ->whereHas('detalleEsclavoComponentes', function($q) use ($esclavoId) {
                    $q->where('esclavo_id', $esclavoId);
                })->first();

            if ($componente) {
                Lectura::create([
                    'componente_id' => $componente->id,
                    'valor' => $valor,
                ]);
            }
        }

        return response()->json(['status' => 'success'], 201);
    }
}