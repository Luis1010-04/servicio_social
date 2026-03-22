<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server   = env('MQTT_HOST', '127.0.0.1');
        $port     = env('MQTT_PORT', 1883);
        $clientId = 'laravel_test_client';

        $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
        
        try {
            $mqtt->connect();
            $mqtt->publish('test/canal', '¡Funciona perfectamente!', 0);
            $this->info('Mensaje enviado con éxito al bróker.');
            $mqtt->disconnect();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
