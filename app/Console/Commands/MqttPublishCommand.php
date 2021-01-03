<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Simps\MQTT\Client;
use Swoole\Coroutine;

class MqttPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $config = [
            'host' => '172.17.0.2',
            'port' => 1883,
            'time_out' => 5,
            'user_name' => '',
            'password' => '',
            'client_id' => 'parking_35bc9224',//Client::genClientID(),
            'keep_alive' => 0,
            'properties' => [
                'session_expiry_interval' => 0,
                'receive_maximum' => 200,
                'topic_alias_maximum' => 200,
            ],
            'protocol_level' => 5,
        ];
        $time = time();
        $this->info($time);
//        Coroutine\run(function () use ($config, $time) {
//            $client = new Client($config, ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024]);
//            while (!$client->connect()) {
//                Coroutine::sleep(3);
//                $client->connect();
//            }
//            $response = $client->publish(
//                'on',
//                '{"time":' . $time . '}',
//                2,
//                0,
//                0,
//                ['topic_alias' => 1]
//            );
//            dump($response);
//            Coroutine::sleep(3);
//        });
        $sConfig = [
            'open_mqtt_protocol' => true,
            'package_max_length' => 2 * 1024 * 1024,
            'connect_timeout' => 1.0,
            'write_timeout' => 5.0,
            'read_timeout' => 0.5,
        ];
        Coroutine\run(function () use($config,$sConfig,$time){
            $client = new Client($config, $sConfig);
            while (!$client->connect()) {
                Coroutine::sleep(3);
                $client->connect();
            }
            while (true) {
                $response = $client->publish('off', '{"time":' . $time . '}', 1);
                var_dump($response);
                Coroutine::sleep(3);
            }
        });

    }
}
