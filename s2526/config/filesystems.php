<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'polls' => [
            'driver' => 'local',
            'root' => storage_path('app/public/polls'),
            'url' => env('APP_URL').'/storage/polls',
            'visibility' => 'public',
        ],

        'lessons' => [
            'driver' => 'local',
            'root' => storage_path('app/public/lessons'),
            'url' => env('APP_URL').'/storage/lessons',
            'visibility' => 'public',
        ],

        'social_accions' => [
            'driver' => 'local',
            'root' => storage_path('app/public/social_accions'),
            'url' => env('APP_URL').'/storage/social_accions',
            'visibility' => 'public',
        ],

        'enrollments' => [
            'driver' => 'local',
            'root' => storage_path('app/public/enrollments'),
            'url' => env('APP_URL').'/storage/enrollments',
            'visibility' => 'public',
        ],

        'payment' => [
            'driver' => 'local',
            'root' => storage_path('app/public/payment'),
            'url' => env('APP_URL').'/storage/payment',
            'visibility' => 'public',
        ],

        'interviews' => [
            'driver' => 'local',
            'root' => storage_path('app/public/interviews'),
            'url' => env('APP_URL').'/storage/interviews',
            'visibility' => 'public',
        ],

        'posts' => [
            'driver' => 'local',
            'root' => storage_path('app/public/posts'),
            'url' => env('APP_URL').'/storage/posts',
            'visibility' => 'public',
        ],

        'educationals' => [
            'driver' => 'local',
            'root' => storage_path('app/public/educationals'),
            'url' => env('APP_URL').'/storage/educationals',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],

];
