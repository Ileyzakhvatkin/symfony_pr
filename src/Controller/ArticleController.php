<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\ModuleRepository;
use App\Services\ArticleCreatePeriodControl;
use App\Services\LicenseLevelControl;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

#[IsGranted('ROLE_USER')]
class ArticleController extends AbstractController
{
    #[Route('/dashboard-history/', name: 'history')]
    public function articlesList(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $pagination = $paginator->paginate(
            $articleRepository->articleListQuery($this->getUser()->getId()),
            $request->query->getInt('page', 1), /*page number*/
            10
        );

        return $this->render('dashboard/history.html.twig', [
            'itemActive' => 3,
            'articles' => $pagination,
            'itemNumberPerPage' => $pagination->getItemNumberPerPage()
        ]);
    }

    #[Route('/dashboard-create-article/{id}', name: 'create_article', defaults: ["id" => null])]
    public function formCreateArticle(
        $id,
        Request $request,
        ArticleRepository $articleRepository,
        ModuleRepository $moduleRepository,
        LicenseLevelControl $licenseLevelControl,
        ArticleCreatePeriodControl $articleCreatePeriodControl,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);
        $isBlocked = $articleCreatePeriodControl->checkBlock($authUser, $licenseInfo);

        // Формируем форму
        $defaults = null;
        $article = null;
        if ($id) {
            $article = $articleRepository->find($id);
            $defaults = [
                'title' => $article->getTitle(),
                'keyword' => $article->getKeyword(),
                'keyword_dist' => $article->getKeywordDist(),
                'keyword_many' => $article->getKeywordMany(),
                'size' => $article->getSize(),
                'maxsize' => $article->getMaxSize(),
            ];
        }

        $formArt = $this->createFormBuilder($defaults)
            ->add('title', TextType::class)
            ->add('theme', ChoiceType::class, [
                'choices'  => [
                    'Про НЕ здоровую еду' => 'FOOD',
                    'Про PHP и как с этим жить' => 'PHP',
                    'Про женщин и не только' => 'WOMEN',
                ],
                'placeholder' => 'Выберете тему',
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'title',
                'placeholder' => 'Выберете модуль',
                'choices' => $moduleRepository->listAuthUser($authUser->getId())
            ])
            ->add('keyword', TextType::class)
            ->add('keyword_dist', TextType::class)
            ->add('keyword_many', TextType::class)
            ->add('size', NumberType::class, ['attr' => ['maxlength' => 4]])
            ->add('maxsize', NumberType::class, ['attr' => ['maxlength' => 4]])
//        ;
//
//        if (isset($article) && count($article->getWords()) > 0) {
//            foreach ($article->getWords() as $key => $el) {
//                $formArt
//                    ->add('word_' . $key + 1, TextType::class)
//                    ->add('word_count_' . $key + 1, NumberType::class);
//            }
//        }
//        $formArt
            ->add('word_0', TextType::class)
            ->add('word_count_0', NumberType::class, ['attr' => ['maxlength' => 2]])
            ->getForm();

        $formArt->handleRequest($request);

        if ($formArt->isSubmitted() && $formArt->isValid()) {
            $newId = 1;
            // $newId = $articleRepository->update($formArt->getData());
            return $this->redirectToRoute('create_article', ['id' => $newId]);
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'licenseInfo' => $licenseInfo,
            'formArt' => $formArt,
            'article' => $article,
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
