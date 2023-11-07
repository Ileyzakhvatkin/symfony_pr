<?php

namespace App\Controller;

use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_homepage')]
    public function home(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/try/', name: 'front_try')]
    public function try(Request $request): Response
    {
        $formFront = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('key_word', TextType::class)
            ->getForm();

        $formFront->handleRequest($request);

        if ($formFront->isSubmitted() && $formFront->isValid()) {
            $this->addFlash('flash_message', 'Тестовая статья успешно создана');

            return $this->redirectToRoute('front_try');
        }
        return $this->render('front/try.html.twig', [
            'formFront' => $formFront,
        ]);
    }

}
