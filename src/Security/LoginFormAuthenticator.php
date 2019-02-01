<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use App\Repository\UserRepository;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $userRepository;
    private $router;

    public function __construct( UserRepository $userRepository, RouterInterface $router){

        $this->userRepository = $userRepository;
        $this->router = $router;
    }
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        //dd($request->request->all()) ;
        // dd($request->request->all()) ; <=> dd($_POST)
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']

        );
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        //dd($credentials);
        // dd($credentials) recupere les valeurs du getCredentials
        return $this->userRepository->findOneBy(['email'=> $credentials['email']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // dd($user); affiche les information sur l' user
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // if (checkCredentials() return true => dd('Succes ! ')
        return new RedirectResponse($this->router->generate('home'));

    }

    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

  
}
