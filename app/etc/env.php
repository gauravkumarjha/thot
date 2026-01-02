<?php
return [
    'system' => [
        'default' => [
            'dev' => [
                'debug' => [
                    'template_hints_storefront' => 1
                ]
            ]
        ]
    ],
    'backend' => [
        'frontName' => 'admin'
    ],
    'install' => [
        'date' => 'Mon, 10 Oct 2022 09:19:02 +0000'
    ],
    'crypt' => [
        'key' => 'xEc5bj9IvPdJNQF766jFR9mJ59szDJzr'
    ],
    'session' => [
        'save' => 'files'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'thotpocthel7tfestageccs46',
                'username' => 'thotpocthel7tfestageccs46',
                'password' => 'cZbXa91wKgy7nVPs#',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'cache_types' => [
        'config' => 0,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 0,
        'reflection' => 0,
        'db_ddl' => 0,
        'eav' => 0,
        'config_integration' => 0,
        'config_integration_api' => 0,
        'full_page' => 0,
        'translate' => 0,
        'config_webservice' => 0,
        'compiled_config' => 1,
        'customer_notification' => 0
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'nqjVFASOS5yjaS9rlRlu7Iy35l2nULJ7'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => 'yqfdjbbekn_'
            ],
            'page_cache' => [
                'id_prefix' => 'yqfdjbbekn_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'http_cache_hosts' => [
        [
            'host' => 'stage.thehouseofthings.com',
            'port' => '8080'
        ],
        [
            'host' => '127.0.0.1',
            'port' => '8080'
        ]
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ]
];
