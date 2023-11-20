<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\WordRepository;
use App\Services\ArticleCreatePeriodControl;
use App\Services\ContentGenerator;
use App\Services\FileUploader;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        WordRepository $wordRepository,
        ArticleCreatePeriodControl $articleCreatePeriodControl,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        ContentGenerator $contentGenerator,
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
            /** @var Article $newArticle */
            $newArticle = $formArt->getData();

            $images = $formArt->get('images')->getData();
            if (count($images) > 0) {
                foreach ($images as $img) {
                    $image = new Image();
                    $image
                        ->setImgUrl($fileUploader->uploadFile($img))
                        ->setImage($img)
                        ->setArticle($newArticle);
                    $em->persist($image);
                }
            }

            function getKeyword(string $word, $formArt)
            {
                return  $formArt->get($word)->getData() ? $formArt->get($word)->getData() : $formArt->get('keyword0')->getData();
            }

            $newArticle
                ->setUser($authUser)
                ->setKeyword([
                    '0' => $formArt->get('keyword0')->getData(),
                    '1' => getKeyword('keyword1', $formArt),
                    '2' => getKeyword('keyword2', $formArt),
                    '3' => getKeyword('keyword3', $formArt),
                    '4' => getKeyword('keyword4', $formArt),
                    '5' => getKeyword('keyword5', $formArt),
                    '6' => getKeyword('keyword6', $formArt),
                ])
                ->setUpdatedAt(Carbon::now());

            if ($formArt->get('title')->getData() === null) {
                $newArticle->setTitle($formArt->get('theme')->getData() . ' - ' . $formArt->get('keyword0')->getData());
            }

            $newId = $id;
            if (!isset($id)) $newArticle->setCreatedAt(Carbon::now());

            $newArticle->setContent($contentGenerator->createText($newArticle));

            if ($formArt->has('words')) {
                foreach ($formArt->get('words')->getData() as $word) {
                    $newArticle->addWord($word);
                }
            }

            $em->persist($newArticle);
            $em->flush();

            if (!isset($id)) {
                $newId = $articleRepository->lastAarticle($authUser->getId())[0]->getId();
            }

            return $this->redirectToRoute('create_article', ['id' => $newId]);
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'formArt' => $formArt->createView(),
            'article' => $article,
            'availableWords' => true,
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
