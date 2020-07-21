<?php

namespace App\Mercure;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JwtProvider
{
    private string $secretKey;

    /**
     * JwtProvider constructor.
     * @param string $secretKey
     */
    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function __invoke (): string
    {
        return (new Builder())
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken(
                new Sha256(),
                new Key($this->secretKey)
            )
        ;
    }

}
