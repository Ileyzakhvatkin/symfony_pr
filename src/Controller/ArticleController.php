<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ArticleController extends AbstractController
{
    #[Route('/dashboard-history/', name: 'history')]
    public function articlesList(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->articleList($this->getUser()->getId());

        return $this->render('dashboard/history.html.twig', [
            'itemActive' => 3,
            'articles' => $articles,
        ]);
    }

    #[Route('/dashboard-create-article/{id}', name: 'create_article', defaults: ["id" => null])]
    public function formCreateArticle(
        $id,
        ArticleRepository $articleRepository,
        LicenseLevelControl $licenseLevelControl,
        Request $request
    ): Response
    {
        $licenseInfo = $licenseLevelControl->update($this->getUser());
        // Проверяем ограничения на генерацию статей
        $isBlocked = false;
        /** @var User $authUser */
        $authUser = $this->getUser();
        $rolesArr = $authUser->getRoles();
        if ($licenseInfo['type'] == 'PRO') {
            $isBlocked = true;
        } else {
            $parameters = [
                'val' => $authUser->getId(),
                'date' => (new Carbon('-2 hours'))->toDateString(),
            ];
            if ($articleRepository->getArticleCountFromPeriod($parameters)[0]['1'] >= 2) {
                $isBlocked = true;
            }
        }

        $form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('title', TextType::class)
            ->add('key_word', TextType::class)
            ->add('key_word_dist', TextType::class)
            ->add('key_word_many', TextType::class)
            ->add('min_size', NumberType::class)
            ->add('max_size', NumberType::class)
            ->add('word_1', TextType::class)
            ->add('word_count_1', NumberType::class)
            ->add('word_2', TextType::class)
            ->add('word_count_2', NumberType::class)
//            ->add('images', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->json($data);
        }

        if (isset($id)) {
            dd('загружаем данные статьи');
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'licenseInfo' => $licenseInfo,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard-article-detail/{id}', name: 'article_detail')]
    #[IsGranted('MANAGE', subject: 'article')]
    public function showArticle(Article $article): Response
    {
        return $this->render('dashboard/article_detail.html.twig', [
            'itemActive' => 3,
            'article' => $article,
        ]);
    }
}
