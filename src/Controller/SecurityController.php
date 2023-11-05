<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
    )
    {
        if ($request->isMethod('POST')) {
            $user = new User();

            $user
                ->setEmail($request->request->get('email'))
                ->setName($request->request->get('name'))
                ->setRoles(['ROLE_USER'])
                ->setPassword($passwordHasher->hashPassword($user, $request->request->get('password')))
                ->setCreatedAt(Carbon::now())
                ->setUpdatedAt(Carbon::now())
            ;

            $em->persist($user);
            $em->flush();

            // авторизация после регистрации
            $userAuthenticator->authenticateUser($user, $authenticator, $request);

            return $this->redirectToRoute('dashboard');
        }
        return $this->render('security/register.html.twig', [
            'error' => '',
        ]);
    }


}
