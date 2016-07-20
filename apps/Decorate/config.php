<?php

return [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'decorate',
    'username'  => 'root',
    'password'  => 'toor',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => '',
    'redis' => [
        'default' => [
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' => '',
            'database' => 2
        ]
    ],
    'qiniu' => [
        'accessKey' => 'r4exlUNIi7WU4w08tFNDc3YsPduwd-SBK3ypYsRn',
        'secretKey' => 'BUzJc7GQju4uWz38csR1JwludyVVA-eKRPrzDW-W',
        'bucket' => [
            'pri' => [
                
            ],
            'pub' => [
                'avatar' => 'http://o97tnvdt9.bkt.clouddn.com/',
                'decorate-pic' => 'http://o97u6hyxb.bkt.clouddn.com/',
                'decorate-style' => 'http://o97uykzik.bkt.clouddn.com/',
                'shop' => 'http://o97uykzik.bkt.clouddn.com/',
            ]
        ]
    ],
];


