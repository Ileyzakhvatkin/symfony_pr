<?php

namespace App\Controller;

use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите заголовок статьи'
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Не менее 12 символов',
                        'max' => 30,
                        'maxMessage' => 'Не более 30 символов',
                    ])
                ]
            ])
            ->add('keyword', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите ключевое слово для статьи'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Не менее 4 символов',
                        'max' => 15,
                        'maxMessage' => 'Не более 15 символов',
                    ])
                ]
            ])
            ->getForm();

        $formFront->handleRequest($request);

        if ($formFront->isSubmitted() && $formFront->isValid()) {

            $text = [];
            $lorem = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex impedit odit harum adipisci? Praesentium vitae quam perspiciatis modi possimus. Repudiandae modi nesciunt officia aliquam odit beatae quam illo adipisci cum.';

            for ($i = 1; $i <= rand(6,10); $i++) {
                $loremArr = explode(' ', $lorem);
                $randPoz = array_rand($loremArr);
                // array_merge(array_slice($a, 0, 2), [5], array_slice($a, 2, 2)
                $text[] = implode(' ', array_merge(array_slice($loremArr, 0, $randPoz), [$formFront->get('keyword')->getData()], array_slice($loremArr, $randPoz, $randPoz)));
            }

            $this->addFlash('flash_message', '');
            return $this->render('front/try.html.twig', [
                'formFront' => $formFront,
                'createArticle' => true,
                'testArticle' => [
                    'title' => $formFront->get('title')->getData(),
                    'text' => implode(' ', $text),
                ]
            ]);
        }
        return $this->render('front/try.html.twig', [
            'formFront' => $formFront,
            'createArticle' => false,
        ]);
    }

}
