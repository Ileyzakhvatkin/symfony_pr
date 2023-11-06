<?php

namespace App\Services;

use App\Repository\ArticleRepository;
use Carbon\Carbon;

class ArticleCreatePeriodControl
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function checkBlock($authUser, $licenseInfo): bool
    {
        $isBlocked = false;
        if ($licenseInfo['type'] == 'PRO') {
            $isBlocked = true;
        } else {
            $parameters = [
                'val' => $authUser->getId(),
                'date' => (new Carbon('-2 hours'))->toDateString(),
            ];
            if ($this->articleRepository->getArticleCountFromPeriod($parameters)[0]['1'] >= 2) {
                $isBlocked = true;
            }
        }

        return $isBlocked;
    }
}