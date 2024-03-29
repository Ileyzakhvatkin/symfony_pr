<?php

namespace App\Services;

use App\Entity\Article;
use App\Entity\Image;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use function App\Controller\getKeyword;

class ArticleSaver
{
    private EntityManagerInterface $em;
    private FileUploader $fileUploader;
    private ArticleRepository $articleRepository;
    private ArticleTextGenerator $articleTextGenerator;
    private ImageRepository $imageRepository;

    public function __construct(
        EntityManagerInterface $em,
        FileUploader           $fileUploader,
        ArticleTextGenerator   $articleTextGenerator,
        ArticleRepository      $articleRepository,
        ImageRepository        $imageRepository,
    )
    {
        $this->em = $em;
        $this->fileUploader = $fileUploader;
        $this->articleRepository = $articleRepository;
        $this->articleTextGenerator = $articleTextGenerator;
        $this->imageRepository = $imageRepository;
    }

    public function save($formArt, $authUser, $id = null)
    {
        /** @var Article $newArticle */
        $newArticle = $formArt->getData();
        $newArticle
            ->setUser($authUser)
            ->setKeyword([
                '0' => $formArt->get('keyword0')->getData(),
                '1' => $this->getKeyword('keyword1', $formArt),
                '2' => $this->getKeyword('keyword2', $formArt),
                '3' => $this->getKeyword('keyword3', $formArt),
                '4' => $this->getKeyword('keyword4', $formArt),
                '5' => $this->getKeyword('keyword5', $formArt),
                '6' => $this->getKeyword('keyword6', $formArt),
            ])
            ->setUpdatedAt(Carbon::now());

        if ($formArt->get('title')->getData() === null) {
            $newArticle->setTitle($formArt->get('theme')->getData() . ' - ' . $formArt->get('keyword0')->getData());
        }

        $newId = $id;
        if (!isset($id)) $newArticle->setCreatedAt(Carbon::now());

        if ($formArt->has('words')) {
            foreach ($formArt->get('words')->getData() as $word) {
                $newArticle->addWord($word);
            }
        }

        if ($formArt->has('images')) {
            $images = $formArt->get('images')->getData();
            $maxImage = count($images);
            if ($id) $maxImage = count($images) + $this->imageRepository->countImages($id)[0]['1'];
            if (count($images) > 0) {
                foreach ($images as $img) {
                    if ($maxImage < 6) {
                        $image = new Image();
                        $image
                            ->setImgUrl($this->fileUploader->uploadFile($img))
                            ->setImage($img)
                            ->setArticle($newArticle);
                        $this->em->persist($image);
                    }
                }
            }
        }

        $newArticle->setContent($this->articleTextGenerator->createText($newArticle));

        $this->em->persist($newArticle);
        $this->em->flush();

        if (!isset($id)) {
            $newId = $this->articleRepository->getLastArticle($authUser->getId())[0]->getId();
        }

        return $newId;
    }

    public function getKeyword(string $word, $formArt)
    {
        return $formArt->has($word) ? $formArt->get($word)->getData() : $formArt->get('keyword0')->getData();
    }
}