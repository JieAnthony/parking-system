<?php

return [
    'config' => [
        'host' => '172.17.0.2',
        'port' => 1883,
        'time_out' => 5,
        'user_name' => '',
        'password' => '',
        'client_id' => 'parking_42034de1',
        'keep_alive' => 60,
        'properties' => [
            'session_expiry_interval' => 0,
            'receive_maximum' => 200,
            'topic_alias_maximum' => 200,
        ],
        'protocol_level' => 5,
    ],
    'swooleConfig' => [
        'open_mqtt_protocol' => true,
        'package_max_length' => 2 * 1024 * 1024,
        'connect_timeout' => 1.0,
        'write_timeout' => 5.0,
        'read_timeout' => 0.5,
    ]
];
