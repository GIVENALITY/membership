<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Backend
    |--------------------------------------------------------------------------
    |
    | This option controls the default backend that will be used by the QR
    | code generator. You may set this to any of the backends defined in
    | the "backends" array below.
    |
    | Supported: "gd", "imagick"
    |
    */

    'backend' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | QR Code Backends
    |--------------------------------------------------------------------------
    |
    | Here you may configure the backends that will be used by the QR code
    | generator. You may add additional backends as needed.
    |
    */

    'backends' => [
        'gd' => [
            'class' => \BaconQrCode\Renderer\Image\GdImageBackEnd::class,
        ],
        'imagick' => [
            'class' => \BaconQrCode\Renderer\Image\ImagickImageBackEnd::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | This option controls the default size of the QR code.
    |
    */

    'size' => 300,

    /*
    |--------------------------------------------------------------------------
    | QR Code Margin
    |--------------------------------------------------------------------------
    |
    | This option controls the default margin around the QR code.
    |
    */

    'margin' => 10,

    /*
    |--------------------------------------------------------------------------
    | QR Code Error Correction
    |--------------------------------------------------------------------------
    |
    | This option controls the default error correction level.
    |
    | Supported: "L", "M", "Q", "H"
    |
    */

    'error_correction' => 'H',
];
