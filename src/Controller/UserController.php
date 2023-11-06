<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/dashboard-profile/', name: 'profile')]
    public function profile(Request $request, UserRepository $userRepository): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $defaults = [
            'name' => $authUser->getName(),
            'email' => $authUser->getEmail(),
        ];
        $formProfile = $this->createFormBuilder($defaults)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('password2', PasswordType::class)
            ->getForm();

        $formProfile->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $userRepository->update($authUser, $formProfile->getData());
            $this->addFlash('flash_message', 'Данные пользователя обновлены');

            return $this->redirectToRoute('profile');
        }

        return $this->render('dashboard/profile.html.twig', [
            'itemActive' => 5,
            'user' => $this->getUser(),
            'formProfile' => $formProfile,
        ]);
    }

//    #[Route('/dashboard-token-update/', name: 'token_update', methods: ['PATCH'])]
//    public function tokenUpdate(): JsonResponse
//    {
//        return $this->json(json_encode(['token' => 'updated']));
//    }


}
