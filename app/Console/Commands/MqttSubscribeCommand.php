<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Simps\MQTT\Client;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Types;
use Swoole\Coroutine;

class MqttSubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start mqtt Subscribe';

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

        Coroutine\run(function () use ($config) {
            $client = new Client($config, ['open_mqtt_protocol' => true, 'package_max_length' => 2 * 1024 * 1024]);
            while (! $data = $client->connect()) {
                Coroutine::sleep(3);
                $client->connect();
            }
            $topics['test'] = [
                'qos' => 1,
                'no_local' => true,
                'retain_as_published' => true,
                'retain_handling' => 2,
            ];
            $timeSincePing = time();
            $res = $client->subscribe($topics);
            // 订阅的结果
            dump(1, $res);
            while (true) {
                $buffer = $client->recv();
                if ($buffer && $buffer !== true) {
                    $timeSincePing = time();
                    // 收到的数据包
                    dump(2, $buffer);
                }
                if (isset($config['keep_alive']) && $timeSincePing < (time() - $config['keep_alive'])) {
                    $buffer = $client->ping();
                    if ($buffer) {
                        $this->info('send ping success'.PHP_EOL);
                        $timeSincePing = time();
                    } else {
                        $client->close();
                        break;
                    }
                }
                dump(3, $buffer);

                // QoS1 发布回复
                if (isset($buffer['type']) && $buffer['type'] === Types::PUBLISH && isset($buffer['qos']) && $buffer['qos'] === 1) {
                    $client->send(
                        [
                            'type' => Types::PUBACK,
                            'message_id' => $buffer['message_id'],
                            'code' => ReasonCode::SUCCESS,
                        ]
                    );
                }
            }
        });
    }
}
