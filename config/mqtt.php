<?php

return [
    'host' => env('MQTT_HOST'),
    'port' => (int)env('MQTT_PORT', 1883),
    'config' => [
        'userName' => env('MQTT_USERNAME'), // 用户名
        'password' => env('MQTT_PASSWORD'), // 密码
        'clientId' => env('MQTT_CLIENT_ID'), // 客户端id
        'keepAlive' => (int)env('MQTT_KEEP_ALIVE', 50), // 默认0秒，设置成0代表禁用
        'protocolName' => env('MQTT_PROTOCOL_NAME', 'MQTT'), // 协议名，默认为MQTT(3.1.1版本)，也可为MQIsdp(3.1版本)
        'protocolLevel' => (int)env('MQTT_PROTOCOL_LEVEL', 4), // 协议等级，MQTT3.1.1版本为4，5.0版本为5，MQIsdp为3
        'properties' => [], // MQTT5 中所需要的属性
        'delay' => (int)env('MQTT_DELAY', 3000), // 重连时的延迟时间 (毫秒)
        'maxAttempts' => (int)env('MQTT_MAX_ATTEMPTS', 5), // 最大重连次数。默认-1，表示不限制
        'swooleConfig' => []
    ]
];
