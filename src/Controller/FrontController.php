<?php

namespace App\Controller;

use App\Services\Constants\DemoFrontText;
use App\Services\TryFormValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function try(Request $request, TryFormValidator $tryFormValidator): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $keyword = $request->request->get('keyword');
            $errors = $tryFormValidator->validate($request);
            if (count($errors) > 0) {
                return $this->render('front/try.html.twig', [
                    'status' => false,
                    'title' => $title,
                    'keyword' => $keyword,
                    'tryErrors' => $errors,
                ]);
            }

            $text = [];
            for ($i = 0; $i <= 2; $i++) {
                $testArr = explode(' ', DemoFrontText::getText()[$i]);
                $randPoz = array_rand($testArr);
                // array_merge(array_slice($a, 0, 2), [5], array_slice($a, 2, 2)
                $text[] = implode(' ', array_merge(array_slice($testArr, 0, $randPoz),
                    ['<strong>' . $keyword . '</strong>'],
                    array_slice($testArr, $randPoz, (count($testArr) - 1)))
                );
            }

            $this->addFlash('flash_message', '');
            return $this->render('front/try.html.twig', [
                'status' => true,
                'title' => $title,
                'keyword' => $keyword,
                'text' => $text,
                'tryErrors' => false,
            ]);
        }
        return $this->render('front/try.html.twig', [
            'status' => false,
            'title' => false,
            'keyword' => false,
            'tryErrors' => false,
        ]);
    }

}
