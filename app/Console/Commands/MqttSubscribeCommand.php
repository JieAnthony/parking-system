<?php

namespace App\Console\Commands;

use App\Events\CarEnterEvent;
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
    protected $description = 'mqtt subscribe';

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
        $this->info('开始订阅' . PHP_EOL);
        Coroutine\run(function () {
            $config = config('mqtt');
            $configObj = new \Simps\MQTT\Config\ClientConfig($config['config']);
            $client = new Client($config['host'], $config['port'], $configObj);
            $client->connect(true);
            $topics['serve/enter'] = 1;
            $topics['serve/out'] = 1;
            $client->subscribe($topics);
            $this->info('订阅成功' . PHP_EOL);
            $timeSincePing = time();
            while (true) {
                $buffer = $client->recv();
                dump($buffer);
                if ($buffer && $buffer !== true) {
                    /**
                     * array:7 [
                     *  "type" => 3
                     *  "dup" => 0
                     *  "qos" => 1
                     *  "retain" => 0
                     *  "topic" => "string"
                     *  "message" => "json"
                     *  "message_id" => 1
                     * ]
                     */
                    if (isset($buffer['message']) && $buffer['message']) {
                        if ($buffer['topic'] == 'serve/enter') {
                            $data = json_decode($buffer['message'], true);
                            event(new CarEnterEvent($data['l'], $data['did']));
                        } else {

                        }
                        $this->info('接收到消息' . PHP_EOL);
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
                        $this->error('Broker is disconnected' . PHP_EOL);
                        $client->close();
                        break;
                    }
                    $timeSincePing = time();
                }
                if ($timeSincePing <= (time() - $client->getConfig()->getKeepAlive())) {
                    $buffer = $client->ping();
                    if ($buffer) {
                        $this->info('send ping success' . PHP_EOL);
                        $timeSincePing = time();
                    }
                }
            }
        });
    }
}
