<?php

declare(strict_types=1);

namespace App\ESI\Auth;

use Crell\Serde\Attributes as Serde;

final class Token
{
    #[Serde\Field('access_token')]
    public string $accessToken;

    #[Serde\Field('token_type')]
    public string $tokenType;

    #[Serde\Field('expires_in')]
    public int $expiresIn;

    #[Serde\Field('refresh_token')]
    public string $refreshToken;

    public function __construct(
        string $accessToken,
        string $tokenType,
        int $expiresIn,
        string $refreshToken,
    ) {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
    }
}
