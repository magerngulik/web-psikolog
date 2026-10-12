<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ed25519 Cryptographic Keys for Offline Licensing
    |--------------------------------------------------------------------------
    |
    | The public key is embedded in all distributed builds to verify license
    | signatures. The private key must ONLY be present on the developer's machine
    | to generate licenses, and is stripped from client production builds.
    |
    */
    'public_key' => env('APP_LICENSE_PUBLIC_KEY', 'ySaV3yL8arlf0jOhm87UPbMs6hU2yxRDi5tHw0BWEII='),

    'private_key' => env('APP_LICENSE_PRIVATE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Support & Activation Contact
    |--------------------------------------------------------------------------
    |
    | The developer's WhatsApp number used in the QR Code and quick activation
    | links on the client's activation screen.
    |
    */
    'whatsapp_number' => env('LICENSE_SUPPORT_WHATSAPP', '6281234567890'),

    /*
    |--------------------------------------------------------------------------
    | License Enforcement Toggle
    |--------------------------------------------------------------------------
    |
    | Set to false if you want to bypass licensing during testing or development.
    |
    */
    'enabled' => env('APP_LICENSE_ENABLED', true),
];

