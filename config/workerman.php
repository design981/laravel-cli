<?php
return [
    'protocol'=>  "http",
    'count'=> 4,
    'ip' => '0.0.0.0',
    'port' => '8880',
    'path' => storage_path('workerman') . '/',
    'pid'=> 'WORKERMAN_%SERVER%_%IP%',
];
