<?php

namespace App\Service\Mercure;

use App\Entity\Users;
use DateInterval;
use DateTime;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

class MercureCookieGenerator
{
    private string $secretKey;

    /**
     * MercureCookieGenerator constructor.
     * @param string $secretKey
     */
    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }


    public function generate (Users $user)
    {
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $user->getUsername())]])
            ->getToken(
                new Sha256(),
                new Key($this->secretKey)
            )
        ;
        return new Cookie(
            "mercureAuthorization",
            $token,
            (new DateTime('now'))->add(new DateInterval('PT2H')),
            '/.well-known/mercure',
            'http://127.0.0.1:8000',
            false,
            true,
            false,
            'strict'
        );
    }
}
