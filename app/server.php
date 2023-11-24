<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ESI\Auth\Token;
use Crell\Serde\SerdeCommon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/** @var \LaravelZero\Framework\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)
    ->bootstrap();

$request = Request::capture();

$clientId = config('esi.auth.client_id');
$secret = config('esi.auth.secret_key');

$response = Http::withOptions([
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode("{$clientId}:{$secret}"),
    ],
    'base_uri' => 'https://login.eveonline.com/v2/oauth/token/',
])->asForm()->post('', [
    'grant_type' => 'authorization_code',
    'code' => $request->input('code'),
]);

/** @var SerdeCommon $serde */
$serde = $app->make('serde');

$token = $serde->deserialize($response->body(), from: 'json', to: Token::class);

Storage::put('token.json', $serde->serialize($token, format: 'json'));

$response = new Response(
    <<<'HTML'
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Authorization</title>
            </head>
            <body>
                <h1>Authorization</h1>
                <p>Authorization successful. You may now close this window.</p>
                <script type="application/javascript">
                    window.open('', '_self', '');
                    window.close();
                </script>
            </body>
        </html>
    HTML,
    200
);

$response->send();

$app->terminate();
