<?php

namespace App\Twig\Runtime;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class MorphRuntime implements RuntimeExtensionInterface
{
    private RequestStack $request;
    private ArticleRepository $articleRepository;

    public function __construct(RequestStack $request, ArticleRepository $articleRepository)
    {
        $this->request = $request;
        $this->articleRepository = $articleRepository;
    }

    public function showMorph($keyword, $param)
    {
        if ( $this->request->getCurrentRequest()->get('id') > 0 ) {

            return $this->articleRepository
                ->find($this->request->getCurrentRequest()->get('id'))
                ->getKeyword()[$param];
        }
        return $keyword;
    }
}
