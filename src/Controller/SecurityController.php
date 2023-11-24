<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\DemoModuleSaver;
use App\Services\Mailer;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
        Mailer $mailer,
        DemoModuleSaver $demoModuleSaver,
        ValidatorInterface $validator
    )
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $regLink = sha1(uniqid('reg-link'));
            $user
                ->setName($request->request->get('name'))
                ->setEmail($request->request->get('email'))
                ->setRoles([])
                ->setPassword1($request->request->get('password1'))
                ->setPassword2($request->request->get('password2'))
                ->setPassword($passwordHasher->hashPassword($user, $request->request->get('password1')))
                ->setRegLink($regLink)
                ->setCreatedAt(Carbon::now())
                ->setUpdatedAt(Carbon::now())
            ;

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->render('security/register.html.twig', [
                    'errors' => $errors,
                    'userName' => $request->request->get('name'),
                    'userEmail' => $request->request->get('email'),
                ]);
            }

            $em->persist($user);
            $token = new ApiToken($user);
            $em->persist($token);
            $em->flush();
            $demoModuleSaver->create($user);

            // В зависимости от ENV включаем/выключаем авторизацию с подтверждением через email
            if ($this->getParameter('user_reg_check_email') == 0) {
                $userAuthenticator->authenticateUser($user, $authenticator, $request);

                return $this->redirectToRoute('dashboard');
            } else {
                $mailer->sendCheckRegistration($user, $regLink);

                return $this->redirectToRoute('app_check_reg');
            }
        }

        return $this->render('security/register.html.twig', [
            'errors' => false,
            'userName' => false,
            'userEmail' => false,
        ]);
    }

    #[Route('/check/{link}', name: 'app_check_reg', defaults: ["link" => null])]
    public function check(
        $link,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
    ) {
        if ($link) {
            /** @var User $user */
            $user = $userRepository->getUserByLink($link)[0];
            if ($user instanceof User) {
                $user->setRoles(['ROLE_USER']);
                $em->persist($user);
                $em->flush();
                $userAuthenticator->authenticateUser($user, $authenticator, $request);
            }
        }

        return $this->render('security/check.html.twig', [
            'regLink' => $link,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {

    }
}
