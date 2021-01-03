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

        Coroutine\run(function () {
            $config = config('mqtt');
            $client = new Client($config['config'], $config['swooleConfig']);
            while (!$data = $client->connect()) {
                Coroutine::sleep(3);
                $client->connect();
            }
            $topics = [
                'on' => [
                    'qos' => 2,
                    'no_local' => true,
                    'retain_as_published' => true,
                    'retain_handling' => 2,
                ],
                'off' => [
                    'qos' => 2,
                    'no_local' => true,
                    'retain_as_published' => true,
                    'retain_handling' => 2,
                ]
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
                if (isset($config['config']['keep_alive']) && $timeSincePing < (time() - $config['config']['keep_alive'])) {
                    $buffer = $client->ping();
                    dump(3, $buffer);
                    if ($buffer) {
                        $this->info(now()->toDateTimeString() . 'send ping success' . PHP_EOL);
                        $timeSincePing = time();
                    } else {
                        $client->close();
                        break;
                    }
                }

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
