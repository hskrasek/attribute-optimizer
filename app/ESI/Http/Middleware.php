<?php

declare(strict_types=1);

namespace App\ESI\Http;

use App\ESI\Auth\Token;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;

final class Middleware
{
    /**
     * @phpstan-return  callable<RequestInterface>
     */
    public static function refreshToken(Token $token): callable
    {
        return static function(callable $handler) use ($token): callable {
            return static function (RequestInterface $request, array $options) use ($handler, $token) {
                if (!$token->isValid()) {
                    $clientId = config('esi.auth.client_id');
                    $secret = config('esi.auth.secret_key');

                    $response = Http::withOptions([
                        'headers' => [
                            'Authorization' => 'Basic ' . base64_encode("{$clientId}:{$secret}"),
                        ],
                        'base_uri' => 'https://login.eveonline.com/v2/oauth/token/',
                    ])->asForm()->post('', [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $token->refreshToken,
                    ]);

                    $token = $token->refresh($response->body());
                }

                return $handler($request->withHeader('Authorization', 'Bearer ' . $token->accessToken), $options);
            };
        };
    }
}
