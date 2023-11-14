<?php

$configs = [];

$configs["servers"] = [
    "{env_name}" => [
        '{host}' => [
            'username'  => '{username}',
            'pass' => '{password}',
            'databases' => [
                'DB label' => '{dbname}',
            ]
        ],
    ]
];

return $configs;
