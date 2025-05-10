<?php

return [
    /*
    |-------------------------------------
    | Messenger display name
    |-------------------------------------
    */
    'name' => 'Live chat',

    /*
    |-------------------------------------
    | The disk on which to store added
    | files and derived images by default.
    |-------------------------------------
    */
    'storage_disk_name' => 'chat',

    /*
    |-------------------------------------
    | Routes configurations
    |-------------------------------------
    */
    'routes'     => [
        'prefix'     => "messages",
        'middleware' => ['web','auth'],
        'namespace'  => "Adfox\LiveChat\Http\Controllers\Chat",
    ],
    'api_routes' => [
        'prefix'     => "messages/api",
        'middleware' => ['api'],
        'namespace'  => "Adfox\LiveChat\Http\Controllers\Chat\Api",
    ],


    /*
    |-------------------------------------
    | Pusher API credentials
    |-------------------------------------
    */
    'pusher' => [
        'debug' => env('APP_DEBUG', false),
        'key'     => '68bb4c49c690ce23cdf3',
        'secret'  => '645a098a35db3cd1121f',
        'app_id'  => '1747741',
        'options' => [
            'cluster'   => 'ap2',
            'encrypted' => true,
        ],
    ],

    /*
    |-------------------------------------
    | User Avatar
    |-------------------------------------
    */
    'user_avatar' => [
        'folder' => 'users-avatar',
        'default' => 'avatar.png',
    ],

    /*
    |-------------------------------------
    | Gravatar
    |
    | imageset property options:
    | [ 404 | mp | identicon (default) | monsterid | wavatar ]
    |-------------------------------------
    */
    'gravatar' => [
        'enabled'    => false,
        'image_size' => 200,
        'imageset'   => 'identicon'
    ],

    /*
    |-------------------------------------
    | Attachments
    |-------------------------------------
    */
    'attachments' => [
        'folder'              => 'attachments',
        'download_route_name' => 'attachments.download',
        'allowed_images'      => (array) ['png','jpg','jpeg','gif'],
        'allowed_files'       => (array) ['zip','rar','txt', 'pdf', 'mp3', 'mp4'],
        'max_upload_size'     => 10, // MB
    ],

    /*
    |-------------------------------------
    | Messenger's colors
    |-------------------------------------
    */
    'colors' => (array) [
        '#2180f3',
        '#2196F3',
        '#00BCD4',
        '#3F51B5',
        '#673AB7',
        '#4CAF50',
        '#FFC107',
        '#FF9800',
        '#ff2522',
        '#9C27B0',
    ],

    /*
    |-------------------------------------
    | Sounds
    | You can enable/disable the sounds and
    | change sound's name/path placed at
    | `public/` directory of your app.
    |
    |-------------------------------------
    */
    'sounds' => [
        'enabled'     => true,
        'public_path' => 'js/chatify/sounds',
        'new_message' => 'new-message-sound.mp3',
    ]
];
