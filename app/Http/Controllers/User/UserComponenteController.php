<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use InfluxDB2\Client as InfluxClient;
use InfluxDB2\Point;

class UserComponenteController extends Controller
{
   public function controlar(Request $request, $esclavoId)
{
    $userId = Auth::id(); // <--- ESTA LÍNEA ES VITAL

    $esclavo = DB::table('maestros_esclavos as me')
        ->join('maestros_usuarios as mu', 'me.maestro_id', '=', 'mu.id')
        ->where('me.id', $esclavoId)
        ->where('mu.user_id', $userId)
        ->select('me.numero_serie', 'mu.user_id')
        ->first();

    if ($esclavo) {
        try {
            $mqtt = new \PhpMqtt\Client\MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'laravel_client_' . uniqid());
            $mqtt->connect();
            
            // Tópico idéntico al que usaste en CMD
            $topic = "v1/usuarios/{$esclavo->user_id}/nodos/{$esclavo->numero_serie}/{$request->nombre_field}";
            
            $mqtt->publish($topic, (string)$request->estado, 0);
            $mqtt->disconnect();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    return response()->json(['status' => 'error', 'message' => 'No autorizado'], 403);
}
}