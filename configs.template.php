<?php

$configs = [];

$configs["servers"] = [
    "{env_name}" => [
        '{host}' => [
            'driver' => '{pgsql|server}',
            'username'  => '{username}',
            'pass' => '{password}',
            'databases' => [
                'DB label' => '{dbname}',
            ]
        ],
    ]
];

return $configs;
