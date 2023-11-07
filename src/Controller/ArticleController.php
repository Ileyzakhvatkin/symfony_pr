<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\User;
use App\Form\ArticleFormType;
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
        LicenseLevelControl $licenseLevelControl,
        ArticleCreatePeriodControl $articleCreatePeriodControl,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);
        $isBlocked = $articleCreatePeriodControl->checkBlock($authUser, $licenseInfo);

        // Берем статью Через ID, чтобы работала пустая форма
        $article = $id ? $articleRepository->find($id) : null;

        $formArt = $this->createForm(ArticleFormType::class, $article);
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
            'formArt' => $formArt->createView(),
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
