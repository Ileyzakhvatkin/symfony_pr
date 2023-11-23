<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\ModuleRepository;
use App\Services\ArticleCreatePeriodControl;
use App\Services\ArticlePlaceholders;
use App\Services\ArticleSaver;
use App\Services\LicenseLevelControl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;

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

    #[Route('/dashboard-article-detail/{id}', name: 'article_detail')]
    #[IsGranted('MANAGE', subject: 'article')]
    public function showArticle(
        Article $article,
        Environment $twig,
        ArticlePlaceholders $articlePlaceholders,
        ModuleRepository $moduleRepository,
    ): Response
    {
        $twigNull = $moduleRepository->find(1)->getTwig();
        if ($article->getModule()) {
            $twigNull = $article->getModule()->getTwig();
        }

        return $this->render('dashboard/article_detail.html.twig', [
            'itemActive' => 3,
            'id' => $article->getId(),
            'tmpl' => $twig->render($twigNull, $articlePlaceholders->create($article)),
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
        ArticleSaver $articleSaver,
        ArticlePlaceholders $articlePlaceholders,
        Environment $twig,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);
        $isBlocked = $articleCreatePeriodControl->checkBlock($authUser, $licenseInfo);

        // Берем статью Через ID, чтобы работала пустая форма
        /** @var Article $article */
        $article = $id ? $articleRepository->find($id) : null;

        $formArt = $this->createForm(ArticleFormType::class, $article);
        $formArt->handleRequest($request);

        if ($formArt->isSubmitted() && $formArt->isValid()) {
            $newId = $articleSaver->save($formArt, $authUser, $id);


            return $this->redirectToRoute('create_article', ['id' => $newId]);
        }

        $twigNull = null;
        if ($article) {
            if ($article->getModule()) {
                $twigNull = $twig->render($article->getModule()->getTwig(), $articlePlaceholders->create($article));
            } else {
                $twigNull = $twig->render($moduleRepository->find(1)->getTwig(), $articlePlaceholders->create($article));
            }
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'formArt' => $formArt->createView(),
            'article' => $article,
            'availableWords' => true,
            'tmpl' => $twigNull,
        ]);
    }


}
