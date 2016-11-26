<?php
namespace Apr\UserBundle\Security;

use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $userProvider;
    protected $httpUtils;

    public function __construct(ApiKeyUserProvider $userProvider, HttpUtils $httpUtils)
    {
        $this->userProvider = $userProvider;
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {
        //echo "Create Token";
        $requestFormat = $request->getRequestFormat();
        // look for an apikey query parameter
        //$apiKey = $request->query->get('apikey');
        // or if you want to use an "apikey" header, then do something like this:
        $apiKey = $request->headers->get('apikey');

        if (!$apiKey) {
            throw new ApiException(4010);
        }
        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        //echo "Authentification Token";
        $apiKey = $token->getCredentials();
        $row = $this->userProvider->getUsernameForApiKey($apiKey);

        if (isset($row['error'])) {
            throw new ApiException($row['error']);
        }

        // User is the Entity which represents your user
        $user = $token->getUser();
        if ($user instanceof User) {
            return new PreAuthenticatedToken(
                $user,
                $apiKey,
                $providerKey,
                $user->getRoles()
            );
        }

        $user = $this->userProvider->loadUserByUsername($row['username']);
        //$user = $this->userProvider->userManager->findUserByUsername($username);

        $preAuthenticatedToken = new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
        $preAuthenticatedToken->setAttribute('APIKeyProgramId', $row['APIKeyProgramId']);
        return $preAuthenticatedToken;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed.", 403);
    }
}

?>
