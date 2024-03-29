<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Filesystem\Filesystem;

#[IsGranted('ROLE_USER')]
class ImageController extends AbstractController
{
    #[Route('/delete-image/{id}/{articleId}', name: 'delete-image', methods: ['POST'])]
    public function deleteImage(
        $id, $articleId,
        ImageRepository $imageRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $em,
        Filesystem $filesystem,
    ): JsonResponse
    {
        /** @var Image $image */
        $image = $imageRepository->find($id);
        $article = $articleRepository->find($articleId);

        if ($this->getUser()->getId() == $article->getUser()->getId()) {
            $filesystem->remove($this->getParameter('article_uploads_dir') . '/' . $image->getImgUrl());
            $em->remove($image);
            $em->flush();
            return $this->json([
                'image' => 'deleted',
            ]);
        }

        return $this->json(['image' => 'not-deleted']);
    }
}
