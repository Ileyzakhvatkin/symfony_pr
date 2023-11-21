<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Module;
use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\Constants\DemoModules;
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
        Mailer $mailer,
    )
    {
        $formReg = $this->createForm(UserRegistrationFormType::class);
        $formReg->handleRequest($request);

        if ($formReg->isSubmitted() && $formReg->isValid()) {
            /** @var User $user */
            $user = $formReg->getData();
            $regLink = sha1(uniqid('reg-link'));

            $user
                ->setRoles([])
                ->setPassword($passwordHasher->hashPassword($user, $user->getPassword1()))
                ->setRegLink($regLink)
                ->setCreatedAt(Carbon::now())
                ->setUpdatedAt(Carbon::now())
            ;
            $em->persist($user);

            $token = new ApiToken($user);
            $em->persist($token);

            foreach (DemoModules::getModules() as $key=>$el) {
                $module = (new Module())
                    ->setTitle(DemoModules::getModules()[$key]['title'])
                    ->setUser($user)
                    ->setCode(DemoModules::getModules()[$key]['code'])
                    ->setCommon(true)
                    ->setTwig(DemoModules::getModules()[$key]['file'])
                    ->setCreatedAt(Carbon::now())
                    ->setUpdatedAt(Carbon::now())
                ;
                $em->persist($module);
            }

            $em->flush();

            // авторизация после регистрации (если сервер не отправляет email) или авторизация с проверкой email
             $userAuthenticator->authenticateUser($user, $authenticator, $request);
            // $mailer->sendCheckRegistration($user, $regLink);

            // Переходим или в личный кабинет или на страницу подтверждения mail
            return $this->redirectToRoute('dashboard');
            // return $this->redirectToRoute('app_check_reg');
        }

        return $this->render('security/register.html.twig', [
            'formReg' => $formReg->createView(),
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
            'link' => $link,
        ]);
    }
}
