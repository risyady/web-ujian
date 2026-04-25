return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'https://sintoga-learn.vercel.app',
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];