<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Services\ArticleCreatePeriodControl;
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
        ArticleRepository $articleRepository,
        Request $request,
        LicenseLevelControl $licenseLevelControl,
        ArticleCreatePeriodControl $articleCreatePeriodControl
    ): Response
    {


        /** @var User $authUser */
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);
        $isBlocked = $articleCreatePeriodControl->checkBlock($authUser, $licenseInfo);

        // Формируем форму
        $defaults = null;
        $content = null;
        if ($id) {
            $article = $articleRepository->find($id);
            $defaults = [
                'title' => $article->getTitle(),
                'key_word' => $article->getKeyWord(),
                'key_word_dist' => $article->getKeyWordDist(),
                'key_word_many' => $article->getKeyWordMany(),
                'min_size' => $article->getMinSize(),
                'max_size' => $article->getMaxSize(),
            ];
            $content = $article->getContent();
        }

        $formArt = $this->createFormBuilder($defaults)
            // ->add('theme', FileType::class)
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
            // ->add('images', FileType::class)
            ->getForm();

        $formArt->handleRequest($request);

        if ($formArt->isSubmitted() && $formArt->isValid()) {
            if ($id) {
                $newId = $articleRepository->update($formArt->getData());
            } else {
                $newId = $articleRepository->create($formArt->getData());
            }
            return $this->redirectToRoute('create_article', ['id' => $newId]);
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'licenseInfo' => $licenseInfo,
            'formArt' => $formArt,
            'content' => $content,
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
