<?php

declare(strict_types=1);

namespace App\ESI\Auth;

use Crell\Serde\Attributes as Serde;
use Crell\Serde\SerdeCommon;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class Token
{
    public function __construct(
        #[Serde\Field('access_token')]
        public string $accessToken,
        #[Serde\Field('token_type')]
        public string $tokenType,
        #[Serde\Field('expires_in')]
        public int    $expiresIn,
        #[Serde\Field('refresh_token')]
        public string $refreshToken,
    )
    {
    }

    public static function make(string $body): self
    {
        return app()->make('serde')->deserialize($body, from: 'json', to: self::class);
    }

    private function decodeToken(int $leeway = 0): \stdClass
    {
        JWT::$leeway = $leeway;

        $jwk = JWK::parseKeySet(Http::get('login.eveonline.com/oauth/jwks')
            ->json());

        return JWT::decode($this->accessToken, $jwk);
    }

    public function characterId(): string
    {
        return Str::of($this->decodeToken(leeway: 60 * 60 * 24 * 7)->sub)
            ->explode(':')
            ->last();
    }

    public function isValid(): bool
    {
        try {
            return $this->decodeToken()->exp > time();
        } catch (ExpiredException $e) {
            return false;
        }
    }

    public function refresh(string $body): self
    {
        return self::make($body);
    }

    public function save(): bool
    {
        /** @var SerdeCommon $serde */
        $serde = app()->make('serde');

        return Storage::put('token.json', $serde->serialize($this, format: 'json'));
    }
}
