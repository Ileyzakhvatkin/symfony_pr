<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Image;
use App\Entity\Word;
use App\Repository\ModuleRepository;
use App\Services\ArticleCreatePeriodController;
use App\Services\ArticleTextGenerator;
use App\Services\Constants\DemoThemes;
use App\Services\FileUploader;
use App\Services\LicenseLevelController;
use App\Services\PlaceholdersCreator;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

#[IsGranted('ROLE_USER')]
//#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ArticleController extends AbstractController
{
    #[Route('/api/article_create', name: 'app_api_article_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ModuleRepository $moduleRepository,
        ValidatorInterface $validator,
        ArticleTextGenerator $articleTextGenerator,
        FileUploader $fileUploader,
        ArticleCreatePeriodController $articleCreatePeriodController,
        LicenseLevelController $licenseLevelController,
        PlaceholdersCreator $placeholdersCreator,
        Environment $twig,
    ): JsonResponse
    {
        $authUser = $this->getUser();
        $errors = [];
        if ($articleCreatePeriodController->checkBlock($authUser, $licenseLevelController->update($authUser))) {
            return $this->json([
                'errors' => 'В рамках вашего тарифа, можно создавать не более 2 статей в час',
            ]);
        }

        $req = json_decode($request->getContent(), true);


        $article = new Article();
        $article
            ->setUser($authUser)
            ->setTheme($req['theme'])
            ->setTitle($req['title'])
            ->setModule($moduleRepository->find(3))
            ->setSize($req['size'])
            ->setKeyword([
                '0' => $req['keyword'][0],
                '1' => $this->getKeyword($req['keyword'], 1),
                '2' => $this->getKeyword($req['keyword'], 2),
                '3' => $this->getKeyword($req['keyword'], 3),
                '4' => $this->getKeyword($req['keyword'], 4),
                '5' => $this->getKeyword($req['keyword'], 5),
                '6' => $this->getKeyword($req['keyword'], 6),
            ])
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now())
        ;
        if ($req['words'] && count($req['words']) > 0) {
            foreach ($req['words'] as $word) {
                $newWord = new Word();
                $newWord
                    ->setTitle($word['word'])
                    ->setCount($word['count'])
                    ->setArticle($article)
                ;
                $errWord = $validator->validate($newWord);
                if (count($errWord) > 0) {
                    $errors[] = $errWord;
                }
                $em->persist($article);
            }
        }
        if ($req['images'] && count($req['images']) > 0) {
            foreach ($req['images'] as $key => $img) {
                if($key < 6) {
//                    $image = new Image();
//                    $image
//                        ->setImgUrl($this->fileUploader->uploadFileUrl($img))
//                        ->setImage('/tmp/phpOOzm2z')
//                        ->setArticle($article);
//                    $errImage = $validator->validate($image);
//                    if (count($errImage) > 0) {
//                        $errors[] = $errImage;
//                    }
//                    $em->persist($image);
                }
            }
        }

        if (!in_array($req['theme'], DemoThemes::getThemes())) {
            $errors[] = 'Указанной темы нет в сервисе';
        }
        $errArticle = $validator->validate($article);
        if (count($errArticle) > 0) {
            $errors[] = $errArticle;
        }

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
            ]);
        }

        $article->setContent($articleTextGenerator->createText($article));

        $em->persist($article);
        $em->flush();

        $content = $twig->render($moduleRepository->find(3)->getTwig(), $placeholdersCreator->create($article));

        return $this->json([
            'title' => $article->getTitle(),
            'description' => 'Статья о ' . $article->getKeyword()[5],
            'content' => $content,
        ]);

    }

     public function getKeyword(array $arr, $key): string
    {
        return $arr[$key] ? $arr[$key] : $arr[0];
    }

}
