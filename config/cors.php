return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',        // dev React
        'https://sintoga-learn.vercel.app', // production
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];