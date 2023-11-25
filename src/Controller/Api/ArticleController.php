<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Image;
use App\Repository\ArticleRepository;
use App\Services\ArticleTextGenerator;
use App\Services\FileUploader;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_USER')]
//#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ArticleController extends AbstractController
{
    #[Route('/api/article/{id}', name: 'app_api_article', defaults: ["id" => null])]
    public function index($id, ArticleRepository $articleRepository): JsonResponse
    {
        if (!$id) {
            return $this->json([ 'error' => 'Sent article id' ]);
        }
        if (!$articleRepository->find($id) || $articleRepository->find($id)->getUser() !== $this->getUser()){
            return $this->json([ 'error' => 'There is no article with this id' ]);
        }
        $article = $articleRepository->getArticleByIdWithWordsImages($id)[0];

        return $this->json([
            'theme' => $article->getTheme(),
            'keyword' => $article->getKeyword(),
            'title' => $article->getTitle(),
            'size' => $article->getSize(),
            'words' => $this->getArticleWords($article),
            'images' => $this->getArticleImages($article),
        ]);

    }

    #[Route('/api/article_create', name: 'app_api_article_create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        FileUploader           $fileUploader,
        ArticleTextGenerator   $articleTextGenerator,
        ValidatorInterface $validator
    ): JsonResponse
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $article = new Article();

        $article
            ->setUser($authUser)
            ->setTheme($request->request->get('theme'))
            ->setSize($request->request->get('size'))
            ->setKeyword([
                '0' => $request->request->get('keyword0'),
                '1' => $this->getKeyword('keyword1', $request),
                '2' => $this->getKeyword('keyword2', $request),
                '3' => $this->getKeyword('keyword3', $request),
                '4' => $this->getKeyword('keyword4', $request),
                '5' => $this->getKeyword('keyword5', $request),
                '6' => $this->getKeyword('keyword6', $request),
            ])
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now())
        ;

        if (!$request->request->has('title') || $request->request->get('title') === null) {
            $article->setTitle($request->request->get('theme') . ' - ' . $request->request->get('keyword0'));
        }
        if ($request->request->has('words') && count($request->request->get('words')) > 0) {
            foreach ($request->request->get('words') as $word) {
                $article->addWord($word);
            }
        }
//        if ($request->request->has('images') && count($request->request->get('images')) > 0) {
//            foreach ($request->request->get('images') as $img) {
//                $image = new Image();
//                $image
//                    ->setImgUrl($fileUploader->uploadFile($img))
//                    ->setImage($img)
//                    ->setArticle($article);
//                $em->persist($image);
//            }
//        }

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
            ]);
        }

        $article->setContent($articleTextGenerator->createText($article));

        $em->persist($article);
        $em->flush();

        return $this->json([
            'theme' => $article->getTheme(),
            'keyword' => $article->getKeyword(),
            'title' => $article->getTitle(),
            'size' => $article->getSize(),
            'words' => $this->getArticleWords($article),
//            'images' => $this->getArticleImages($article),
        ]);
    }

    public function getKeyword(string $word, Request $request): string
    {
        return $request->request->get($word) ? $request->request->get($word) : $request->request->get('keyword0');
    }

    public function getArticleWords($article): array
    {
        $words = [];
        foreach ($article->getWords() as &$el) {
            $words[] = [
                'word' => $el->getTitle(),
                'count' => $el->getCount(),
            ];
        }

        return $words;
    }

    public function getArticleImages($article): array
    {
        $images = [];
        foreach ($article->getImages() as &$el) {
            $images[] = $el->getImgUrl();
        }

        return $images;
    }
}
