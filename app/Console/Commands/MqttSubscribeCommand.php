<?php

namespace App\Console\Commands;

use App\Events\TestEvent;
use App\Jobs\TestJob;
use Illuminate\Console\Command;
use Simps\MQTT\Client;
use Simps\MQTT\Protocol\Types;
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
    protected $description = 'mqtt mqtt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Coroutine\run(function () {
            $config = [
                'userName' => '', // 用户名
                'password' => '', // 密码
                'clientId' => '', // 客户端id
                'keepAlive' => 50, // 默认0秒，设置成0代表禁用
                'protocolName' => 'MQTT', // 协议名，默认为MQTT(3.1.1版本)，也可为MQIsdp(3.1版本)
                'protocolLevel' => 4, // 协议等级，MQTT3.1.1版本为4，5.0版本为5，MQIsdp为3
                'properties' => [], // MQTT5 中所需要的属性
                'delay' => 3000, // 重连时的延迟时间 (毫秒)
                'maxAttempts' => 5, // 最大重连次数。默认-1，表示不限制
                'swooleConfig' => []
            ];
            $configObj = new \Simps\MQTT\Config\ClientConfig($config);
            $client = new Client('172.17.0.3', 1883, $configObj);
            $client->connect(true);
            $topics['anthony'] = 1;
            $topics['anthony1'] = 1;
            $client->subscribe($topics);
            $timeSincePing = time();
            while (true) {
                $buffer = $client->recv();
                if ($buffer && $buffer !== true) {
                    var_dump($buffer);
                    $message = $buffer['message'];
                    if (isset($message) && $message) {
                        # TODO
                    }
                    // QoS1 PUBACK
                    if ($buffer['type'] === Types::PUBLISH && $buffer['qos'] === 1) {
                        $client->send(
                            [
                                'type' => Types::PUBACK,
                                'message_id' => $buffer['message_id'],
                            ],
                            false
                        );
                    }
                    if ($buffer['type'] === Types::DISCONNECT) {
                        echo "Broker is disconnected\n";
                        $client->close();
                        break;
                    }
                    $timeSincePing = time();
                }
                if ($timeSincePing <= (time() - $client->getConfig()->getKeepAlive())) {
                    $buffer = $client->ping();
                    if ($buffer) {
                        echo 'send ping success' . PHP_EOL;
                        $timeSincePing = time();
                    }
                }
            }
        });
    }
}
