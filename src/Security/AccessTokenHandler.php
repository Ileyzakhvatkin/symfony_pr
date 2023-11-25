<?php

namespace App\Security;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        // e.g. query the "access token" database to search for this token
        /** @var ApiToken $token */
        $token = $this->apiTokenRepository->findOneByValue($accessToken);
        if (null === $token || $token->isExpired()) {
            throw new BadCredentialsException('Неверные учетные данные.');
        }

        // and return a UserBadge object containing the user identifier from the found token
        return new UserBadge($token->getUser()->getId());
    }
}