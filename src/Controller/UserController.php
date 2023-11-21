<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Form\UserUpdateFormType;
use App\Repository\ApiTokenRepository;
use App\Services\Mailer;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/dashboard-profile/', name: 'profile')]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ApiTokenRepository $apiTokenRepository,
        Mailer $mailer,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();

        $formProfile = $this->createForm(UserUpdateFormType::class, $authUser);
        $formProfile->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $user = $formProfile->getData();

            $user
                ->setPassword($passwordHasher->hashPassword($user, $formProfile->get('password1')->getData()))
                ->setUpdatedAt(Carbon::now());
            $em->persist($user);
            $em->flush();
            $this->addFlash('flash_message', 'Данные пользователя обновлены');
            $mailer->sendUpdateProfile($authUser, $formProfile->get('password1')->getData());

            return $this->redirectToRoute('profile');
        }

        return $this->render('dashboard/profile.html.twig', [
            'itemActive' => 5,
            'user' => $authUser,
            'token' => $apiTokenRepository->getActualToken($authUser)[0]->getToken(),
            'formProfile' => $formProfile->createView(),
        ]);
    }

    #[Route('/dashboard-token-update/', name: 'token_update', methods: ['POST'])]
    public function tokenUpdate(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        if ($authUser) {
            $newToken = new ApiToken($authUser);
            $em->persist($newToken);
            $em->flush();

            return $this->json([
                'status' => 'updated',
                'token' => $newToken->getToken(),
            ]);
        }

        return $this->json(['status' => 'not-updated']);
    }


}
