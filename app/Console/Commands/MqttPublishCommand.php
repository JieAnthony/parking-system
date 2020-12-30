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
            'host' => 'broker.emqx.io',
            'port' => 1883,
            'time_out' => 5,
            'user_name' => 'username',
            'password' => 'password',
            'client_id' => Client::genClientID(),
            'keep_alive' => 20,
            'properties' => [
                'session_expiry_interval' => 60,
                'receive_maximum' => 200,
                'topic_alias_maximum' => 200,
            ],
            'protocol_level' => 5,
        ];
        $time = time();
        $this->info($time);
        Coroutine\run(function () use ($config, $time) {
            $client = new Client($config, ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024]);
            while (! $client->connect()) {
                Coroutine::sleep(3);
                $client->connect();
            }
            $response = $client->publish(
                'test',
                '{"time":'.$time.'}',
                1,
                0,
                0,
                ['topic_alias' => 1]
            );
            dump($response);
            Coroutine::sleep(3);
        });
    }
}
